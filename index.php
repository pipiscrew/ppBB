<?php

require_once('page_top.php');

require_once('general.php');

$db = new dbase();
$db->connect_sqlite();

if (isset($_SESSION["id"])) {
	$is_admin = true;
	$sql = 'select * from categories order by cat_order, cat_name';
}
else {
	$is_admin = false;
	$sql = 'select * from categories where cat_private = 0 order by cat_order, cat_name';
}

$rows = $db->getSet($sql, null);

?>


<?php if ($is_admin) { ?>

	<button class="btn btn-default btn-primary" onclick="add_new();">
		<span class="glyphicon glyphicon-plus"></span> new forum
	</button>

<?php } ?>


				<table class="table table-hover" id="forums">
					<thead>
						<tr>
							<th>Type</th>
							<th>Forum</th>
							<th>Last Activity</th>
<?php if ($is_admin) {?>
							<th>Private</th>
							<th>Action</th>
<?php } ?>
						</tr>
					</thead>
					<tbody>


					<?php foreach($rows as $row) { ?>
						<tr class="topics">
							<td>
								<img src="assets/cat_icon.png" class="img-polaroid" data-id="<?= $row['cat_id']; ?>"/>
							</td>
							<td>
								<a href="list_topics.php?id=<?= $row['cat_id']; ?>"><?= $row['cat_name']; ?></a>
							</td>
							<td><?= $row['cat_last_update']; ?></td>
<?php if ($is_admin) {?>
							<td><?= ($row['cat_private']==1) ? '<span class="glyphicon glyphicon-ok">' : ''  ?></td>
							<td><a id="btn_edit" class="btn btn-primary btn-xs">Edit</a></td>
<?php } ?>
						</tr>
					<?php } ?>
					
					</tbody>
				</table>

<?php if ($is_admin) { ?>

<div id="modal_tr" class="modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form class="form-horizontal" method="get">
				<div class="modal-header">
					<strong id="modal_tr_title">Forum Edit</strong>
				</div>

				<div class="modal-body">
					<div class="form-group">
							<label>Name :</label>
							<input name="forum_name" id="forum_name" class="form-control" placeholder="Enter forum name...">
						</div>

						<div class="form-group">
							<div class="checkbox">
								<label><input name="forum_private" id="forum_private" type="checkbox">Private</label> 
							</div>
						</div>

						<input name="forum_id" id="forum_id"> 
						<!-- style="display:none;" -->
				</div>

				<div class="modal-footer">
					<button class="btn btn-default" type="button" onclick="modal.close();">cancel</button>
					<button class="btn btn-primary" type="button" onclick="form_submit();">save</button>
				</div>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript" src="assets/rmodal.min.js"></script>

<script>
	
	//when all elements rendered
	window.onload = function() {
		var modal = new RModal(document.getElementById('modal_tr'), {
				closeTimeout: 10
		});

		//pass the modal variable to window obj, as per rmodal.js specs (used on modal closed) 
		window.modal = modal;
	}

	//add new button
	function add_new(){
		
		tr = null;

		document.getElementById("forum_id").value = '';
		document.getElementById("forum_name").value = '';
		document.getElementById("forum_private").checked = false;

		//
		document.getElementById("modal_tr_title").innerHTML = "New Forum";

		modal.open();
	}
	//add new button 


	//edit buttons
	Array.prototype.forEach.call(document.querySelectorAll('#btn_edit'), function(el) {

		el.addEventListener('click', function(e) {
			e.preventDefault();

			var rec = getSelected(e.srcElement);

			//fill the modal object by table content!
			if (rec) {
				document.getElementById("forum_id").value = rec.id;
				document.getElementById("forum_name").value = rec.forum_name;
				document.getElementById("forum_private").checked = rec.forum_private;

				//
				document.getElementById("modal_tr_title").innerHTML = "Edit Forum";

				modal.open();
			}
			
		})

	})
	
	var tr = null; 

	function getSelected(e){

		tr = e.parentNode.parentNode;

		var id = tr.cells[0].children[0].getAttribute('data-id');
		var forum_name = tr.cells[1].children[0].text;
		var forum_private = tr.cells[3].childElementCount;
		
		return { id, forum_name, forum_private };
	}
	//edit buttons


	//save/update record from modal 
	function form_submit(){

		var forum_name = document.getElementById("forum_name").value.trim();
		var forum_private = (document.getElementById("forum_private").checked) ? 1 : 0;
 		var forum_id = document.getElementById("forum_id").value.trim();

		postAjax('save_forum.php', { forum_name: forum_name, forum_private: forum_private, forum_id : forum_id }, function(data){ 
			var result = JSON.parse(data);
			
			if (result.status == 1){
				
				if (tr != null && !result.rec_id) {
					//is public variable didnt set and also the PHP response doesnt contain rec_id property (JSON)

					// [ UPDATE ]
					

					//on success update the UI - tr is a public variable
					tr.cells[1].children[0].text = forum_name;

					if (forum_private==0 && tr.cells[3].childElementCount==1)
						tr.cells[3].firstChild.remove();
					else if (forum_private==1 && tr.cells[3].childElementCount==0)
						tr.cells[3].innerHTML = '<span class="glyphicon glyphicon-ok">';

					tr = null;
				} else 	{
					// [ ADD NEW ]

					//https://stackoverflow.com/questions/18333427/how-to-insert-row-in-html-table-body-in-javascript
					var table = document.getElementById("forums");

					// create an empty <tr> element
					var row = table.insertRow(0);

					// insert td elements
					var cell1 = row.insertCell(0);
					var cell2 = row.insertCell(1);
					var cell3 = row.insertCell(2);
					var cell4 = row.insertCell(3);
					var cell5 = row.insertCell(4);

					// add new record data to cells
					cell1.innerHTML = '<img src="assets/cat_icon.png" class="img-polaroid" data-id="' + result.rec_id + '"/>';
					cell2.innerHTML = '<a href="list_topics.php?id=' + result.rec_id + '">' + forum_name + '</a>';
					cell3.innerHTML = "";
					cell4.innerHTML = (forum_private==1) ? '<span class="glyphicon glyphicon-ok">' : '';
					cell5.innerHTML = ""; //'<a id="btn_edit" class="btn btn-primary btn-xs">Edit</a>'; //needs addEventListener

					table.tBodies[0].appendChild(row)
				}

				modal.close();

			} else {
				alert ("error occured!");
			}
		});
	
		
	}
	//save/update record from modal

	//native ajax - https://plainjs.com/javascript/ajax/send-ajax-get-and-post-requests-47/
	// https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch -PHPparadox-  https://codepen.io/dericksozo/post/fetch-api-json-php + https://github.com/github/fetch/issues/263
	function postAjax(url, data, result) {
		var params = typeof data == 'string' ? data : Object.keys(data).map(
				function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
			).join('&');

		var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
		xhr.open('POST', url);
		xhr.onreadystatechange = function() {
			if (xhr.readyState>3 && xhr.status==200) { result(xhr.responseText); } else if (xhr.status!=200) {result("error");}
		};
		xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.send(params);
		return xhr;
	}

</script>

<?php
}

require_once('page_footer.php');

?>