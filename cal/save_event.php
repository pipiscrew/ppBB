<?php
    @session_start();

    require_once '../general.php';

    date_default_timezone_set("UTC");

    if (isset($_SESSION['login_expiration']) && $_SESSION["login_expiration"] != date("Y-m-d"))
    {	
        echo '>> login expired <<';

        if (isset($_POST['event_descr'])){
            echo $_POST['event_descr'];
        }

        return;
    }

    if (!isset($_SESSION["id"])) {
        echo '>> user is not logged in <<';

        if (isset($_POST['event_descr'])){
            echo $_POST['event_descr'];
        }

        return;	
    }



    if (!isset($_POST["dtp_start"]) || !isset($_POST["dtp_end"]) || !isset($_POST["event_type"]) || !isset($_POST["event_descr"])) {
        echo json_encode(3);
        exit;
    }

    
    $db = new dbase();
    $db->connect_sqlite();


    
    $update_id = 0;

    if (isset($_POST["rec_id"]))
        $update_id = $_POST["rec_id"];



    if ($update_id == 0) {
        $sql = "INSERT INTO events (date_start, date_end, event_type, event_description, date_rec) VALUES (:date_start, :date_end, :event_type, :event_description, :date_rec)";
        $stmt = $db->getConnection()->prepare($sql);
    }
    else {
        $sql = "UPDATE events set date_start=:date_start, date_end=:date_end, event_type=:event_type, event_description=:event_description, date_rec=:date_rec where event_id=:event_id";
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->bindValue(':event_id' , $update_id);
    }
        
    $stmt->bindValue(':date_start' , $_POST['dtp_start']);

    //if the start equals with end null it!
    if ($_POST['dtp_start']==$_POST['dtp_end'])
        $stmt->bindValue(':date_end' , null, PDO::PARAM_NULL);
    else 
        $stmt->bindValue(':date_end' , $_POST['dtp_end']);

    $stmt->bindValue(':event_type' , $_POST['event_type']);
    $stmt->bindValue(':event_description' , $_POST['event_descr']);
    $stmt->bindValue(':date_rec' , date("Y-m-d"));

    $stmt->execute();

    $res = $stmt->rowCount();

    if($res == 1)
        echo json_encode(100);
    else
        echo json_encode(0);
?>