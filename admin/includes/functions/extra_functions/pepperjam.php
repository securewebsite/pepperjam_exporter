<?php
/**
 * pepperjam.php
 *
 * @package Pepperjam Exporter
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: pepperjam.php,v 1.0 17.07.2007 18:57 numinix $
 */

if (!function_exists('zen_cfg_pull_down_currencies')){
	function zen_cfg_pull_down_currencies($currencies_id, $key = '') {
		global $db;
		$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
		$currencies = $db->execute("select code from " . TABLE_CURRENCIES);
		$currencies_array = array();
		while (!$currencies->EOF) {
			$currencies_array[] = array('id' => $currencies->fields['code'],
																'text' => $currencies->fields['code']);
			$currencies->MoveNext();
		}
		return zen_draw_pull_down_menu($name, $currencies_array, $currencies_id);
	}
}

if (!function_exists('zen_cfg_pull_down_country_iso3_list')){
	function zen_cfg_pull_down_country_iso3_list($countries_id, $key = '') {
		global $db;
		$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
		$countries = $db->execute("select countries_id, countries_iso_code_3 from " . TABLE_COUNTRIES);
		$countries_array = array();
		while (!$countries->EOF) {
			$countries_array[] = array('id' => $countries->fields['countries_id'],
																'text' => $countries->fields['countries_iso_code_3']);
			$countries->MoveNext();
		}
		return zen_draw_pull_down_menu($name, $countries_array, $countries_id);
	}
} 

if (!function_exists('zen_cfg_pull_down_languages_list')){
	function zen_cfg_pull_down_languages_list($languages_id, $key = '') {
		global $db;
		$name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
		$languages = $db->execute("select code, name from " . TABLE_LANGUAGES);
		$languages_array = array();
		while (!$languages->EOF) {
			$languages_array[] = array('id' => $languages->fields['name'],
																'text' => $languages->fields['name']);
			$languages->MoveNext();
		}
		return zen_draw_pull_down_menu($name, $languages_array, $languages_id);
	}
} 
?>