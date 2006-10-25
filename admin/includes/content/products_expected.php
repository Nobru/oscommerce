<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Products_expected extends osC_Template {

/* Private variables */

    var $_module = 'products_expected',
        $_page_title,
        $_page_contents = 'products_expected.php';

/* Class constructor */

    function osC_Content_Products_expected() {
      global $osC_Database;

      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
        $_GET['page'] = 1;
      }

      $Qcheck = $osC_Database->query('select products_id from :table_products where products_date_available is not null limit 1');
      $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
      $Qcheck->execute();

      if ($Qcheck->numberOfRows()) {
        $Qupdate = $osC_Database->query('update :table_products set products_date_available = null where unix_timestamp(now()) > unix_timestamp(products_date_available)');
        $Qupdate->bindTable(':table_products', TABLE_PRODUCTS);
        $Qupdate->execute();
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'save':
            $this->_save();
            break;
        }
      }
    }

/* Private methods */

    function _save() {
      global $osC_Database, $osC_MessageStack;

      if (isset($_GET['pID']) && is_numeric($_GET['pID'])) {
        $Qproduct = $osC_Database->query('update :table_products set products_date_available = :products_date_available, products_last_modified = now() where products_id = :products_id');
        $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
        if (date('Y-m-d') < $_POST['products_date_available']) {
          $Qproduct->bindValue(':products_date_available', $_POST['products_date_available']);
        } else {
          $Qproduct->bindRaw(':products_date_available', 'null');
        }
        $Qproduct->bindInt(':products_id', $_GET['pID']);
        $Qproduct->execute();

        if ($osC_Database->isError() === false) {
          $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');
        } else {
          $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');
        }
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&page=' . $_GET['page']));
    }
  }
?>
