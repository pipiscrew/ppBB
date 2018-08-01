<?php
    @session_start();

    require_once '../general.php';

    date_default_timezone_set("UTC");

    if (isset($_SESSION['login_expiration']) && $_SESSION["login_expiration"] != date("Y-m-d"))
    {	
        echo '>> login expired <<';
        return;
    }

    if (!isset($_SESSION["id"])) {
        echo '>> user is not logged in <<';
        return;	
    }
    // VALIDATION^

    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if(!$isAjax) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Python - SyntaxError: invalid syntax, visit pipiscrew.com', true, 500);
        exit;
    }

    if (!isset($_POST["id"])){
        echo json_encode(3);
        exit;
    } 

    $rec_id = $_POST["id"];


    $db = new dbase();
    $db->connect_sqlite();
	
	$res = $db->executeSQL("delete from events where event_id=?", array($rec_id));

    echo json_encode($res);
?>