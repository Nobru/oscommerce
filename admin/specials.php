<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require('includes/classes/tax.php');
  $osC_Tax = new osC_Tax_Admin();

  $action = (isset($_GET['action']) ? $_GET['action'] : '');

  if (!isset($_GET['page']) || (isset($_GET['page']) && !is_numeric($_GET['page']))) {
    $_GET['page'] = 1;
  }

  if (!empty($action)) {
    switch ($action) {
      case 'save':
        $error = false;
        $Qproduct = $osC_Database->query('select products_price from :table_products where products_id = :products_id');
        $Qproduct->bindTable(':table_products', TABLE_PRODUCTS);
        $Qproduct->bindInt(':products_id', $_POST['products_id']);
        $Qproduct->execute();

        if ($Qproduct->numberOfRows() === 1) {
          $specials_price = $_POST['specials_price'];

          if (substr($specials_price, -1) == '%') {
            $specials_price = $Qproduct->valueDecimal('products_price') - (((double)$specials_price / 100) * $Qproduct->valueDecimal('products_price'));
          }

          if ( ($specials_price < '0.00') || ($specials_price >= $Qproduct->valueDecimal('products_price')) ) {
            $error = true;
            $osC_MessageStack->add_session('header', ERROR_SPECIALS_PRICE, 'error');
          }

          if ($_POST['specials_expires_date'] < $_POST['specials_start_date']) {
            $error = true;
            $osC_MessageStack->add_session('header', ERROR_SPECIALS_DATE, 'error');
          }
          
          if ($error == false) {
          	if (isset($_GET['sID']) && is_numeric($_GET['sID'])) {
              $Qspecial = $osC_Database->query('update :table_specials set specials_new_products_price = :specials_new_products_price, specials_last_modified = now(), expires_date = :expires_date, start_date = :start_date, status = :status where specials_id = :specials_id');
              $Qspecial->bindInt(':specials_id', $_GET['sID']);
            } else {
              $Qspecial = $osC_Database->query('insert into :table_specials (products_id, specials_new_products_price, specials_date_added, expires_date, start_date, status) values (:products_id, :specials_new_products_price, now(), :expires_date, :start_date, :status)');
              $Qspecial->bindInt(':products_id', $_POST['products_id']);
            }
            $Qspecial->bindTable(':table_specials', TABLE_SPECIALS);
            $Qspecial->bindValue(':specials_new_products_price', $specials_price);
            $Qspecial->bindValue(':expires_date', $_POST['specials_expires_date']);
            $Qspecial->bindValue(':start_date', $_POST['specials_start_date']);
            $Qspecial->bindInt(':status', (isset($_POST['specials_status']) && ($_POST['specials_status'] == '1') ? '1' : '0'));
            $Qspecial->execute();

            if ($osC_Database->isError() === false) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
            } else {
            $osC_MessageStack->add_session('header', ERROR_DB_ROWS_NOT_UPDATED, 'error');
            }
          }          	
        }
        osc_redirect(osc_href_link_admin(FILENAME_SPECIALS, 'page=' . $_GET['page'] . (isset($_GET['sID']) ? '&sID=' . $_GET['sID'] : '')));
        break;
      case 'deleteconfirm':
        if (isset($_GET['sID']) && is_numeric($_GET['sID'])) {
          $Qspecial = $osC_Database->query('delete from :table_specials where specials_id = :specials_id');
          $Qspecial->bindTable(':table_specials', TABLE_SPECIALS);
          $Qspecial->bindInt(':specials_id', $_GET['sID']);
          $Qspecial->execute();

          if ($Qspecial->affectedRows()) {
            $osC_MessageStack->add_session('header', SUCCESS_DB_ROWS_UPDATED, 'success');
          } else {
            $osC_MessageStack->add_session('header', WARNING_DB_ROWS_NOT_UPDATED, 'warning');
          }
        }

        osc_redirect(osc_href_link_admin(FILENAME_SPECIALS, 'page=' . $_GET['page']));
        break;
    }
  }

  require('../includes/classes/currencies.php');
  $osC_Currencies = new osC_Currencies();

  switch ($action) {
    case 'sNew':
    case 'sEdit': $page_contents = 'specials_edit.php'; break;
    default: $page_contents = 'specials.php';
  }

  require('templates/default.php');

  require('includes/application_bottom.php');
?>
