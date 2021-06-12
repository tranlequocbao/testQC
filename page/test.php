<?php
require_once "../vendor/PDOconnect/pdo/PdoConnect.php";
$db = new pdoRequest();
$db->changeAdapter('sqlsrv');
$db->setTable('T001_BODY_DATA');
echo '<pre>';
print_r($db->all());
echo '</pre>';