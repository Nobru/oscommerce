<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_Content_Languages_definitions extends osC_Template {

/* Private variables */

    var $_module = 'languages_definitions',
        $_page_title,
        $_page_contents = 'languages_definitions.php';

/* Class constructor */

    function osC_Content_Languages_definitions() {
      $this->_page_title = HEADING_TITLE;

      if (!isset($_GET['action'])) {
        $_GET['action'] = '';
      }

      if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
          case 'lDefine':
            $this->_page_contents = 'languages_definitions_listing.php';
            break;

          case 'insert':
            $this->_insert();
            break;

          case 'save':
            $this->_save();
            break;

          case 'deleteconfirm':
            $this->_delete();
            break;
        }
      }
    }

/* Private methods */

    function _insert() {
      global $osC_Database, $osC_Language, $osC_MessageStack;

      $error = false;

      $osC_Database->startTransaction();

      foreach ($osC_Language->getAll() as $l) {
        $Qdefinition = $osC_Database->query('insert into :table_languages_definitions (languages_id, content_group, definition_key, definition_value) values (:languages_id, :content_group, :definition_key, :definition_value)');
        $Qdefinition->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdefinition->bindInt(':languages_id', $l['id']);
        $Qdefinition->bindValue(':content_group', (empty($_POST['group_new']) ? $_POST['group'] : $_POST['group_new']));
        $Qdefinition->bindValue(':definition_key', $_POST['key']);
        $Qdefinition->bindValue(':definition_value', $_POST['value'][$l['id']]);
        $Qdefinition->execute();

        if ($osC_Database->isError()) {
          $error = true;
          break;
        }
      }

      if ($error === false) {
        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');

        $osC_Database->commitTransaction();

        osC_Cache::clear('languages-' . $osC_Language->getCodeFromID($_GET['lID']) . '-' . $_POST['group']);
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');

        $osC_Database->rollbackTransaction();
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&lID=' . $_GET['lID'] . '&content=' . $_POST['content']));
    }

    function _save() {
      global $osC_Database, $osC_Language, $osC_MessageStack;

      $error = false;

      $osC_Database->startTransaction();

      foreach ($_POST['def'] as $key => $value) {
        $Qupdate = $osC_Database->query('update :table_languages_definitions set definition_value = :definition_value where definition_key = :definition_key and languages_id = :languages_id and content_group = :content_group');
        $Qupdate->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qupdate->bindValue(':definition_value', $value);
        $Qupdate->bindValue(':definition_key', $key);
        $Qupdate->bindInt(':languages_id', $_GET['lID']);
        $Qupdate->bindValue(':content_group', $_GET['group']);
        $Qupdate->execute();

        if ($osC_Database->isError()) {
          $error = true;
          break;
        }
      }

      if ($error === false) {
        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');

        $osC_Database->commitTransaction();

        osC_Cache::clear('languages-' . $osC_Language->getCodeFromID($_GET['lID']) . '-' . $_GET['group']);
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');

        $osC_Database->rollbackTransaction();
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&lID=' . $_GET['lID'] . '&content=' . $_GET['content']));
    }

    function _delete() {
      global $osC_Database, $osC_Language, $osC_MessageStack;

      $error = false;

      $osC_Database->startTransaction();

      foreach ($_POST['defs'] as $value) {
        $Qdel = $osC_Database->query('delete from :table_languages_definitions where id = :id');
        $Qdel->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
        $Qdel->bindValue(':id', $value);
        $Qdel->execute();

        if ($osC_Database->isError()) {
          $error = true;
          break;
        }
      }

      if ($error === false) {
        $osC_MessageStack->add_session($this->_module, SUCCESS_DB_ROWS_UPDATED, 'success');

        $osC_Database->commitTransaction();

        osC_Cache::clear('languages-' . $osC_Language->getCodeFromID($_GET['lID']) . '-' . $_GET['group']);
      } else {
        $osC_MessageStack->add_session($this->_module, ERROR_DB_ROWS_NOT_UPDATED, 'error');

        $osC_Database->rollbackTransaction();
      }

      osc_redirect(osc_href_link_admin(FILENAME_DEFAULT, $this->_module . '&lID=' . $_GET['lID']));
    }
  }
?>
