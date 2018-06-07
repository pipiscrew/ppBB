<?php
@session_start();

if (!isset($_GET['id']))
	return;
else 
	$category_id = intval($_GET['id']);

require_once('page_top.php');

$breadcrumb_info = breadcrumb($db, $category_id);

if (!$breadcrumb_info)
{	
	echo '>> category not found <<';
	return;
}

//query for subforums
if ($is_admin) {
	$sql = "select * from categories where cat_parent_id = $category_id order by cat_order, cat_name";
}
else {
	$sql = "select * from categories where cat_private = 0 and cat_parent_id = $category_id order by cat_order, cat_name";
}

$cat_rows = $db->getSet($sql, null);
//query for subforums



//topics query
$sql = <<<EOD
select topics.*,replies.reply_dateupd as 'reply_daterec', count(replies.topic_id)-1 as 'replies' from topics 
left join replies on replies.topic_id = topics.topic_id 
where topics.category_id = ? 
group by topics.topic_name 
order by reply_daterec DESC,topics.topic_name
EOD;

$topic_rows = $db->getSet($sql, array($category_id));



if (!$is_admin && $breadcrumb_info[1] == 1) {
	$topic_rows = $cat_rows = array();
} else if ($is_admin || (!$is_admin && $breadcrumb_info[1] == 0)) {
	echo $breadcrumb_info[0];
}


?>



			<div id="subforums_panel" class="panel panel-default" style="margin-top:20px; <?= (sizeof($cat_rows) == 0) ? 'display:none;' : ''; ?> ">
				<div class="panel-heading">
					<h3 class="panel-title">Subforums</h3>
				</div>
				<div class="panel-body">

<?php require_once('forums_list.php'); ?>

				</div>
			</div>
			
<?php if ($is_admin) { ?>

	<button class="btn btn-default btn-success pull-right" onclick="add_new();">
		<span class="glyphicon glyphicon-plus"></span> new subforum
	</button>

	<a class="btn btn-default btn-primary" href="add_topic.php?category_id=<?=$category_id;?>">
		<span class="glyphicon glyphicon-plus"></span> new topic
	</a> 
	<br><br>

<?php } ?>
				<table class="table table-hover">
					<thead>
						<tr>
							<th>User</th>
							<th>Topic</th>
							<th>Started</th>
							<th>Last Activity</th>
							<th>Replies</th>
							<th>Views</th>
						</tr>
					</thead>
					<tbody>


					<?php foreach($topic_rows as $topic_row) { ?>
						<tr class="topics">
							<td>
								<img src="assets/user_icon.png" class="img-polaroid" />
							</td>
							<td>
								<a href="view_topic.php?id=<?= $topic_row['topic_id']; ?>"><?= $topic_row['topic_name']; ?></a><br>
							</td>
							<td>
								<?= $topic_row['topic_daterec']; ?>
							</td>
							<td>
								<?= $topic_row['reply_daterec']; ?>
							</td>
							<td>
								<?= $topic_row['replies']; ?>
							</td>
							<td>
								<?= $topic_row['topic_views']; ?>
							</td>
						</tr>
					<?php } ?>
					
					</tbody>
				</table>	

<?php

require_once('page_footer.php');

?>