<?php

if (!isset($_GET['id']))
	return;
else 
	$category_id = intval($_GET['id']);

require_once('page_top.php');


require_once('general.php');
$db = new dbase();
$db->connect_sqlite();

$sql = <<<EOD
select topics.*,replies.reply_dateupd as 'reply_daterec', count(replies.topic_id)-1 as 'replies' from topics 
left join replies on replies.topic_id = topics.topic_id 
where topics.category_id = ? 
group by topics.topic_name 
order by reply_daterec,topics.topic_name
EOD;

$rows = $db->getSet($sql, array($category_id));

?>

	<a class="btn btn-default" href=".">
		<span class="glyphicon glyphicon-chevron-left"></span> back2forums
	</a>

	<?php if (isset($_SESSION["id"])) { ?>

		<a class="btn btn-default btn-primary" href="add_topic.php?category_id=<?=$category_id;?>">
			<span class="glyphicon glyphicon-plus"></span> new topic
		</a>

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


					<?php foreach($rows as $row) { ?>
						<tr class="topics">
							<td>
								<img src="assets/user_icon.png" class="img-polaroid" />
							</td>
							<td>
								<a href="view_topic.php?id=<?= $row['topic_id']; ?>"><?= $row['topic_name']; ?></a><br>
							</td>
							<td>
								<?= $row['topic_daterec']; ?>
							</td>
							<td>
								<?= $row['reply_daterec']; ?>
							</td>
							<td>
								<?= $row['replies']; ?>
							</td>
							<td>
								<?= $row['topic_views']; ?>
							</td>
						</tr>
					<?php } ?>
					
					</tbody>
				</table>	

<?php

require_once('page_footer.php');

?>