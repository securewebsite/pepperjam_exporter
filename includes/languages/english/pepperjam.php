<?php
/**
 * epier.php
 *
 * @package Pepperjam Exporter
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: pepperjam.php, v1.09 04.19.2008 14:56:59 numinix $
 */
 
define('TEXT_PEPPERJAM_STARTED', 'Pepperjam Exporter v.' . PEPPERJAM_VERSION . ' started ' . date("Y/m/d H:i:s"));
define('TEXT_PEPPERJAM_FILE_LOCATION', 'Feed file - ');
define('TEXT_PEPPERJAM_FEED_COMPLETE', 'Pepperjam File Complete');
define('TEXT_PEPPERJAM_FEED_TIMER', 'Time:');
define('TEXT_PEPPERJAM_FEED_SECONDS', 'Seconds');
define('TEXT_PEPPERJAM_FEED_RECORDS', ' Records');
define('PEPPERJAM_TIME_TAKEN', 'In');
define('PEPPERJAM_VIEW_FILE', 'View File:');
define('ERROR_PEPPERJAM_DIRECTORY_NOT_WRITEABLE', 'Your Pepperjam folder is not writeable! Please chmod the /' . PEPPERJAM_DIRECTORY . ' folder to 755 or 777 depending on your host.');
define('ERROR_PEPPERJAM_DIRECTORY_DOES_NOT_EXIST', 'Your Pepperjam output directory does not exist! Please create an /' . PEPPERJAM_DIRECTORY . ' directory and chmod to 755 or 777 depending on your host.');
define('ERROR_PEPPERJAM_OPEN_FILE', 'Error opening Pepperjam output file "' . DIR_FS_CATALOG . PEPPERJAM_DIRECTORY . PEPPERJAM_OUTPUT_FILENAME . '"');
define('TEXT_PEPPERJAM_ERRSETUP', 'Pepperjam error setup:');
define('TEXT_PEPPERJAM_ERRSETUP_L', 'Pepperjam Feed Language "%s" not defined in zen-cart store.');
define('TEXT_PEPPERJAM_ERRSETUP_C', 'Pepperjam Default Currency "%s" not defined in zen-cart store.');
//eof