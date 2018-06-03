<?php

require_once('page_top.php');


require_once('general.php');
$db = new dbase();
$db->connect_sqlite();
$rows = $db->getSet('select * from categories order by cat_order', null);

?>
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Type</th>
							<th>Forum</th>
							<th>Last Activity</th>
						</tr>
					</thead>
					<tbody>


					<?php foreach($rows as $row) { ?>
						<tr class="topics">
							<td>
								<img src="assets/cat_icon.png" class="img-polaroid" />
							</td>
							<td>
								<a href="list_topics.php?id=<?= $row['cat_id']; ?>"><?= $row['cat_name']; ?></a><br>
							</td>
							<td>
								<?= $row['cat_last_update']; ?>
							</td>
						</tr>
					<?php } ?>
					
					</tbody>
				</table>

<?php if (isset($_SESSION["id"])) { ?>
<br>
<hr>
<br >	

<div class="container">
	<form method="post" action="save_forum.php">
		<div class="row">
			<div class="col-md-6">
				<label for="forum_name">Create a new forum :</label>
				<input type="text" placeholder="Enter forum name.." name="forum_name" class="form-control">
			</div>


			<div class="col-md-6" style="margin-top:25px;width:300px;">
					<input type="submit" name="Submit" value="add forum" class="btn btn-default btn-success btn-block">
			</div>	
		</div>	
	</form>

</div>

<?php
}

require_once('page_footer.php');

?>