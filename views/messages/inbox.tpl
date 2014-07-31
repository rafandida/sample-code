{extends file="_includes/default-main.tpl"}
{block name=body}

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
					<li>Dashboard</li><li>Messages</li>
					{if $o.folder_id == 0}
					<li>Inbox</li>
					{else}
					<li>Folder - {$o.check_folder_name|sslash}</li>
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
					{if $o.show_message_inbox_table}
					<article class="col-xs-12 col-sm-12 col-md-12 col-lg-9">

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget jarviswidget-color-blue" id="wid-id-4" data-widget-editbutton="false">
								<!-- widget options:
								usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">

								data-widget-colorbutton="false"
								data-widget-editbutton="false"
								data-widget-togglebutton="false"
								data-widget-deletebutton="false"
								data-widget-fullscreenbutton="false"
								data-widget-custombutton="false"
								data-widget-collapsed="true"
								data-widget-sortable="false"

								-->
								<header>
									<span class="widget-icon"> <i class="fa fa-envelope"></i> </span>
									<h2>{if $o.folder_id == 0}Inbox{else}Folder - {$o.check_folder_name|sslash}{/if} </h2>

								</header>

								<!-- widget div-->
								<div>

									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->

									</div>
									<!-- end widget edit box -->

									<!-- widget content -->
									<div class="widget-body no-padding">
										<div class="table-responsive">
											<table class="table table-bordered table-striped table-condensed table-hover has-tickbox">
												<thead>
													<tr>
														<th>&nbsp;
														</th>
														<th style="width:15px">From </th>
														<th style="width:250px;">Subject </th>
														<th style="width: 50px;" >Date Created</th>
													</tr>
												</thead>
												<tbody>
													{foreach from=$o.show_inbox item=mc}
													<tr>
														<td>
															<label class="checkbox">
																<input type="checkbox" name="checkbox-inline" class="checkBoxClass" value="{$mc.msg_conv_id}">
																<i></i>
															</label>
														</td>
														<td><img src="{$o.baseurl}/assets/images/users/{$mc.user_photo}" alt="{$mc.sname|sslash}" class="online" width="50px" height="54px" /></td>
														<td><a href="{$o.baseurl}/messages/read_msg/{$mc.msg_conv_id}/{$mc.msg_conv_sig}">{if $mc.msg_status == '0'}<span class="label bg-color-orange">NEW</span>&nbsp;{/if}<b>{$mc.subject|sslash}</b></a> <br> 
															<small>{$mc.details|sslash}</small>
															<div class="note note-info">
															From: {$mc.sname|sslash}  <br>
															ID #: {$mc.id_number} <br>
															Department: {$mc.user_dept|sslash}</div></td>
														<td><div class="note note-info">{$mc.date_added_human|sslash} <br> {$mc.date_added|date_format:"%A, %B %e, %Y %I:%M %p"}</div></td>
													</tr>
													{/foreach}
											</tbody>
											</table>
											<div class="widget-footer" style="padding-bottom: 50px">
												<span class="pull-left" >

											<button class="btn btn-sm btn-success" id="check_all_items" type="button">
												Check All
											</button>	
											<button class="btn btn-sm btn-success" id="uncheck_all_items" type="button">
												UnCheck All
											</button>&nbsp;
											{if $o.show_folder_moved}
													&nbsp;{$show_folders} <button class="btn btn-sm btn-info move_selected_to_folder " type="button">
												Move to Folder
											</button>
											{/if}| <button class="btn btn-sm btn-info move_selected_archive " type="button">
												Move to Archive
											</button>	
											 | <button class="btn btn-sm btn-danger delete_selected_item" type="button">
												Delete Selected Item
											</button>
											</span>
											<input type="hidden" value="{if $o.folder_id != '0'}{$o.folder_id}{else}inbox{/if}" id="msg_window">
										</div>
										<div class="widget-footer">
											<ul class="pagination pagination-sm pull-left">
												{$o.create_paging}
											</ul>
										</div>

										</div>
										
									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->
							</div>
							<!-- end widget -->

						</article>
					{/if}
					<!-- END MESSAGE CONTENT -->
				</div>


			</div>
			<!-- END MAIN CONTENT -->

		</div>

		
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
		<script src="{/literal}{$o.baseurl}{literal}/assets/js/messages.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
				show_message_folders();
		});
		</script>
	

{/literal}
{/block}				
{block name=footer}
{extends file="_includes/footer.tpl"}
{/block}
{block name=title}Communicator - {if $o.folder_id == 0}Inbox{else}Folder: {$o.check_folder_name|sslash}{/if}{/block}