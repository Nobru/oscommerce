<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $Qcurrencies = $osC_Database->query('select * from :table_currencies');
  $Qcurrencies->bindTable(':table_currencies', TABLE_CURRENCIES);
  $Qcurrencies->setCache('currencies');
  $Qcurrencies->execute();

  $currencies_array = array();

  while ($Qcurrencies->next()) {
    $currencies_array[] = array('id' => $Qcurrencies->valueInt('currencies_id'),
                                'text' => $Qcurrencies->value('title'));
  }

  $Qcurrencies->freeResult();

  $import_languages_array = array();

  $osC_DirectoryListing = new osC_DirectoryListing('../includes/languages');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $osC_DirectoryListing->setCheckExtension('xml');
  foreach ($osC_DirectoryListing->getFiles() as $file) {
    $import_languages_array[] = array('id' => substr($file['name'], 0, strrpos($file['name'], '.')), 'text' => substr($file['name'], 0, strrpos($file['name'], '.')));
  }
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_lDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_LANGUAGE_NAME; ?></th>
        <th><?php echo TABLE_HEADING_TOTAL_DEFINITIONS; ?></th>
        <th><?php echo TABLE_HEADING_LANGUAGE_CODE; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $Qlanguages = $osC_Database->query('select * from :table_languages order by sort_order, name');
  $Qlanguages->bindTable(':table_languages', TABLE_LANGUAGES);
  $Qlanguages->execute();

  while ($Qlanguages->next()) {
    $Qdef = $osC_Database->query('select count(*) as total_definitions from :table_languages_definitions where languages_id = :languages_id');
    $Qdef->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
    $Qdef->bindInt(':languages_id', $Qlanguages->valueInt('languages_id'));
    $Qdef->execute();

    if (!isset($lInfo) && (!isset($_GET['lID']) || (isset($_GET['lID']) && ($_GET['lID'] == $Qlanguages->valueInt('languages_id'))))) {
      $lInfo = new objectInfo(array_merge($Qlanguages->toArray(), $Qdef->toArray()));
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td>

<?php
    if ($Qlanguages->value('code') == DEFAULT_LANGUAGE) {
      echo '<b>' . $Qlanguages->value('name') . ' (' . TEXT_DEFAULT . ')</b>';
    } else {
      echo $Qlanguages->value('name');
    }
?>

        </td>
        <td><?php echo $Qdef->value('total_definitions'); ?></td>
        <td><?php echo $Qlanguages->value('code'); ?></td>
        <td align="right">

<?php
    echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, 'languages_definitions&lID=' . $Qlanguages->valueInt('languages_id')), osc_icon('edit.png', IMAGE_EDIT_DEFINITIONS)) . '&nbsp;';

    if (isset($lInfo) && ($Qlanguages->valueInt('languages_id') == $lInfo->languages_id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_CONFIGURE), 'onclick="toggleInfoBox(\'lEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('export.png', IMAGE_EXPORT), 'onclick="toggleInfoBox(\'lExport\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'lDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $Qlanguages->valueInt('languages_id') . '&action=lEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $Qlanguages->valueInt('languages_id') . '&action=lExport'), osc_icon('export.png', IMAGE_EXPORT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $Qlanguages->valueInt('languages_id') . '&action=lDelete'), osc_icon('trash.png', IMAGE_DELETE));
    }
?>

        </td>
      </tr>

<?php
  }
?>

    </tbody>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_IMPORT . '" onclick="toggleInfoBox(\'lImport\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_lImport" <?php if ($_GET['action'] != 'lImport') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_IMPORT_LANGUAGE; ?></div>
  <div class="infoBoxContent">
    <form name="lImport" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=import'); ?>" method="post">

    <p><?php echo TEXT_INFO_IMPORT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_SELECT_LANGUAGE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('language_import', $import_languages_array, null, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_SELECT_IMPORT_TYPE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_radio_field('import_type', array(array('id' => 'add', 'text' => 'Only Add New Records'), array('id' => 'update', 'text' => 'Only Update Existing Records'), array('id' => 'replace', 'text' => 'Replace Completely')), null, null, '<br />'); ?></td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_IMPORT . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($lInfo)) {
?>

<div id="infoBox_lEdit" <?php if ($_GET['action'] != 'lEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $lInfo->name; ?></div>
  <div class="infoBoxContent">
    <form name="lEdit" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $lInfo->languages_id . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_INFO_EDIT_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_NAME . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('name', $lInfo->name, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_CODE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('code', $lInfo->code, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_LOCALE . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('locale', $lInfo->locale, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_CHARSET . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('charset', $lInfo->charset, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_TEXT_DIRECTION . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('text_direction', array(array('id' => 'ltr', 'text' => 'ltr'), array('id' => 'rtl', 'text' => 'rtl')), $lInfo->text_direction, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DATE_FORMAT_SHORT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('date_format_short', $lInfo->date_format_short, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DATE_FORMAT_LONG . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('date_format_long', $lInfo->date_format_long, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_TIME_FORMAT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('time_format', $lInfo->time_format, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFAULT_CURRENCY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_pull_down_menu('currencies_id', $currencies_array, $lInfo->currencies_id, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_NUMERIC_SEPARATOR_DECIMAL . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('numeric_separator_decimal', $lInfo->numeric_separator_decimal, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_NUMERIC_SEPARATOR_THOUSANDS . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('numeric_separator_thousands', $lInfo->numeric_separator_thousands, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_SORT_ORDER . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('sort_order', $lInfo->sort_order, 'style="width: 100%"'); ?></td>
      </tr>

<?php
    if (DEFAULT_LANGUAGE != $lInfo->code) {
?>

      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
      </tr>

<?php
    }
?>

    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_lDelete" <?php if ($_GET['action'] != 'lDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $lInfo->name; ?></div>
  <div class="infoBoxContent">

<?php
    if (DEFAULT_LANGUAGE == $lInfo->code) {
?>

    <p><?php echo '<b>' . TEXT_INFO_DELETE_PROHIBITED . '</b>'; ?></p>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

<?php
    } else {
?>

    <p><?php echo TEXT_INFO_DELETE_INTRO; ?></p>

    <p><?php echo '<b>' . $lInfo->name . '</b>'; ?></p>

    <p align="center"><?php echo '<input type="button" value="' . IMAGE_DELETE . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $lInfo->languages_id . '&action=deleteconfirm') . '\';" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

<?php
    }
?>

  </div>
</div>

<div id="infoBox_lExport" <?php if ($_GET['action'] != 'lExport') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('export.png', IMAGE_EXPORT) . ' ' . $lInfo->name; ?></div>
  <div class="infoBoxContent">
    <form name="lExport" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $lInfo->languages_id . '&action=export'); ?>" method="post">

    <p><?php echo TEXT_INFO_EXPORT_INTRO; ?></p>

<?php
    $Qgroups = $osC_Database->query('select distinct content_group from :table_languages_definitions where languages_id = :languages_id order by content_group');
    $Qgroups->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
    $Qgroups->bindInt(':languages_id', $lInfo->languages_id);
    $Qgroups->execute();

    $groups_array = array();

    while ($Qgroups->next()) {
      $groups_array[] = array('id' => $Qgroups->value('content_group'), 'text' => $Qgroups->value('content_group'));
    }
?>

    <p>(<a href="javascript:selectAllFromPullDownMenu('groups');"><u>select all</u></a> | <a href="javascript:resetPullDownMenuSelection('groups');"><u>select none</u></a>)<br /><?php echo osc_draw_pull_down_menu('groups[]', $groups_array, array('account', 'checkout', 'general', 'index', 'info', 'order', 'products', 'search'), 'id="groups" size="10" multiple="multiple" style="width: 100%;"'); ?></p>

    <p><?php echo osc_draw_checkbox_field('include_data', array(array('id' => '', 'text' => TEXT_INFO_EXPORT_WITH_DATA)), true); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_EXPORT . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="resetPullDownMenuSelection(\'groups\'); toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
