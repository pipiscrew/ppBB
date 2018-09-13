<?php
@session_start();

date_default_timezone_set('UTC');

if (isset($_SESSION['login_expiration']) && $_SESSION['login_expiration'] != date('Y-m-d'))
{	
	echo '>> login expired <<';
	return;
}

if (!isset($_SESSION['id'])) {
	echo '>> user is not logged in <<';
	return;	
}
// VALIDATION^

if (!isset($_GET['topic_id']) || !isset($_GET['new_cat_id'])) {
   echo 'error2343';
   return;
}

$topic_id = intval($_GET['topic_id']);
$new_cat_id = intval($_GET['new_cat_id']);

if ($topic_id == 0 || $new_cat_id ==0) {
   echo 'error345';
   return;
}

require_once('general.php');

$db = new dbase();
$db->connect_sqlite();

$res = $db->executeSQL('update topics set category_id=? where topic_id=?', array($new_cat_id, $topic_id));

if ($res)
    header("Location: view_topic.php?id={$topic_id}");
else 
    echo "No SQL transaction done.";