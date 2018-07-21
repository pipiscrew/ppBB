<?php
@session_start();

if (!isset($_GET['id']))
	return;
else 
	$topic_id = intval($_GET['id']);
	
require_once('page_top.php');

$sql = <<<EOD
select replies.*,topics.topic_name,topics.category_id, categories.cat_private  from replies 
left join topics on topics.topic_id = replies.topic_id 
left join categories on categories.cat_id = topics.category_id 
where replies.topic_id = ? 
order by reply_id
EOD;

$rows = $db->getSet($sql, array($topic_id));



if (!$rows)
{
	echo ">> topic not found <<";
	return;
} else {
	$breadcrumb_info = breadcrumb($db, $rows[0]['category_id']);

	if (!$is_admin && $breadcrumb_info[1] == 1) {
		$rows = array();
	} else if ($is_admin || (!$is_admin && $breadcrumb_info[1] == 0)) {
		echo $breadcrumb_info[0];
	}

	//when no admin
	if (!$is_admin) {

		//if rows exist
		if ($rows) {
				//update topic views when user is not logged in
				$db->executeSQL("update topics set topic_views=topic_views+1 where topic_id = ?", array($topic_id)); //increase the topic views
		}
	}
}

if (!$rows) // dont let it pass!
{
	echo ">> topic not found <<";
	return;
}

?>
<div class="row" style="margin-left:0px;margin-bottom:20px">
	<!-- <a id="btn_new_topic" class="btn btn-default" href="list_topics.php?id=<?=$rows[0]['category_id'];?>">
		<span class="glyphicon glyphicon-chevron-left"></span> back
	</a> -->

	<?php if (isset($_SESSION["id"])) { ?>	
		<a id="btn_new_topic" class="btn btn-success" href="add_topic.php?topic_id=<?=$topic_id;?>">
			<span class="glyphicon glyphicon-edit"></span> reply
		</a>
	<?php } ?>
</div>
	

				<div class="media">

						<a class="media-left" href="#">
							<img src="assets/user_icon.png" class="img-polaroid" title="<?= $rows[0]['reply_dateupd']; ?>"/>
						</a>
					
					
					<div class="media-body">

						<h3 class="media-heading"><?= $rows[0]['topic_name']; ?></h4>


						<?= $rows[0]['reply_body']; ?>
						
					</div>
					
	<?php if ($is_admin) { ?>

					<a class="btn btn-default btn-primary btn-xs" style="margin-top: 15px" href="add_topic.php?reply_id=<?= $rows[0]['reply_id']; ?>">
						<span class="glyphicon glyphicon-edit"></span> edit topic
					</a>
					
					<a class="btn btn-default btn-danger btn-xs" style="margin-top: 15px" onclick="return confirm('Delete *topic* (including all replies), are you sure?')" href="delete_reply.php?reply_id=<?= $rows[0]['reply_id']; ?>">
						<span class="glyphicon glyphicon-remove"></span> delete topic
					</a>

	<?php } ?>
				</div>

				<hr>

				<div id="comments">
					<?php $skip_first=false; foreach($rows as $row) { 
						if (!$skip_first){
							$skip_first=true;
							continue;
						}
							
							?>

						<div class="media">

	
								<a class="media-left" href="#">
									<img src="assets/user_icon.png" class="img-polaroid" title="<?= $row['reply_dateupd']; ?>" />
								</a>

							
							<div class="media-body">
								<?= $row['reply_body']; ?>
							</div>
							
	<?php if ($is_admin) { ?>
							<a id="btn_new_topic" class="btn btn-default btn-primary btn-xs" style="margin-top: 15px" href="add_topic.php?reply_id=<?= $row['reply_id'];?>">
								<span class="glyphicon glyphicon-edit"></span> edit reply
							</a>

							<a id="btn_del_topic" class="btn btn-default btn-danger btn-xs" style="margin-top: 15px" onclick="return confirm('Delete reply, are you sure?')" href="delete_reply.php?reply_id=<?= $row['reply_id']; ?>">
								<span class="glyphicon glyphicon-remove"></span> delete reply
							</a>
	<?php } ?>
						</div>

						<hr>

					<?php } ?>
				</div>
				

<?php

require_once('page_footer.php');

?>