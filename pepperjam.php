<?php
/**
 * pepperjam.php
 *
 * @package Pepperjam Exporter
 * @copyright Copyright 2007-2008 Numinix http://www.numinix.com
 * @copyright Portions Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: pepperjam.php, v 1.09a 04.23.2008 22:40:59 numinix $
 * @author numinix
 */
	@define('PEPPERJAM_VERSION', '1.09a 04.23.2008 22:40:59');
 /*
 * update notes
 * 1. Added Per Category Shipping;
 * 2. Bug fix;
 * 3. Added include/exclude categories by id;
 */
	require('includes/application_top.php');
	
	@define('PEPPERJAM_OUTPUT_BUFFER_MAXSIZE', 1024*1024);
	@define('PEPPERJAM_CHECK_IMAGE', 'false');
	@define('PEPPERJAM_STAT', false);
	$anti_timeout_counter = 0; //for timeout issues as well as counting number of products processed
	$max_limit = false;
	$today = date("Y-m-d");
	@define('PEPPERJAM_USE_CPATH', 'false');
	@define('NL', "<br />\n");
  
  if (PEPPERJAM_MAGIC_SEO_URLS == 'true') {
    require_once(DIR_WS_CLASSES . 'msu_ao.php');
    include(DIR_WS_INCLUDES . 'modules/msu_ao_1.php');
  }

	require(zen_get_file_directory(DIR_WS_LANGUAGES . $_SESSION['language'] .'/', 'pepperjam.php', 'false'));

	$languages = $db->execute("select code, languages_id from " . TABLE_LANGUAGES . " where name='" . PEPPERJAM_LANGUAGE . "' limit 1");

	$product_url_add = (PEPPERJAM_LANGUAGE_DISPLAY == 'true' ? "&language=" . $languages->fields['code'] : '') . (PEPPERJAM_CURRENCY_DISPLAY == 'true' ? "&currency=" . PEPPERJAM_CURRENCY : '');

	echo TEXT_PEPPERJAM_STARTED . NL;
	echo TEXT_PEPPERJAM_FILE_LOCATION . DIR_FS_CATALOG . PEPPERJAM_DIRECTORY . PEPPERJAM_OUTPUT_FILENAME . NL;
	echo "Processing: Feed - " . (isset($_GET['feed']) && $_GET['feed'] == "yes" ? "Yes" : "No") . NL;

if (isset($_GET['feed']) && $_GET['feed'] == "yes") {
	if (is_dir(DIR_FS_CATALOG . PEPPERJAM_DIRECTORY)) {
		if (!is_writeable(DIR_FS_CATALOG . PEPPERJAM_DIRECTORY)) {
			echo ERROR_PEPPERJAM_DIRECTORY_NOT_WRITEABLE . NL;
			die;
		}
	} else {
		echo ERROR_PEPPERJAM_DIRECTORY_DOES_NOT_EXIST . NL;
		die;
	}

	$stimer_feed = microtime_float();
	if (!get_cfg_var('safe_mode') && function_exists('safe_mode')) {
		set_time_limit(0);
	}

	$output_buffer = "";
	
	$outfile = DIR_FS_CATALOG . PEPPERJAM_DIRECTORY . PEPPERJAM_OUTPUT_FILENAME;
  
  if (file_exists($outfile)) {
    chmod($outfile, 0777);
  }
  
  if(!zen_pepperjam_fwrite('', 'w')) {
    echo ERROR_PEPPERJAM_OPEN_FILE . NL;
    die;
  }

	$output = array();
	$output["sku"] = "sku";
	$output["name"] = "name";
	$output["buy_url"] = "buy_url";
	$output["image_url"] = "image_url";
	$output["price"] = "price";
	$output["manufacturer"] = "manufacturer";
	$output["category_program"] = "category_program";
  $output["keywords"] = "keywords";
  $output["quantity_in_stock"] = "quantity_in_stock";
  $output["mpn"] = "mpn";
  $output["description_long"] = "description_long";	
	
	zen_pepperjam_fwrite($output);
	
	$categories_array = zen_pepperjam_category_tree();
	$products_query = "SELECT distinct(p.products_id), p.products_model, pd.products_name, pd.products_description, p.products_image, p.products_tax_class_id, p.products_price_sorter, s.specials_new_products_price, s.expires_date, GREATEST(p.products_date_added, IFNULL(p.products_last_modified, 0), IFNULL(p.products_date_available, 0)) AS base_date, m.manufacturers_name, p.products_quantity, pt.type_handler, mtpd.metatags_keywords
									 FROM " . TABLE_PRODUCTS . " p
										 LEFT JOIN " . TABLE_MANUFACTURERS . " m ON (p.manufacturers_id = m.manufacturers_id)
										 LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON (p.products_id = pd.products_id)
										 LEFT JOIN " . TABLE_PRODUCT_TYPES . " pt ON (p.products_type=pt.type_id)
										 LEFT JOIN " . TABLE_SPECIALS . " s ON (s.products_id = p.products_id)
                     LEFT JOIN " . TABLE_META_TAGS_PRODUCTS_DESCRIPTION . " mtpd ON (mtpd.products_id = p.products_id)
									 WHERE p.products_status = 1
										 AND p.product_is_call = 0
										 AND p.product_is_free = 0
										 AND pd.language_id = " . (int)$languages->fields['languages_id'] ."
                   GROUP BY p.products_id 
									 ORDER BY p.products_id ASC";
	$products = $db->Execute($products_query);
	$tax_rate = array();
	$starting_point = PEPPERJAM_START_PRODUCTS;
		while (!$products->EOF && !$max_limit) { // run until end of file or until maximum number of products reached
			list($categories_list, $cPath) = zen_pepperjam_get_category($products->fields['products_id']);
			$pepperjam_start_counter++;
			if (numinix_categories_check(PEPPERJAM_POS_CATEGORIES, $products->fields['products_id'], 1) == true && numinix_categories_check(PEPPERJAM_NEG_CATEGORIES, $products->fields['products_id'], 2) == false && ($pepperjam_start_counter >= PEPPERJAM_START_PRODUCTS)) { // check to see if category limits are set.  If so, only process for those categories.
        if ($anti_timeout_counter == PEPPERJAM_MAX_PRODUCTS && PEPPERJAM_MAX_PRODUCTS != 0) { // if counter is greater than or equal to maximum products
					$max_limit = true; // then max products reached
				} else {
					$max_limit = false; // otherwise, max products not reached
				}
				$price = zen_get_products_actual_price($products->fields['products_id']);
				if ($price > 0) {
					$anti_timeout_counter++;
					if (!isset($tax_rate[$products->fields['products_tax_class_id']]))
						$tax_rate[$products->fields['products_tax_class_id']] = zen_get_tax_rate($products->fields['products_tax_class_id']);
					$price = zen_add_tax($price, $tax_rate[$products->fields['products_tax_class_id']]);
					$price = $currencies->value($price, true, 'USD', $currencies->get_value('USD'));
					
          $href = ($products->fields['type_handler'] ? $products->fields['type_handler'] : 'product') . '_info';
					$cPath_href = (PEPPERJAM_USE_CPATH == 'true' ? 'cPath=' . $cPath . '&' : '');
					
          $bread_crumbs = zen_pepperjam_get_category($products->fields['products_id']);
          array_pop($bread_crumbs);
          $bread_crumbs = implode(" > ", $bread_crumbs);
          $bread_crumbs = htmlentities($bread_crumbs);
          
          $output = array();
					$output["sku"] = $products->fields['products_id'];
					$output["name"] = ShortenText(utf8_encode($products->fields['products_name']), 128);
          $output["category_program"] = ShortenText($bread_crumbs, 256);
					$output["description_long"] = ShortenText(trim(preg_replace('/\s+/', ' ', strip_tags(nl2br(utf8_encode($products->fields['products_description']))))), 2000);
					if (PEPPERJAM_QUANTITY) {
						$output["quantity_in_stock"] = $products->fields['products_quantity'];
					}				
					$output["image_url"] = zen_pepperjam_image_url($products->fields['products_image']);
          $output["keywords"] = ShortenText(str_replace(array(',', ', '), ' ', $products->fields['metatags_keywords']), 256);
          // BEGIN MAGIC SEO URLS
          if (PEPPERJAM_MAGIC_SEO_URLS == 'true') {
            include(DIR_WS_INCLUDES . 'modules/msu_ao_2.php');
          // END MAGIC SEO URLS
          } else {
            $link = ($products->fields['type_handler'] ? $products->fields['type_handler'] : 'product') . '_info';
            $cPath_href = (PEPPERJAM_USE_CPATH == 'true' ? 'cPath=' . $cPath . '&' : '');
            $link = zen_href_link($link, $cPath_href . 'products_id=' . (int)$products->fields['products_id'] . $product_url_add, 'NONSSL', false);
          }
          $link = html_entity_decode($link);
          $output["buy_url"] = $link;
          $output["manufacturer"] = ShortenText(utf8_encode($products->fields['manufacturers_name']), 128);
          $output["mpn"] = ShortenText(utf8_encode($products->fields['products_model']), 128); 
					zen_pepperjam_fwrite($output);
				}
			}
			$products->MoveNext();
		}
    zen_pepperjam_fwrite();
    chmod($outfile, 0655); 

	$timer_feed = microtime_float()-$stimer_feed;
	
	echo TEXT_PEPPERJAM_FEED_COMPLETE . ' ' . PEPPERJAM_TIME_TAKEN . ' ' . sprintf("%f " . TEXT_PEPPERJAM_FEED_SECONDS, number_format($timer_feed, 6) ) . ' ' . $anti_timeout_counter . TEXT_PEPPERJAM_FEED_RECORDS . NL;	
}

  function br2nl($string){
    $return=eregi_replace('<br[[:space:]]*/?'.
      '[[:space:]]*>',chr(13).chr(10),$string);
    return $return;
  } 
  
  function percategory_shipping($products_id, $countries_iso_code_2 = 'US') {
    global $percategory;
    
    $products_array = array();
    $products_array[0]['id'] = $products_id;
    $countries_id = numinix_get_countries_id($countries_iso_code_2);
    $shipping = $percategory->calculation($products_array, numinix_get_dest_zone($countries_id), (int)MODULE_SHIPPING_PERCATEGORY_GROUPS);
    return $shipping;
  }
  
  function numinix_get_countries_id($countries_iso_code_2) {
    global $db;
    $countries_query = "SELECT * FROM " . TABLE_COUNTRIES . "
                        WHERE countries_iso_code_2 = '" . $countries_iso_code_2 . "'
                        LIMIT 1";
    $countries = $db->Execute($countries_query);
    return $countries->fields['countries_id'];
  }
  
  // shortens a string of text to the a specified length, full words only
  function ShortenText($text, $chars) {        // Change to the number of characters you want to display                
    $text = $text." ";        
    $text = substr($text,0,$chars);        
    $text = substr($text,0,strrpos($text,' '));      
    return $text;    
  }
    
  function numinix_get_dest_zone($countries_id) {
    global $db;
    $geozones = $db->Execute("SELECT * FROM " . TABLE_GEO_ZONES); 
    $num_zones = $geozones->RecordCount();
    $dest_zone = 0;
    for ($i=1; $i<=$num_zones; $i++) {
      if ((int)constant('MODULE_SHIPPING_PERCATEGORY_ZONE_' . $i) > 0) {
        $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . constant('MODULE_SHIPPING_PERCATEGORY_ZONE_' . $i) . "' and zone_country_id = '" . $countries_id . "' order by zone_id");
        while (!$check->EOF) {
          if ($check->fields['zone_id'] < 1) {
            $dest_zone = $i;
            break;
          } elseif ($check->fields['zone_id'] == $countries_id) {
            $dest_zone = $i;
            break;
          }
          $check->MoveNext();
        } // end while
      } // END if ((int)constant('MODULE_SHIPPING_PERCATEGORY_ZONE_' . $i) > 0)
    } // END for ($i=1; $i<=$this->num_zones; $i++)
    return $dest_zone;
  }  

	function zen_pepperjam_fwrite($output='', $mode='wb') {
    global $outfile;
		static $fp = false;
		static $output_buffer = "";
		static $title_row = false;
		if($output == '') {
			if(!$fp) {
				$retval = $fp = fopen($outfile, $mode);
			} else {
				if(strlen($output_buffer) > 0) {
					$retval = fwrite($fp, $output_buffer, strlen($output_buffer));
					$output_buffer = "";
				}
				fclose($fp);
			}
		} else {
			if(!$title_row) {
				$title_row = $output;
			}
			$buf = array();
			foreach($title_row as $key=>$val) {
				$buf[] = (isset($output[$key]) ? $output[$key] : '');
			}
			$output = implode("\t", $buf);
			if(strlen($output_buffer) > PEPPERJAM_OUTPUT_BUFFER_MAXSIZE) {
				$retval = fwrite($fp, $output_buffer, strlen($output_buffer));
				$output_buffer = "";
			}
			$output = rtrim($output) . "\n";
			$output_buffer .= $output;
		}
		return $retval;
	}
	
	function trim_array($x) {
   		if (is_array($x)) {
       		return array_map('trim_array', $x);
   		} else {
   			return trim($x);
		}
	}
	
  function numinix_categories_check($categories_list, $products_id, $charge) {
    $categories_array = split(',', $categories_list);
    if ($categories_list == '') {
      if ($charge == 1) {
        return true;
      } elseif ($charge == 2) {
        return false;
      }
    } else {
      $match = false;
      foreach($categories_array as $category_id) {
        if (zen_product_in_category($products_id, $category_id)) {
          $match = true;
          break;
        }
      }
      if ($match == true) {
        return true;
      } else {
        return false;
      }
    }
  }

	function zen_pepperjam_get_category($products_id) {
		global $categories_array, $db;
		static $p2c;
		if(!$p2c) {
			$q = $db->Execute("SELECT *
												FROM " . TABLE_PRODUCTS_TO_CATEGORIES);
			while (!$q->EOF) {
				if(!isset($p2c[(int)$q->fields['products_id']]))
					(int)$p2c[$q->fields['products_id']] = (int)$q->fields['categories_id'];
				$q->MoveNext();
			}
		}
		if(isset($p2c[$products_id])) {
			$retval = $categories_array[$p2c[$products_id]]['name'];
			$cPath = $categories_array[$p2c[$products_id]]['cPath'];
		} else {
			$cPath = $retval =  "";
		}
		return array($retval, $cPath);
	}

	function zen_pepperjam_category_tree($id_parent=0, $cPath='', $cName='', $cats=array()){
		global $db, $languages;
		$cat = $db->Execute("SELECT c.categories_id, c.parent_id, cd.categories_name
												 FROM " . TABLE_CATEGORIES . " c
													 LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd on c.categories_id = cd.categories_id
												 WHERE c.parent_id = '" . (int)$id_parent . "'
												 AND cd.language_id='" . (int)$languages->fields['languages_id'] . "'
												 AND c.categories_status= '1'",
												 '', false, 150);
		while (!$cat->EOF) {
			$cats[$cat->fields['categories_id']]['name'] = (zen_not_null($cName) ? $cName . ', ' : '') . trim($cat->fields['categories_name']); // previously used zen_pepperjam_sanita instead of trim
			$cats[$cat->fields['categories_id']]['cPath'] = (zen_not_null($cPath) ? $cPath . '_' : '') . (int)$cat->fields['categories_id'];
			if (zen_has_category_subcategories($cat->fields['categories_id'])) {
				$cats = zen_pepperjam_category_tree((int)$cat->fields['categories_id'], $cats[(int)$cat->fields['categories_id']]['cPath'], $cats[(int)$cat->fields['categories_id']]['name'], $cats);
			}
			$cat->MoveNext();
		}
		return $cats;
	}

	function zen_pepperjam_sanita($str, $rt=false) { // currently using zen_pepperjam_cleaner below instead of zen_pepperjam_sanita
		$str = strip_tags($str);
		$str = str_replace(array("\t" , "\n", "\r"), ' ', $str);
		$str = preg_replace('/\s\s+/', ' ', $str);
//	$str = str_replace(array("&reg;", "®", "&copy;", "©", "&trade;", "™"), ' ', $str);
		$str = htmlentities(html_entity_decode($str));
		$in = $out = array();
		$in[] = "&reg;"; $out[] = '(r)';
		$in[] = "&copy;"; $out[] = '(c)';
		$in[] = "&trade;"; $out[] = '(tm)';
//		$str = str_replace($in, $out, $str);
		if($rt) {
			$str = str_replace(" ", "&nbsp;", $str);
			$str = str_replace("&nbsp;", "", $str);
		}
		$str = trim($str);
		return $str;
	}
		
	function zen_pepperjam_cleaner ($str) {
		$str = html_entity_decode($str);
		$_strip_search = array("![\t ]+$|^[\t ]+!m",'%[\r\n]+%m'); // remove CRs and newlines
		$_strip_replace = array('',' ');
		$_cleaner_array = array(">" => "> ", "&reg;" => "", "®" => "", "&trade;" => "", "™" => "", "\t" => "", "    " => "");
		$str = strtr($str, $_cleaner_array);
		$str = strip_tags($str);
		$str = strip_tags($str);
		$str = preg_replace($_strip_search, $_strip_replace, $str);
		return $str;
	}

	function zen_pepperjam_image_url($products_image) {
		if($products_image == "") return "";

		$products_image_extention = substr($products_image, strrpos($products_image, '.'));
		$products_image_base = ereg_replace($products_image_extention, '', $products_image);
		$products_image_medium = $products_image_base . IMAGE_SUFFIX_MEDIUM . $products_image_extention;
		$products_image_large = $products_image_base . IMAGE_SUFFIX_LARGE . $products_image_extention;

		// check for a medium image else use small
		if (!file_exists(DIR_WS_IMAGES . 'medium/' . $products_image_medium)) {
		  $products_image_medium = DIR_WS_IMAGES . $products_image;
		} else {
		  $products_image_medium = DIR_WS_IMAGES . 'medium/' . $products_image_medium;
		}
		// check for a large image else use medium else use small
		if (!file_exists(DIR_WS_IMAGES . 'large/' . $products_image_large)) {
		  if (!file_exists(DIR_WS_IMAGES . 'medium/' . $products_image_medium)) {
		    $products_image_large = DIR_WS_IMAGES . $products_image;
		  } else {
		    $products_image_large = DIR_WS_IMAGES . 'medium/' . $products_image_medium;
		  }
		} else {
		  $products_image_large = DIR_WS_IMAGES . 'large/' . $products_image_large;
		}
		if (function_exists('handle_image')) {
			$image_ih = handle_image($products_image_large, '', MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT, ''); // medium should be enough
			$retval = (HTTP_SERVER . DIR_WS_CATALOG . $image_ih[0]);
		} else {
			$retval = (HTTP_SERVER . DIR_WS_CATALOG . $products_image_large); // medium should be enough
		}
		return $retval;
	}

	function zen_pepperjam_expiration_date($base_date) {
		if(PEPPERJAM_EXPIRATION_BASE == 'now')
			$expiration_date = time();
		else
			$expiration_date = strtotime($base_date);
		$expiration_date += PEPPERJAM_EXPIRATION_DAYS*24*60*60;
		$retval = (date('Y-m-d', $expiration_date));
		return $retval;
	}

function microtime_float() {
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
?>