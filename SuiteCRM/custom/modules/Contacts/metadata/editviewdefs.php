<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

// Adds a 'preferred_name' field to the Contacts edit view first panel
$viewdefs['Contacts']['EditView']['panels']['default'][] = [
    ['name' => 'preferred_name', 'label' => 'LBL_PREFERRED_NAME'],
];
