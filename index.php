<?php

require_once('page_top.php');

if ($is_admin) {
	$sql = 'select * from categories where cat_parent_id = 0 order by cat_order, cat_name';
}
else {
	$sql = 'select * from categories where cat_parent_id = 0 and cat_private = 0 order by cat_order, cat_name';
}

$cat_rows = $db->getSet($sql, null);

?>


<?php if ($is_admin) { ?>
<div class="row" style="margin-left:0px;margin-bottom:20px">
	<button class="btn btn-default btn-primary" onclick="add_new();">
		<span class="glyphicon glyphicon-plus"></span> new forum
	</button>
</div>

<?php } ?>


<?php require_once('forums_list.php'); ?>


<?php 
require_once('page_footer.php');
?>