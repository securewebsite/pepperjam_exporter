<?php
/**
 * pepperjam.php
 *
 * @package Pepperjam Exporter
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: pepperjam.php, v1.0 17.07.2007 18:58 numinix $
 */

define('HEADING_TITLE', 'Pepperjam Exporter');
define('TEXT_PEPPERJAM_OVERVIEW_HEAD', '<p><strong>OVERVIEW:</strong></p>');
define('TEXT_PEPPERJAM_OVERVIEW_TEXT', '<p>This module automatically generates a Pepperjam bulk file from your Zen Cart store.</p>');
define('TEXT_PEPPERJAM_INSTRUCTIONS_HEAD', '<p><strong>INSTRUCTIONS: </strong></p>');
define('TEXT_PEPPERJAM_INSTRUCTIONS_STEP1', '<p><strong><font color="#FF0000">STEP 1:</font></strong> Click <a href=%s><strong>[HERE]</strong></a> to create / update your product feed. </p>');
define('TEXT_PEPPERJAM_INSTRUCTIONS_STEP1_NOTE', '<p>NOTE: You may <a href="' . HTTP_SERVER . DIR_WS_CATALOG . PEPPERJAM_DIRECTORY . PEPPERJAM_OUTPUT_FILENAME . '" target="_blank" class="splitPageLink"><strong>view</strong></a> your product feed before uploading to pepperjam.com. </p>');
//eof