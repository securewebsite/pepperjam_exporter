<?php
/**
 * epier.php
 *
 * @package Pepperjam Exporter
 * @copyright Copyright 2007 Numinix Technology http://www.numinix.com
 * @copyright Portions Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: pepperjam.php, v1.0 18.07.2007 01:11 numinix $
 */

require('includes/application_top.php');
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
</head>
<body onload="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><img src="images/pepperjam.jpg" width="208" height="54"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
      	<td width="100%" valign="top">
          <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="main">
            <tr>
              <td width="78%" align="left" valign="top">
<?php 
echo TEXT_PEPPERJAM_OVERVIEW_HEAD; 
echo TEXT_PEPPERJAM_OVERVIEW_TEXT; 
echo TEXT_PEPPERJAM_INSTRUCTIONS_HEAD; 
printf(TEXT_PEPPERJAM_INSTRUCTIONS_STEP1, "\"javascript:(void 0)\" class=\"splitPageLink\" onClick=\"window.open('" . HTTP_SERVER . DIR_WS_CATALOG . FILENAME_PEPPERJAM . ".php?feed=yes', 'pepperjamfeed', 'resizable=1, statusbar=5, width=600, height=400, top=0, left=50, scrollbars=yes')\""); 
echo TEXT_PEPPERJAM_INSTRUCTIONS_STEP1_NOTE;
?>
              	<div id="ePierFeed" style="display: block; margin: 5px; width:96%; float: left; background-color:#CCCCCC;"></div>
<?php 
?>
            </tr>
          </table>
        </td>
      </tr>
<!-- body_text_eof //-->
    </table>
    </td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>