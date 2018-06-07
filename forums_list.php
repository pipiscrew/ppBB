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
						<?php foreach($cat_rows as $cat_row) { ?>
							<tr class="topics">
								<td>
									<img src="assets/cat_icon.png" class="img-polaroid" data-id="<?= $cat_row['cat_id']; ?>" data-order="<?= $cat_row['cat_order']; ?>"/>
								</td>
								<td>
									<a href="list_topics.php?id=<?= $cat_row['cat_id']; ?>"><?= $cat_row['cat_name']; ?></a>
								</td>
								<td><?= $cat_row['cat_last_update']; ?></td>
	<?php if ($is_admin) {?>
								<td><?= ($cat_row['cat_private']==1) ? '<span class="glyphicon glyphicon-ok">' : ''  ?></td>
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

                        	<div class="form-group">
                                <label>Sort Order :</label>
                                <input type="number" name="forum_order" id="forum_order" class="form-control" style="width:80px" min=1 value=0>
                            </div>

                            <input style="display:none;" name="forum_id" id="forum_id">
                            <input style="display:none;" name="parent_forum_id" id="parent_forum_id" value ="<?= (isset($category_id)) ? $category_id : 0 ?>" >
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
        //when coming from subforums and panel element is not visible show it (enables the modal that is inside the panel, otheriwse not showing)
        var subforums_panel = document.getElementById("subforums_panel");
        if (subforums_panel) {
            subforums_panel.style.display = 'block';
        }

		tr = null;

		document.getElementById("forum_id").value = '';
        document.getElementById("forum_name").value = '';
		document.getElementById("forum_private").checked = false;
		document.getElementById("forum_order").value = 0;

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
				document.getElementById("forum_order").value = rec.forum_order;

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
		var forum_order = tr.cells[0].children[0].getAttribute('data-order');
		
		return { id, forum_name, forum_private, forum_order };
	}
	//edit buttons


	//save/update record from modal 
	function form_submit(){

		var forum_name = document.getElementById("forum_name").value.trim();
        var parent_forum_id = document.getElementById("parent_forum_id").value;
		var forum_private = (document.getElementById("forum_private").checked) ? 1 : 0;
 		var forum_id = document.getElementById("forum_id").value.trim();
		var forum_order = document.getElementById("forum_order").value;

		 if (forum_name.length == 0)
		 {
			 alert("please enter forum name!");
			 return;
		 }

		postAjax('save_forum.php', { forum_name: forum_name, parent_forum_id: parent_forum_id, forum_private: forum_private, forum_order: forum_order, forum_id: forum_id }, function(data){ 
			var result = JSON.parse(data);
			
			if (result.status == 1){
				
				if (tr != null && !result.rec_id) {
					//if public variable didnt set and also the PHP response doesnt contain rec_id property (JSON)

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

<?php } ?>