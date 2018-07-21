<?php
@session_start();

require_once('general.php');

require_once('page_top.php'); 

if (!$is_admin) {
	echo '>> user is not logged in <<';
	return;	
}
?>


<form method="POST" action="">
    <div class="form-group" style="width:500px">
        <label for="search_txt">Search for </label>
        <input class="form-control" name="search_txt" placeholder="example str1, str2, str3" title="Greek characters are case and accent sensitive" value='<?php if (isset($_POST['search_txt'])) echo $_POST['search_txt'];?>'>
    </div>
    <button class="btn btn-success" type="submit">search</button> <br/>
</form><br/>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new dbase();
    $db->connect_sqlite();

    $sql = 'select topics.topic_id, reply_id, reply_dateupd, category_id, topic_name, reply_body from replies left join topics on replies.topic_id = topics.topic_id ';

	$search_arr = explode(',', $_POST['search_txt']);
	
	if (sizeof($search_arr) == 1) {
		$sql.= ' where reply_body like :searchTerm or topic_name like :searchTerm ';
	} else if (sizeof($search_arr) == 2) {
		$sql.= ' where (reply_body like :searchTerm or topic_name like :searchTerm) and (reply_body like :searchTerm2 or topic_name like :searchTerm2) ';
	} else if (sizeof($search_arr) == 3) {
		$sql.= ' where (reply_body like :searchTerm or topic_name like :searchTerm) and (reply_body like :searchTerm2 or topic_name like :searchTerm2) and (reply_body like :searchTerm3 or topic_name like :searchTerm3) ';
	}
    else 
        die ("error");


    //prepare
    $stmt = $db->getConnection()->prepare($sql);

    if (sizeof($search_arr)==1){
        $stmt->bindValue(':searchTerm', '%'.trim($search_arr[0]).'%');
    } else if (sizeof($search_arr) == 2) {
        $stmt->bindValue(':searchTerm', '%'.trim($search_arr[0]).'%');
        $stmt->bindValue(':searchTerm2', '%'.trim($search_arr[1]).'%');
    } else if (sizeof($search_arr) == 3) {
        $stmt->bindValue(':searchTerm', '%'.trim($search_arr[0]).'%');
        $stmt->bindValue(':searchTerm2', '%'.trim($search_arr[1]).'%');
        $stmt->bindValue(':searchTerm3', '%'.trim($search_arr[2]).'%');
    }

    //output
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<span class='label label-danger'>".sizeof($rows)." result(s)</span><br/><br/>";

    foreach($rows as $row) {
        echo breadcrumb($db, $row['category_id'], ' > <a href="view_topic.php?id='.$row['topic_id'].'">'.$row['topic_name'].'</a>')[0];
    }
}

?>

<?php

require_once('page_footer.php');

?>