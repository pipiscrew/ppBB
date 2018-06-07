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


if (isset($_POST['forum_id'])) {
    $category_id = intval($_POST['forum_id']);

    if ($category_id == 0)  {
        unset($category_id);
    }

}


$db = new dbase();
$db->connect_sqlite();

if (isset($category_id)) {

    // UPDATE

    $sql = 'update categories set cat_name = :cat_name, cat_private = :cat_private, cat_order = :cat_order where cat_id = :cat_id';

    $stmt = $db->getConnection()->prepare($sql);

    $stmt->bindValue(':cat_id' , $category_id);
}
else 
{   // INSERT

    $sql = 'INSERT INTO categories (cat_name, cat_parent_id, cat_private, cat_order) VALUES (:cat_name, :cat_parent_id, :cat_private, :cat_order)';

    $stmt = $db->getConnection()->prepare($sql);

    $stmt->bindValue(':cat_parent_id' , $_POST['parent_forum_id']);
}


$stmt->bindValue(':cat_name' , $_POST['forum_name']);
$stmt->bindValue(':cat_private' , $_POST['forum_private']);
$stmt->bindValue(':cat_order' , $_POST['forum_order']);

$stmt->execute();


//if ($stmt->errorCode()!='00000')
if ( $stmt->rowCount() != 1 )
{
    echo 'couldnt insert the record to forums';
    return;

} else {

    if (isset($category_id))        // UPDATE
        echo json_encode(array('status' => '1'));
    else {
        $new_rec_id = $db->getConnection()->lastInsertId();

        echo json_encode(array('status' => '1', 'rec_id' => $new_rec_id));
    }
}