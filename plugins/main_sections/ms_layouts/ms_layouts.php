<?php
/*
 * Copyright 2005-2022 OCSInventory-NG/OCSInventory-ocsreports contributors.
 * See the Contributors file for more details about them.
 *
 * This file is part of OCSInventory-NG/OCSInventory-ocsreports.
 *
 * OCSInventory-NG/OCSInventory-ocsreports is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 2 of the License,
 * or (at your option) any later version.
 *
 * OCSInventory-NG/OCSInventory-ocsreports is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OCSInventory-NG/OCSInventory-ocsreports. if not, write to the
 * Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */

if (AJAX) {
    parse_str($protectedPost['ocs']['0'], $params);
    $protectedPost += $params;
    ob_start();
}

printEnTete($l->g(9907));

$tab_options = $protectedPost;
$form_name = "layouts";
$tab_options['form_name'] = $form_name;
$tab_options['table_name'] = $form_name;

$layout = new Layout($protectedGet['value']);
//ADD new layout
if (isset($protectedPost['Valid_modif']) && (!empty($_SESSION['OCS']['layout_visib']))){
    $dupli = $layout->insertLayout($protectedPost['LAYOUT_NAME'], $protectedPost['LAYOUT_DESCR'], $_SESSION['OCS']['loggeduser'], $_SESSION['OCS']['layout_visib']);
    // if dupli, user needs to be redirected to the form and not to the list
    if (!empty($dupli)) {
        unset($protectedPost['Valid_modif']);
    }
}

//delete layout
if (isset($protectedPost['SUP_PROF']) && $protectedPost['SUP_PROF'] != '') {
    $layout->deleteLayout($protectedPost['SUP_PROF']);
    $tab_options['CACHE'] = 'RESET';
}

if (is_defined($protectedPost['del_check'])) {
    $layout->deleteLayout($protectedPost['del_check']);
    $tab_options['CACHE'] = 'RESET';
}

// if no layout has been added yet and user did not delete layout : show the form 
if ((isset($protectedGet['tab']) && $protectedGet['tab'] == 'add') && (!isset($protectedPost['Valid_modif'])) && (!isset($protectedPost['SUP_PROF']) && !isset($protectedPost['del_check'])) && !isset($protectedPost['show_list'])) {
    echo open_form($form_name, '', '', 'form-horizontal');
    ?>
        <div class="col-md-12">
            <?php
            formGroup('text', 'LAYOUT_NAME', $l->g(9911).' :', '', '', $protectedPost['LAYOUT_NAME'] ?? '');
            formGroup('text', 'LAYOUT_DESCR', $l->g(9912).' :', '', '', $protectedPost['LAYOUT_DESCR'] ?? '');
            ?>
        </div>
    <div class="row">
        <div class="col-md-12">
            <input type="submit" name="Valid_modif" value="<?php echo $l->g(1363) ?>" class="btn btn-success">
            <input type="submit" name="show_list" value="<?php echo $l->g(9908) ?>" class="btn btn-info">
        </div>
    </div>
    <?php
    echo close_form();

// show the table
} else {
    echo open_form($form_name, '', '', 'form-horizontal');
    
    $list_fields = array(
        $l->g(9911) => 'LAYOUT_NAME',
        $l->g(243) => 'USER',
        $l->g(9913) => 'TABLE_NAME',
        $l->g(53) => 'DESCRIPTION',
        'SUP' => 'SUP',
        'CHECK' => 'CHECK'
    );
    
    $list_col_cant_del = $list_fields;
    
    $list_fields['SUP'] = 'ID';
    $list_fields['CHECK'] = 'ID';
    $tab_options['LBL_POPUP']['SUP'] = 'LAYOUT_NAME';
    
    $default_fields = $list_col_cant_del;
    $queryDetails = "SELECT ID, LAYOUT_NAME, USER, TABLE_NAME, DESCRIPTION FROM `layouts` WHERE USER = '".$_SESSION['OCS']['loggeduser']."'";

    ajaxtab_entete_fixe($list_fields, $default_fields, $tab_options, $list_col_cant_del);
    del_selection($form_name);
    echo close_form();

}


if (AJAX) {
    ob_end_clean();
    tab_req($list_fields, $default_fields, $list_col_cant_del, $queryDetails, $tab_options);
}