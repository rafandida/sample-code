{extends file="_includes/default-main.tpl"}
{block name=body}
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
		<link rel="stylesheet" href="{$o.baseurl}/assets/js/uploadify/uploadify.css">
			<!-- possible classes: minified, fixed-ribbon, fixed-header, fixed-width-->

		<!-- HEADER -->
		{include file="_includes/header.tpl"}
		<!-- END HEADER -->

		<!-- Left panel : Navigation area -->
		{include file="_includes/left_menu.tpl"}
		<!-- END of Left panel : Navigation area -->

		<!-- MAIN PANEL -->
		<div id="main" role="main">

			<!-- RIBBON -->
			<div id="ribbon">
				<!-- breadcrumb -->
				<ol class="breadcrumb">
					{if $o.gmd.is_reply == '1'}
					<li>Dashboard</li><li>Messages</li><li>Reply message</li>
					{else}
					<li>Dashboard</li><li>Messages</li><li>Compose</li><li>Users</li>
					{/if}
				</ol>
			</div>
			<!-- END RIBBON -->

			<!-- MAIN CONTENT -->
			<div id="content">
					{if $o.message}
						{include file="_includes/infomessages.tpl"}
					{/if}			
				<div class="row">
					<!-- START MESSAGE SIDEBAR -->
					{include file="messages/message_sidebar.tpl"}
					<!-- END MESSAGE SIDEBAR -->

					<!-- START MESSAGE CONTENT -->
					<article class="col-sm-12 col-md-12 col-lg-9">
				
							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget jarviswidget-color-blue" id="wid-id-2" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-custombutton="false">
								<header>
									<span class="widget-icon"> <i class="fa fa-edit"></i> </span>
									<h2>{if $o.gmd.is_reply == '1'}Reply message{else}Compose new message{/if}</h2>
				
								</header>
				
								<!-- widget div-->
								<div>
				
									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->
				
									</div>
									<!-- end widget edit box -->
				
									<!-- widget content -->
									<div class="widget-body">
				
										<form  method="POST" action="" enctype="multipart/form-data">
				
											<fieldset>

												<div class="form-group">
													<input style="width:100%" class="select2" placeholder="TO" type="text" id="searchuser">
													<div id="log" class="font-xs margin-top-10 text-danger"></div>
													<div class="note">
														<strong>Lookup:</strong> View list of groups
														<a data-toggle="modal" href="{$o.baseurl}/compose/show_addressbook/0" data-target="#myModal" >
						Click here</a>
													</div>
												</div>
				
												<div class="form-group">
													<input style="width:100%" class="select2" placeholder="CC" type="text" id="searchusercc">
													<div id="log" class="font-xs margin-top-10 text-danger"></div>
													<div class="note">
														<strong>Lookup:</strong> View list of groups
														<a data-toggle="modal" href="{$o.baseurl}/compose/show_addressbook/1" data-target="#myModal">
						Click here</a>
													</div>
												</div>

												 <div class="form-group">
													<input style="width:100%" class="form-control" placeholder="SUBJECT" type="text" id="subject" name="subject" value="{$o.gmd.subject|sslash}">
													<div id="log" class="font-xs margin-top-10 text-danger"></div>
												</div>
				
											</fieldset>
				
											<fieldset>
												<div class="row">
													<div class="col-sm-12">
														<div class="form-group">
															<div id="emailbody">{$o.gmd.details|sslash}</div>	
														</div>
													</div>
							
												</div>
											</fieldset>
											<fieldset>
												<legend>
													Attachments
												</legend>
												<div class="row">
													<div class="col-sm-12">
														<div class="well no-padding">				
														<div align="center" style="padding-top: 10px;">						
															<input data-no-uniform="true" type="file" name="userfile" id="userfile" />
                              							</div>
                              							<div class="control-group"><label id="fileQueue_image"></label></div>                           
                              							<div id="show_attachments"></div>
													</div>				
												</div>
											</fieldset>
											<div class="inbox-compose-footer">
											<div class="row">
													<div class="col-md-12">
													<button class="btn btn-danger" type="button" id="disregard">
														Disregard
													</button>
														
													<button class="btn btn-info" type="button" id="savedraft">
														Save As Draft
													</button>


													<button  class="btn btn-primary pull-right" type="button" id="send">
														Send <i class="fa fa-arrow-circle-right fa-lg"></i>
													</button>
													<input type="hidden" name="compose_id" id="compose_id" value="{$o.gmd.msg_compose_id}">
													<input type="hidden" name="msg_type" id="msg_type" value="{$o.gmd.msg_type}">
												</div>
												</div>
											</div>
											
										</form>
				
									</div>
									<!-- end widget content -->
				
								</div>
								<!-- end widget div -->
				
							</div>
							<!-- end widget -->
				
						</article>
					<!-- END MESSAGE CONTENT -->
				</div>


			</div>
			<!-- END MAIN CONTENT -->

		</div>

	<!-- Modal -->
				<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">

						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->			
{/block}
{block name=JsFooter}
{literal}
		<!-- PAGE RELATED PLUGIN(S) -->

		<script src="{/literal}{$o.baseurl}{literal}/assets/js/plugin/datatables/jquery.dataTables.min.js"></script>
		<script src="{/literal}{$o.baseurl}{literal}/assets/js/plugin/datatables/dataTables.colVis.min.js"></script>
		<script src="{/literal}{$o.baseurl}{literal}/assets/js/plugin/datatables/dataTables.tableTools.min.js"></script>
		<script src="{/literal}{$o.baseurl}{literal}/assets/js/plugin/datatables/dataTables.bootstrap.min.js"></script>
		<script src="{/literal}{$o.baseurl}{literal}/assets/js/plugin/delete-table-row/delete-table-row.min.js"></script>
		<script src="{/literal}{$o.baseurl}{literal}/assets/js/plugin/summernote/summernote.min.js"></script>
		<script src="{/literal}{$o.baseurl}{literal}/assets/js/uploadify/jquery.uploadify-3.1.min.js"></script>

		<script src="{/literal}{$o.baseurl}{literal}/assets/js/messages.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {

		});
		

		$("a[data-target=#myModal]").click(function(ev) {
    		ev.preventDefault();
   		 	var target = $(this).attr("href");
   		 // load the url and show modal on success
    		$("#myModal .modal-body-container").load(target, function() { 
         	$("#myModal").modal("show"); 
   		 	});
		});


		show_message_folders();
			var upload_base_url = '{/literal}{$o.baseurl}{literal}';

			$('#userfile').uploadify({
				'swf'      			: upload_base_url + '/assets/js/uploadify/uploadify.swf',
				'uploader' 			: upload_base_url + '/compose/upload_file',
				'formData'			: {'msg_compose_id':'{/literal}{$o.gmd.msg_compose_id}{literal}','user_id':'{/literal}{$o.gmd.user_id}{literal}'},
				'queueID'        	: "fileQueue_image",
				'fileSizeLimit'		: '10MB',
				'progressData'		: 'percentage',
				'auto'           	: true,
				'fileTypeDesc'		: 'Upload Image (*.jpeg;*.jpg;*.doc;*.xls;*.docx)',
				'fileTypeExts'		: '*.jpeg;*.jpg;*.doc;*.xls;*.docx',
				'simUploadLimit'	: 2,
				'multi'         	: true,
				 'onUploadError' : function(file, errorCode, errorMsg, errorString) {
					alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
					},			
				'onUploadSuccess' : function(file, data, response) {						 
					//alert('The file ' + file.name + ' was successfully uploaded with a response of ' + response + ':' + data);
					//document.location.reload(true);
				  },
				  'onQueueComplete' : function(queueData) {
				  	show_attachments('{/literal}{$o.gmd.msg_compose_id}{literal}');
				  	//alert(queueData);
        			//alert(queueData.uploadsSuccessful + ' files were successfully uploaded.');
					//document.location.reload(true);
		        }
			// Your options here
			});

	// DO NOT REMOVE : GLOBAL FUNCTIONS!

	//runAllForms();

	 // PAGE RELATED SCRIPTS

	$(".table-wrap [rel=tooltip]").tooltip();


	$("")

    $('#emailbody').summernote({
        height: 200,
        focus: false,
        tabsize: 2,
 		toolbar: [
	    //[groupname, [button list]]
	    ['wizardstyle', ['style']],
	    ['style', ['bold', 'italic', 'underline', 'clear']],
	    ['fontsize', ['fontsize']],
	    ['color', ['color']],
	    ['para', ['ul', 'ol', 'paragraph']],
	    ['height', ['height']],
	    ['fullscreen', ['fullscreen','codeview']],
	    ['help', ['help']]
	  ]       
    });


	$(".show-next").click(function () {
	    $this = $(this);
	    $this.hide();
	    $this.parent().parent().parent().parent().parent().next().removeClass("hidden");
	})

	var baseurl = '{/literal}{$o.baseurl}{literal}';
	$('#searchuser').select2({
      	multiple: true,
      	minimumInputLength: 2,
      	allowClear : true,
    	ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
        	url: baseurl + "/compose/search_users",
        	dataType: 'json',
        	data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
        	results: function (data, page) {
    				return { results: data };
  			}
        },
        escapeMarkup: function (m) { return m; }
     });

	$('#searchusercc').select2({
      	multiple: true,
      	minimumInputLength: 2,
      	allowClear : true,
    	ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
        	url: baseurl + "/compose/search_users",
        	dataType: 'json',
        	data: function (term, page) {
                return {
                    term: term, //search term
                    page_limit: 10 // page size
                };
            },
        	results: function (data, page) {
    				return { results: data };
  			}
        },
        escapeMarkup: function (m) { return m; }
     });

	{/literal}
	{if $o.has_saved_user_to == 1}
	{literal}
     $('#searchuser').select2('data', {/literal}{$o.default_user_to}{literal} );
    {/literal}
	{/if}
    {literal}

    {/literal}
	{if $o.has_saved_user_cc == 1}
	{literal}
     $('#searchusercc').select2('data', {/literal}{$o.default_user_cc}{literal} );
    {/literal}
	{/if}
    {literal}

    /* show attachments*/
    setTimeout(show_attachments('{/literal}{$o.gmd.msg_compose_id}{literal}'),2000);

    function include_group_to_compose(group_id){
    	//$("#searchusercc").select2("data", [{id: "CA", text: "California"},{id:"MA", text: "Massachusetts"}]);
    	var baseurl 		= document.getElementById('baseurl').value;
	   	$.ajax
		({
			type: "POST",
			url: baseurl + "/compose/include_group_comp",
			data: ({"group_id" : group_id}),
			cache: false,
			beforeSend: function()
			{
					$.smallBox({
						title : "Adding..",
						content : "<i class='fa fa-gear fa-spin'></i> <i>Adding, please wait....</i>",
						color : "#659265",
						iconSmall : "fa fa-check fa-2x fadeInRight animated",
						timeout : 4000
					});	
			},

			success: function(result)
			{

				$.smallBox({
					title : "Success..",
					content : "<i class='fa fa-check-circle'></i> <i>Users in group, successfully added</i>",
					color : "#659265",
					iconSmall : "fa fa-check fa-2x fadeInRight animated",
					timeout : 4000
				});

				var data = $("#searchusercc").select2('data');
				var arrdata = result; 
				$(arrdata.split(",")).each(function () {
				data.push({ id: this.split(':')[0], text: this.split(':')[1] });});

				 $("#searchusercc").select2("data", data,true);

				$('#myModal').modal('hide');
			}					
		});

    	
    }	

    function include_groupuser_to_compose(group_id){
    	var baseurl 		= document.getElementById('baseurl').value;
	   	$.ajax
		({
			type: "POST",
			url: baseurl + "/compose/include_group_comp",
			data: ({"group_id" : group_id}),
			cache: false,
			beforeSend: function()
			{
					$.smallBox({
						title : "Adding..",
						content : "<i class='fa fa-gear fa-spin'></i> <i>Adding, please wait....</i>",
						color : "#659265",
						iconSmall : "fa fa-check fa-2x fadeInRight animated",
						timeout : 4000
					});	
			},

			success: function(result)
			{

				$.smallBox({
					title : "Success..",
					content : "<i class='fa fa-check-circle'></i> <i>Users in group, successfully added</i>",
					color : "#659265",
					iconSmall : "fa fa-check fa-2x fadeInRight animated",
					timeout : 4000
				});

				var data = $("#searchuser").select2('data');
				var arrdata = result; 
				$(arrdata.split(",")).each(function () {
				data.push({ id: this.split(':')[0], text: this.split(':')[1] });});

				 $("#searchuser").select2("data", data,true);

				$('#myModal').modal('hide');
			}					
		});  	
    }    
</script>

{/literal}
{/block}				
{block name=footer}
{extends file="_includes/footer.tpl"}
{/block}
{block name=title}Communicator - Compose Message to users{/block}