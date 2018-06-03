<?php
@session_start();

date_default_timezone_set("UTC");

if (isset($_SESSION['login_expiration']) && $_SESSION["login_expiration"] != date("Y-m-d"))
{	
	echo '>> login expired <<';

	if (isset($_POST['summernote'])){
		echo $_POST['summernote'];
	}

	return;
}

if (!isset($_SESSION["id"])) {
	echo '>> user is not logged in <<';

	if (isset($_POST['summernote'])){
		echo $_POST['summernote'];
	}

	return;	
}
// VALIDATION^

/*https://gist.github.com/soomtong/6635053

https://github.com/summernote/summernote/issues/1415

https://summernote.org/deep-dive/#callbacks

https://stackoverflow.com/questions/34170950/summernote-inserthtml

https://github.com/summernote/summernote/issues/1407*/

require_once('general.php');

 if (!isset($_POST['category_id']) && !isset($_POST['reply_id'])) {
 		echo "error2343";
		return;
 }

else if (!isset($_POST['summernote'])){
	echo "error010101010";
	return;
}

$now = date('Y-m-d');


$db = new dbase();
$db->connect_sqlite();

if(isset($_POST['category_id']) && !empty($_POST['category_id']))
{
	if (!isset($_POST['topic_id'])) {

		/* COMING FROM ADD NEW TOPIC */
		$sql = "INSERT INTO topics (category_id, topic_name, topic_views, topic_daterec) VALUES (:category_id, :topic_name, :topic_views, :topic_daterec)";
		
		$stmt =$db->getConnection()->prepare($sql);
		
		$stmt->bindValue(':category_id' , $_POST['category_id']);
		$stmt->bindValue(':topic_name' , $_POST['topic_name']);
		$stmt->bindValue(':topic_views' , 0);
		$stmt->bindValue(':topic_daterec' , $now);
		
		$stmt->execute();
		
	    if ($stmt->errorCode()!='00000')
	    {
	    	echo "couldnt add record to categories";
	    	return;
		}
		else {
			//get the id of the last inserted record + used for redirect as well
			$topic_id = $db->getConnection()->lastInsertId();
		}
	}
	else {
		$topic_id = $_POST['topic_id'];
	}
		/////////// ADD AS REPLY
		$sql = "INSERT INTO replies (topic_id, reply_body, reply_dateupd, reply_user_id) VALUES (:topic_id, :reply_body, :reply_dateupd, :reply_user_id)";

		$stmt = $db->getConnection()->prepare($sql);

		$stmt->bindValue(':topic_id' , $topic_id);


}
else if(isset($_POST['reply_id']) && !empty($_POST['reply_id'])) 
{
	/* WHEN EDITING A REPLY HEAD OR NORMAL */
	
	// update topic title when editing the 'HEAD reply'
	if (isset($_POST['topic_name']) && isset($_POST['topic_id'])) {
		
		//used for redirect as well
		$topic_id = $_POST['topic_id'];
		
		$sql = "update topics set topic_name = :topic_name where topic_id = :topic_id";
		
		$stmt =$db->getConnection()->prepare($sql);
		
		$stmt->bindValue(':topic_name' , $_POST['topic_name']);
		$stmt->bindValue(':topic_id' , $topic_id);
		
		$stmt->execute();
		
	    if ($stmt->errorCode()!='00000')
	    {
	    	echo "couldnt update record to topics";
	    	return;
		}
	} else {
		//when editing a non 'HEAD reply' we didnt pass the topic_id from frontend so we have to dig here
		$topic_id = $db->getScalar('select topic_id from replies where reply_id=?',array($_POST['reply_id']));
	}
	
	
	/* COMING FROM EDIT TOPIC */
	$sql = "UPDATE replies set reply_body = :reply_body, reply_dateupd = :reply_dateupd, reply_user_id = :reply_user_id where reply_id=:reply_id";
	$stmt = $db->getConnection()->prepare($sql);
	$stmt->bindValue(':reply_id' , $_POST['reply_id']);
	
}


	$stmt->bindValue(':reply_body' , $_POST['summernote']);
	$stmt->bindValue(':reply_dateupd' , $now);
	$stmt->bindValue(':reply_user_id' , 1);

$stmt->execute();


if ($stmt->errorCode()!='00000')
{
	echo "couldnt save the record";
	return;
}
else {
	
	if (isset($topic_id)) //always on insert or update we have it, added as validation		
	{
		/* UPDATE CATEGORY LAST ACTIVITY */
		$x = $db->executeSQL("update categories set cat_last_update = ? where cat_id = (select cat_id from topics where topic_id = ?)", array($now, $topic_id));

		header('Location: view_topic.php?id=' . $topic_id);
	}
	else 
		die("record updated succesfully couldnt find the topic_id");
}



?>