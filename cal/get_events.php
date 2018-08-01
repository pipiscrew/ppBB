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

    if (!isset($_GET["start"]) || !isset($_GET["end"]) || !isset($_GET["_"])) {
        echo json_encode(3);
        exit;
    } 
	

    $db = new dbase();
    $db->connect_sqlite();
	
	$rows = $db->getSet("select event_id, date_start, date_end, event_type, event_description, date_rec from events where date_start between ? and ?",array($_GET["start"], $_GET["end"]));
     
	
	//create an array
	$record = array();
	
	//for each record
	foreach($rows as $row) {
		$datetime = new DateTime($row['date_start']);
	 
		$event_type = $row['event_type'];
		
		//give to calendar bar the proper color
		switch ($event_type) {
			case 1 :
				$color = "#9B26AF";
				break;
			case 2 :
				$color = "#2095F2";
				break;
			case 3 :
				$color = "#009587";
				break;
			case 4 :
				$color = "#FE5621";
				break;
			case 5 :
				$color = "#5CB85C";
				break;
			case 6 :
				$color = "#8C72CB";
				break;
			case 7 :
				$color = "#785447";
				break;
			case 8 :
				$color = "#5F7C8A";
				break;
			case 9 :
				$color = "#212121";
				break;
			case 10 :
				$color = "#FF0000";
				break;
			 default:
				$color = "#212121";
			}
		
        //https://fullcalendar.io/docs/agenda/allDaySlot/
        //https://fullcalendar.io/docs/event_data/Event_Object/
		//convert mysql datetime to ISO8601 format FullCalendar compatible
		$record[] = array("id" => $row['event_id'],"title" => substr($row['event_description'], 0, 30) ,"color" => $color, "allDay" => true, "start" => $datetime->format(DateTime::ISO8601));
		//add it to array
	}

	echo json_encode($record);
?>