<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_aDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_ADMINISTRATORS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $Qadmin = $osC_Database->query('select id, user_name from :table_administrators order by user_name');
  $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
  $Qadmin->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qadmin->execute();

  while ($Qadmin->next()) {
    if (!isset($aInfo) && (!isset($_GET['aID']) || (isset($_GET['aID']) && ($_GET['aID'] == $Qadmin->value('id')))) && ($_GET['action'] != 'aNew')) {
      $aInfo = new objectInfo($Qadmin->toArray());
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo $Qadmin->value('user_name'); ?></td>
        <td align="right">

<?php
    if (isset($aInfo) && ($Qadmin->valueInt('id') == $aInfo->id)) {
      echo osc_link_object('#', osc_icon('configure.png', IMAGE_EDIT), 'onclick="toggleInfoBox(\'aEdit\');"') . '&nbsp;' .
           osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'aDelete\');"');
    } else {
      echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&aID=' . $Qadmin->valueInt('id') . '&action=aEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&aID=' . $Qadmin->valueInt('id') . '&action=aDelete'), osc_icon('trash.png', IMAGE_DELETE));
    }
?>

        </td>
      </tr>

<?php
  }
?>

    </tbody>
  </table>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td><?php echo $Qadmin->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ADMINISTRATORS); ?></td>
      <td align="right"><?php echo $Qadmin->displayBatchLinksPullDown('page', $osC_Template->getModule()); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'aNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_aNew" <?php if ($_GET['action'] != 'aNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_HEADING_NEW_ADMINISTRATOR; ?></div>
  <div class="infoBoxContent">
    <form name="mNew" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_NEW_INTRO; ?></p>

    <p><?php echo '<b>' . TEXT_ADMINISTRATOR_USERNAME . '</b><br />' . osc_draw_input_field('user_name', null, 'style="width: 100%;"'); ?></p>
    <p><?php echo '<b>' . TEXT_ADMINISTRATOR_PASSWORD . '</b><br />' . osc_draw_password_field('user_password', 'style="width: 100%;"'); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'aDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($aInfo)) {
?>

<div id="infoBox_aEdit" <?php if ($_GET['action'] != 'aEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $aInfo->user_name; ?></div>
  <div class="infoBoxContent">
    <form name="aEdit" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&aID=' . $aInfo->id . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_EDIT_INTRO; ?></p>

    <p><?php echo '<b>' . TEXT_ADMINISTRATOR_USERNAME . '</b><br />' . osc_draw_input_field('user_name', $aInfo->user_name, 'style="width: 100%;"'); ?></p>
    <p><?php echo '<b>' . TEXT_ADMINISTRATOR_PASSWORD . '</b><br />' . osc_draw_password_field('user_password', 'style="width: 100%;"'); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'aDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_aDelete" <?php if ($_GET['action'] != 'aDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $aInfo->user_name; ?></div>
  <div class="infoBoxContent">
    <form name="aDelete" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&aID=' . $aInfo->id . '&action=deleteconfirm'); ?>" method="post">

    <p><?php echo TEXT_DELETE_INTRO; ?></p>

    <p><?php echo '<b>' . $aInfo->user_name . '</b>'; ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'aDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
