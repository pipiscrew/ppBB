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


$db = new dbase();
$db->connect_sqlite();
	

if ( (!isset($_GET['topic_id'])) && (!isset($_GET['category_id']) && !isset($_GET['reply_id']))         )
	return;
else if (isset($_GET['category_id']))
	$category_id = intval($_GET['category_id']);  	//when adding new topic
else if (isset($_GET['topic_id'])){
	//when adding a new repy to topic
	$reply_topic_id = intval($_GET['topic_id']);
	$category_id =$db->getScalar("select category_id from topics where topic_id=?",array($reply_topic_id));
	$add_reply = true;
}
else 
{
	$reply_id = intval($_GET['reply_id']);			//when editing an existing topic
	
	
	//read reply record details
	$reply_row = $db->getRow("select reply_body, topic_id from replies where reply_id=?",array($reply_id));
	
	if ($reply_row){
		$reply_val = $reply_row['reply_body'];
		$reply_topic_id = $reply_row['topic_id'];
		
		//identify if is editing 'HEAD reply'
		$reply_min_id = $db->getScalar("select min(reply_id) from replies where topic_id=?",array($reply_topic_id));

		if ($reply_id==$reply_min_id) {
			//if edit the 'HEAD reply' parse the 'topic name''
			$topic_title = $db->getScalar("select topic_name from topics where topic_id=?",array($reply_topic_id));
		}
		
	} else {
		echo "couldnt find reply on database!";
		exit;
	}
	
}
	
require_once('page_top.php');

?>

<!-- jQuery 2.0.2 -->
<script src="assets/jquery.min.js" type="text/javascript"></script>

<!-- bootstrap http://getbootstrap.com -->
<script src="assets/bootstrap.min.js" type="text/javascript"></script>

<!-- include summernote -->
<link rel="stylesheet" href="assets/summernote.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="assets/summernote.min.js"></script>
  
  
<form id="new_FORM" role="form" method="post" action="save_topic.php">

	<button type='button' id="btn_doc_details_cancel" class="btn btn-default" onclick="abort_topic()">
		<span class="glyphicon glyphicon-chevron-left"></span> cancel
	</button>

	<button id="btn_doc_details_save" class="btn btn-default btn-danger" type="submit" name="submit">
		<span class="glyphicon glyphicon-floppy-disk"></span> save
	</button>

	<br>
	<br>

	<form role="form">

<?php if( (isset($category_id) || isset($topic_title)) && !isset($add_reply)) { ?>

		<label for="f_name">Topic :</label>
		<input name="topic_name" class="form-control" placeholder="Topic name" required value="<?= (isset($topic_title)) ? $topic_title : ''  ;?>"><br>

<?php } ?>

		<?php if (isset($reply_topic_id)) { ?>
		<input name="topic_id" style="display:none;" value='<?=$reply_topic_id;?>'>
		<?php } ?>
		
		<?php if (isset($category_id)) { ?>
		<input name="category_id" style="display:none;" value='<?=$category_id;?>'>
		<?php } ?>
		
		<?php if (isset($reply_id)) { ?>
		<input name="reply_id"  style="display:none;" value='<?= $reply_id;?>'>
		<?php } ?>

	
				<p class="container">
					<textarea class="input-block-level" id="summernote" name="summernote" rows="18">
<?php if (isset($reply_id)) echo $reply_val; ?> 
					</textarea>
				</p>
				

	  
	</form>

</form>
				
<script>
	$('#summernote').summernote({
			height:$(window).height()
		});
		
	function abort_topic(){
		var x = $('#summernote').val();
		
		if (x.length >0) {
			if (confirm('All data will be lost, are you sure ?')) {
			    window.history.go(-1);
			} else {
			   //do nothing
			}
		} else {
			//if is already empty
			 window.history.go(-1);
		}

		
	}



	
</script>
<?php

require_once('page_footer.php');

?>