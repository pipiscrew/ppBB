<?php
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


//auto shows the edit modal, when id passed through URL
if ($is_admin && isset($_GET['id']))
    $edit_rec = $_GET['id'];
    
?>

<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">



<script src="../assets/jquery.min.js"></script>
<script src="assets/moment.min.js"></script>
<script src="assets/fullcalendar.min.js"></script> <!--    https://fullcalendar.io/docs/event_ui/Requirements/ -->
<script src="../assets/bootstrap.min.js"></script>
<script src="assets/bootstrap-datepicker.min.js"></script>
    
     

<link rel="stylesheet" href="../assets/bootstrap.min.css"> <!-- needed by dtp-->
    
<link rel="stylesheet" href="assets/cal_style.css">
    
<link rel="stylesheet" href="assets/fullcalendar.min.css">
    
<link rel="stylesheet" href="assets/bootstrap-datepicker3.min.css">



<script>


    /////////////////////////////////////////////////// [ jQuery starts ] ///////////////////////////////////////////////////
    
    $(function() {
        
        ////////////////////////////////////////
        // MODAL FUNCTIONALITIES [START]
        //when modal closed, hide the warning messages + reset
        $('#modalEvent').on('hidden.bs.modal', function() {
            //when close - clear elements
            $('#formEvent').trigger("reset");
        });

        //functionality when the modal already shown and its long, when reloaded scroll to top
        $('#modalEvent').on('shown.bs.modal', function() {
            $(this).animate({
                scrollTop : 0
            }, 'slow');
        });
        // MODAL FUNCTIONALITIES [END]
        ////////////////////////////////////////
        
        
        //datepicker
        $('#dtp_start, #dtp_end').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
        
        $('#dtp_goto').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            startView: 1,
            minViewMode: 1
            
        });

       
        
        //form submit button
        $('#formEvent').submit(function(e) {
            e.preventDefault();

            if (!validate_modal())
                return;
            
            var postData = $(this).serializeArray();
            var formURL = $(this).attr("action");
            
            $.ajax(
            {
                url : formURL,
                type: "POST",
                data : postData,
                dataType : "json",
                success:function(data, textStatus, jqXHR)
                {

                    console.log(data);
                    
                    if (data==100){
                        //refetch daybars from source
                        $('#calendar').fullCalendar('refetchEvents');
                        $('#modalEvent').modal('toggle');
                    }
                    else
                        alert("ERROR");
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    alert("ERROR - connection error");
                }
            });
        });

        cal_init();
        

        
<?php if(isset($edit_rec)) { ?>
        //if ID passed through URL, show edit modal
        get_rec_details("<?=$edit_rec?>");
<?php } ?>
        
    }); 
    
    /////////////////////////////////////////////////// [ jQuery ends ] ///////////////////////////////////////////////////

    function cal_init() {
        
        var calendar =  $('#calendar').fullCalendar({
            customButtons: {
                btnGotoDate: {
                  text: 'goto',
                  click: function() {
                      $('#modalGotoDate').modal('toggle');
                  }
                }
            },
            header: {
                left: 'prev,next today btnGotoDate',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
<?php if($is_admin) { ?>
            events: { //A URL of a JSON feed that the calendar will fetch Event Objects - https://fullcalendar.io/docs/events-json-feed 
                url: 'get_events.php',
                error: function() {
                    alert("ERROR - Reading the dbase.");
                }
<?php } ?>
            },
            eventLimit: false, //limits the number of events displayed on a day. The rest will show up in a popover.
            weekends : true,
            editable: false, //if the events can be dragged and resized
            firstDay: 1, //  1(Monday) this can be changed to 0(Sunday) for the USA system
            selectable: true, // highlight multiple days by clicking and dragging, used on create new event for multiple days
            defaultView: 'month',
            allDaySlot: true, //if the 'all-day' slot is displayed at the top of the calendar, when hidden with false, all-day events will not be displayed in week/day views.
            selectHelper: false, //whether to draw a “placeholder” event while the user is dragging.
            droppable: false, // if external jQuery UI draggables can be dropped onto the calendar
<?php if($is_admin) { ?>
            select: function(start, end, allDay) { 
                //when a 'day block' square clicked 
   
                //set the click date as start end dates
                $('#dtp_start, #dtp_end').datepicker('setDate', start.toDate());
                //or
                // $('#dtp_start, #dtp_end').datepicker('update', start.toDate());
                
                //just in case!
                $("#rec_id").val('');
                
                //hide delete button
                $("#bntDelete_Event").hide();
                
                //refresh modal title
                $("#lblTitle_Event").text("New Event");
                
                // programmatically clearing the current selection
                $('#calendar').fullCalendar('unselect'); 


                $('#modalEvent').modal('toggle');

            },
            eventClick: function(calEvent, jsEvent, view) {
                //when an event clicked
                //alert('EventID: ' + calEvent.id + '\n'  + 'EventTitle: ' + calEvent.title + '\n' + 'Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY  + '\n' + 'View: ' + view.name);
                
                $("#lblTitle_Event").text(calEvent.title);
                
                // programmatically clearing the current selection
                $('#calendar').fullCalendar('unselect'); 
                
                //show delete button
                $("#bntDelete_Event").show();
                
                //fill the modal, by fetching the record details via recID stored to timebar
                get_rec_details(calEvent.id);
            } 
<?php } ?>
            
        });
        
    }
    
<?php if($is_admin) { ?>
    function validate_modal(){
        var d_start = $("#dtp_start").val();
        var d_end = $("#dtp_end").val();

        if (!d_start) // || !d_end)
        {
            alert("Please fill the date!");
            return false;
        }

        var description = $("#event_descr").val();

        if (!description)
        {
            alert("Please fill the description!");
            return false;
        }

        var e_type = $("#event_type").val();

        if (e_type < 1)
        {
            alert("Please fill the event type!");
            return false;
        }

        return true;
    }
    
    function get_rec_details(rec_id){
        $.ajax(
        {
            url : "get_id.php",
            type: "POST",
            data : {id: rec_id},
            dataType : "json",
            success : function(data, textStatus, jqXHR)
            {
                
                if (!data)
                {
                    alert("ERROR - connection error");
                    return;
                }
                
                //scroll the calendar to spefic date (used only for URL edit)
                <?php if(isset($edit_rec)) { ?>
                    $('#calendar').fullCalendar('gotoDate', data["date_start"]);
                <?php } ?>
                
                //fill controls
                $("#dtp_start").val(data["date_start"]);
                $("#dtp_end").val(data["date_end"]);
                $("#event_type").val(data["event_type"]);
                $("#event_descr").val(data["event_description"]);
                $("#rec_id").val(data["event_id"]);
                
                //show modal
                $('#modalEvent').modal('toggle');
                
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                alert("ERROR - connection error");
            }
        });
    }
    
    function del_rec(){
        
        var ask = prompt("DELETE!\n\nPlease type the word 'delete' to proceed!");
        
        if (!ask)
        {
            alert("canceled");
            return;
        }
        else if(ask!="delete")
        {
            alert("canceled, mistyped!");
            return;
        }   
        
        var rec_id = $("#rec_id").val();
        
        if (!rec_id)
        {
            alert("Cant find record id!");
            return;
        }
            
        $.ajax(
        {
            url : "delete_event.php",
            type: "POST",
            data : {id: rec_id},
            dataType : "json",
            success : function(data, textStatus, jqXHR)
            {

                if (data==1){
                    //refetch daybars from source
                    $('#calendar').fullCalendar('refetchEvents');
                    $('#modalEvent').modal('toggle');
                }
                else
                    alert("ERROR");
                
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                alert("ERROR - connection error");
            }
        });
    }
    
    function goto_date(){
        $('#calendar').fullCalendar('gotoDate', $("#dtp_goto").val());
        $('#modalGotoDate').modal('toggle');
    }
<?php } ?>
    
</script>
</head>
<body>
    <div class="container-fluid">
    <div class="page-header full-content">
    <div class="row">
    <div class="col-sm-6">
    <h1>Calendar <small>by PipisCrew</small></h1>
    </div> 
    <div class="col-sm-6">
    <?php if (!$is_admin) { ?>
        <ol class="breadcrumb">
            <li><a href="../login.php"><i class="tiny-icon icon-plug">&#xf1e6;</i></a></li>
        </ol>
    <?php } else { ?>
        <ol class="breadcrumb">
            <li><a href="../"><i class="tiny-icon icon-eject">&#xe800;</i></a></li>
            <li><a href="timeline.php">timeline</a></li>
        </ol>
    <?php } ?>
    </div> 
    </div> 
    </div> 
    <div class="row">
    <div class="col-md-12">
    <div class="panel">
    <div class="panel-heading">
    <div class="panel-title"><h4>CALENDAR</h4></div>
    </div> 
    <div class="panel-body">
    <div class="row">
    <div class="col-md-12">
    <div id="calendar"></div> 
    </div> 
    </div> 
    </div> 
    </div> 
    </div> 
    </div> 
</div> 
    
    <?php if(!$is_admin) return; ?>
    
<!-- NEW EVENT MODAL [START] -->
<div class="modal fade bs-modal-lg" id="modalEvent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h4 class="modal-title" id="lblTitle_Event">New Event</h4>
			</div>
			<div class="modal-body">
				<form id="formEvent" role="form" method="post" action="save_event.php">

                        <!-- https://github.com/uxsolutions/bootstrap-datepicker-->
                        <div class="input-daterange input-group" id="datepicker">
                            <input type="text" class="input-sm form-control" id="dtp_start" name="dtp_start"/>
                            <span class="input-group-addon">to</span>
                            <input type="text" class="input-sm form-control" id="dtp_end" name="dtp_end"/>
                        </div>

						<div class="form-group">
							<label>Type :</label>
                            <select id="event_type" name="event_type" class="form-control">
                            <option value="1">Common</option>
                            <option value="2">Health</option>
                            <option value="3">Life</option>
                            <option value="4">Travel</option>
                            <option value="5">Job</option>
                            <option value="6">Food</option>
                            <option value="7">Medicine</option>
                            </select>
						</div>
						
						
						<div class="form-group">
							<label>Comment :</label>
							<textarea style="height: 200px;resize: none;" id="event_descr" name="event_descr" class="form-control" placeholder="the event in detail"></textarea>
						</div>
                    
                        <input id="rec_id" name="rec_id" hidden />
                    
						<div class="modal-footer">
							<button id="bntDelete_Event" type="button" class="btn btn-danger pull-left" onclick="javascript:del_rec();">
								delete
							</button>
                            <button id="bntCancel_Event" type="button" class="btn btn-default" data-dismiss="modal">
								cancel
							</button>
							<button id="bntSave_Event" class="btn btn-primary" type="submit" name="submit">
								save
							</button>
						</div>
						
				</form>
			</div><!-- End of Modal body -->
		</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW EVENT MODAL [END] -->

<!-- NEW GOTODATE MODAL [START] -->
<div class="modal fade bs-modal-lg" id="modalGotoDate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h4 class="modal-title" id="lblTitle_GotoDate">Goto Date</h4>
			</div>
			<div class="modal-body">
                
                <div class="input-daterange input-group" id="datepicker">
                    <input type="text" class="input-sm form-control" id="dtp_goto" placeholder="click here"/>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        cancel
                    </button>
                    <button class="btn btn-primary" onClick="goto_date();">
                        goto
                    </button>
                </div>
						
			</div><!-- End of Modal body -->
		</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->
<!-- NEW GOTODATE MODAL [END] -->

</body>
</html>