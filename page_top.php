<?php
/* used by 
	add_topic
	index
	list_topics
	view_topic
*/

@session_start();

date_default_timezone_set("UTC");

if (isset($_SESSION['login_expiration']) && $_SESSION["login_expiration"] != date("Y-m-d"))
{	
	session_destroy();
}

if (isset($_SESSION["id"])) {
	$is_admin = true;
}
else {
	$is_admin = false;
}

require_once('general.php');

$db = new dbase();
$db->connect_sqlite();

function breadcrumb($db, $category_id){
//returns an array [0] breadcrumb [1] is_private

	$is_private = 0;

	$id = $category_id;

	$breadcrumb = array();
	
	while ($id != 0){	
		
		$row = $db->getRow("select cat_id, cat_parent_id, cat_name, cat_private from categories where cat_id = $id", null);

		if (!$row) {
			return false;
		}

		$breadcrumb[] = array($row['cat_id'], $row['cat_name']);

		$id = $row['cat_parent_id'];

		if (($row['cat_private']) > $is_private)
			$is_private = ($row['cat_private']);
	}

	$breadcrumb = array_reverse($breadcrumb);

	$output = '<a href="."><span class="glyphicon glyphicon glyphicon-home"></span></a>';
	foreach($breadcrumb as $crumb) {

		if ($category_id == $crumb[0])
			$output .= " > <span class='btn btn-success btn-xs'>$crumb[1]</span>";//$output .= " > <a class='btn btn-success btn-xs' href='list_topics.php?id=$crumb[0]'>$crumb[1]</a>";
		else 
		 	$output .= " > <a href='list_topics.php?id=$crumb[0]'>$crumb[1]</a>";
	}

	return array('<div class="row" style="margin-left:0px;margin-bottom:20px">' . $output . '</div>', $is_private);// '<br><br>';
}
?>

<!DOCTYPE html>
<html>
	<head>
		<!--http://glyphicons.bootstrapcheatsheets.com/-->
		<meta charset="UTF-8">
		<title>Forums</title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        
		<!-- bootstrap -->
		<link href="assets/bootstrap.min.css" rel="stylesheet" type="text/css" />

<?php if (isset($_SESSION["id"])) { ?>
		<!-- rmodal -->
		<style>
		.modal {
			display: none;
			background: rgba(0, 0, 0, .30);
			z-index: 999;
		}
		
		.modal-body {
			position: relative;
			padding: 30px;
		}
		
		.modal .modal-dialog {
			position: relative;
			margin: 30px auto;
			width: 500px;
			border-radius: 6px;
			-webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
					box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
		}
		</style>
<?php } ?>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<nav>
					<ul class="nav nav-pills pull-right">
						<li><a href="https://pipiscrew.com">Home</a></li>
<?php if (!isset($_SESSION["id"])) { ?>
						<li><a href="login.php" id="login_button">Login</a></li>
<?php } else  { ?>
						<li><a href="login.php?logout=1" id="login_button">Logout</a></li>
<?php } ?>
					</ul>
				</nav>
				<h3 class="text-muted"><a href=".">ppBB</a></h3>
			</div>

			<div class="marketing">			
				<div class="jumbotron">
					<h1>It's all just talk</h1>
					<!-- <p class="lead">This is a demonstration install of the <a href="https://github.com/Xeoncross/forumfive">ForumFive</a> PHP forum system. Please be respectful.</p> -->
				</div>

