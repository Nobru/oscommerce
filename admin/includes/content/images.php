<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Images extends osC_Template {

/* Private variables */

    var $_module = 'images',
        $_page_title,
        $_page_contents = 'images.php';

/* Class constructor */

    function osC_Content_Images() {
      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['module'])) {
        $_GET['module'] = '';
      }

      if (!empty($_GET['module']) && !file_exists('includes/modules/image/' . $_GET['module'] . '.php')) {
        $_GET['module'] = '';
      }

      if (empty($_GET['module'])) {
        $this->_page_contents = 'images_listing.php';
      }

      include('includes/classes/image.php');
    }
  }
?>