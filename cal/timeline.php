<?php

require_once '../general.php';

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


if  (!$is_admin) {
    echo '>> user is not logged in <<';
    return;
    exit;
}


$db = new dbase();
$db->connect_sqlite();

$start = '1979-01-01';
$end = '3000-01-01';

$rows = $db->getSet("select * from events where date_start between ? and ? order by date_start DESC",array($start, $end));
    


//https://stackoverflow.com/a/36525712
$year_head = <<<'EOD'
<li class="timeline-item period">
	<div class="timeline-info"></div>
	<div class="timeline-marker"></div>
	<div class="timeline-content">
		<h2 class="timeline-title">{year}</h2>
	</div>
</li>
EOD;

$event_row = <<<'EOD'
<li class="timeline-item">
    <div class="timeline-info"><span>{date}</span></div>
    <div class="timeline-marker"  data-id="{recid}"></div>
    <div class="timeline-content">
        <div class="row">
            <div class="col-sm-12">{comment}</div> 
        </div>
    </div>
</li>
EOD;

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato" /> <!-- https://stackoverflow.com/a/31172338 -->
	<link href="../assets/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="assets/timeline.css" rel="stylesheet" type="text/css" />
	<title>Life Timeline</title>
</head>

<body>
    <div class="container">
        <ul class="rawlinks timeline timeline-centered">
    
<?php
        $prev_year = '';
        foreach($rows as $event)
        {   
            //cut the year
            $event_year = substr($event['date_start'], 0,4);
            
            //prepare the date range string
            if ($event['date_end'] != null && $event['date_start'] != $event['date_end'])
                $event_range = $event['date_start'].' - '.$event['date_end'];
            else 
                $event_range = $event['date_start'];
            
            //if the current row year is diff from previous row year, add the year header!
            if ($prev_year != $event_year)
                echo str_replace('{year}', $event_year, $year_head);
            
            //replace new line with br
            $event['event_description'] = str_replace("\r\n",'<br>',$event['event_description']);
            
            echo str_replace(array('{date}', '{recid}', '{comment}'), array($event_range, $event['event_id'], $event['event_description']), $event_row);
            
            $prev_year = $event_year;
        }
?>
            <br>
        </ul> <!-- rawlinks -->
    </div> <!-- container -->
    
    
<script>
    
    Array.prototype.forEach.call(document.querySelectorAll('.timeline-marker'), function(el) {

        el.addEventListener('click', function(e) {
            e.preventDefault();
            
            var rec_id = e.srcElement.getAttribute('data-id');
            
            window.open("./?id=" + rec_id, '_blank');

        })

    })

</script>