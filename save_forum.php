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

if (!isset($_POST['forum_name'])) {
    echo "error2343";
   return;
}

$db = new dbase();
$db->connect_sqlite();

$sql = "INSERT INTO categories (cat_name) VALUES (:cat_name)";

$stmt =$db->getConnection()->prepare($sql);

$stmt->bindValue(':cat_name' , $_POST['forum_name']);

$stmt->execute();

if ($stmt->errorCode()!='00000')
{
    echo "couldnt insert the record to forums";
    return;
}
else {
    header('Location: .');
}