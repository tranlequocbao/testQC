<?php
    require_once "./vendor/PDOconnect/pdo/PdoConnect.php";
    $db = new pdoRequest();

    echo $db->exportDb("db/quoc_bao.sql");