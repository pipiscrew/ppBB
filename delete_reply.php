<?php
@session_start();

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

require_once('general.php');

 if (!isset($_GET['reply_id']) ) {
 		echo "error2343";
		return;
 }
 
$reply_id = intval($_GET['reply_id']);
 
 if (!$reply_id){
 		echo "error45654";
		return;
 }
 	
 
$db = new dbase();
$db->connect_sqlite();

$reply_row = $db->getRow("select topic_id from replies where reply_id=?",array($reply_id));

if (!$reply_row){	
 		echo "error986";
		return;
 }
 
$reply_topic_id = $reply_row['topic_id'];

//identify if is editing 'HEAD reply'
$reply_min_id = $db->getScalar("select min(reply_id) from replies where topic_id=?",array($reply_topic_id));


if ($reply_id==$reply_min_id) {
	//have to del all replies from dbase + topic
	$rows = $db->getSet("select reply_id from replies where topic_id = ?"  , array($reply_topic_id));
	
	$rec_ids = '0';
	foreach($rows as $row) {
		$rec_ids .= ',' . $row['reply_id'] ;
	}
	
	$db->executeSQL("delete from replies where reply_id in ($rec_ids)", null); //remove replies
	
	$cat_id = $db->getScalar("select category_id from topics where topic_id=?",array($reply_topic_id)); //grab the id for redirect
	
	$db->executeSQL("delete from topics where topic_id = ?", array($reply_topic_id)); //remove the topic itself
	
	
	
} else {
	$db->executeSQL("delete from replies where reply_id=?", array($reply_id));
}


if (isset($cat_id)	)
	header('Location: list_topics.php?id=' . $cat_id);
else
	header('Location: view_topic.php?id=' . $reply_topic_id);