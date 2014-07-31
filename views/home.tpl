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
			<!-- END RIBBON -->

			<!-- MAIN CONTENT -->
			<div id="content">
					{if $o.message}
						{include file="_includes/infomessages.tpl"}
					{/if}	
			<!-- FIRST ROW! -->		
			<section id="widget-grid" class="">
			<div class="row">
				<article class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
{if $o.show_approved_table}
							<div class="jarviswidget jarviswidget-color-blue" id="wid-id-1" data-widget-colorbutton="false"	
									data-widget-editbutton="false"
									data-widget-togglebutton="false"
									data-widget-deletebutton="false"
									data-widget-fullscreenbutton="true"
									data-widget-custombutton="false"
									data-widget-collapsed="false" 
									data-widget-sortable="false">

								<header>
									<span class="widget-icon"> <i class="fa fa-calendar"></i> </span>
									<h2> My Meetings </h2>
									<div class="widget-toolbar">
										<!-- add: non-hidden - to disable auto hide -->
										<div class="btn-group">
											<button class="btn dropdown-toggle btn-xs btn-default" data-toggle="dropdown">
												Showing <i class="fa fa-caret-down"></i>
											</button>
											<ul class="dropdown-menu js-status-update pull-right">
												<li>
													<a href="javascript:void(0);" id="mt">Month</a>
												</li>
												<li>
													<a href="javascript:void(0);" id="ag">Agenda</a>
												</li>
												<li>
													<a href="javascript:void(0);" id="td">Today</a>
												</li>
											</ul>
										</div>
									</div>
								</header>

								<!-- widget div-->
								<div>
									<!-- widget edit box -->
									<div class="jarviswidget-editbox">

										<input class="form-control" type="text">

									</div>
									<!-- end widget edit box -->

									<div class="widget-body no-padding">
										<!-- content goes here -->
										<div class="widget-body-toolbar">

											<div id="calendar-buttons">

												<div class="btn-group">
													<a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-prev"><i class="fa fa-chevron-left"></i></a>
													<a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-next"><i class="fa fa-chevron-right"></i></a>
												</div>
											</div>
										</div>
										<div id="calendar"></div>

										<!-- end content -->
									</div>

								</div>
								<!-- end widget div -->
							</div>
							<!-- end widget -->
							{else}
							<div class="jarviswidget jarviswidget-color-blue" id="wid-id-2" data-widget-colorbutton="false"	
									data-widget-editbutton="false"
									data-widget-togglebutton="false"
									data-widget-deletebutton="false"
									data-widget-fullscreenbutton="false"
									data-widget-custombutton="false"
									data-widget-collapsed="false" 
									data-widget-sortable="false">
	
								<header>
									<span class="widget-icon"> <i class="fa fa-calendar"></i> </span>
									<h2> My Meetings </h2>
								</header>
								<div>
								<div class="widget-body no-padding">
									<div class="alert alert-info fade in">
											<i class="fa-fw fa fa-info"></i>
										<strong>Info!</strong> You Have no schedules Meetings.		
									</div>
								</div>
								</div>
							</div>	
							{/if}

				<!-- BULLETIN BOARD -->
{if $o.show_bulletin_table}

							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget jarviswidget-color-blue" id="wid-id-3" data-widget-colorbutton="false"	
									data-widget-editbutton="false"
									data-widget-togglebutton="false"
									data-widget-deletebutton="false"
									data-widget-fullscreenbutton="false"
									data-widget-custombutton="false"
									data-widget-collapsed="false" 
									data-widget-sortable="false">
								<header>
									<span class="widget-icon"> <i class="fa fa-bullhorn"></i> </span>
									<h2>Bulletin Board </h2>

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
														<th style="width:300px;">Subject </th>
													</tr>
												</thead>
												<tbody>
													{foreach from=$o.msg_content item=mc}
													<tr class="bb_{$mc.bulletin_id}">
														<td>{counter}
														</td>
														<td><b><a href="{$o.baseurl}/bulletinboard/view_content/{$mc.bulletin_id}">{$mc.bsubject|sslash}</a></b><div class="note note-info">{$mc.date_added|date_format:"%A, %B %e, %Y %I:%M %p"} <br> Views: {$mc.views|number_format:0}</div></td>
													</tr>
													{/foreach}
											</tbody>
											</table>

										</div>
										
									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->
							</div>
							<!-- end widget -->

					{/if}
				<!-- END OF BULLETIN BOARD-->




				</article>
				<article class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
								<!-- end widget div -->
								
						<div class="jarviswidget jarviswidget-color-blue" id="wid-id-4" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
								<header>
									<span class="widget-icon"> <i class="fa fa-phone"></i> </span>
									<h2>INFO</h2>

								</header>

								<!-- widget div-->
								<div>
									<div class="widget-body" >
										<div class="table-responsive">
											<h5><span class="semi-bold">Information Technology </span> <i class="ultra-light">Department</i><br>
								<small class="text-danger slideInRight fast animated"><strong>Contact Numbers</strong></small></h5>
							
								<p>Tech Support During Regular Office Hours LOC. 5425<br><br>

                                        Tech Support Mondays - Fridays (after 6:00PM)<br>
                                        Weekends and Holidays SUN NUM. <br>
                                     
                                        <font color="#FF0000"><b><u>0923-746-8452</u></b></font>
								</p>

										</div>
										
									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->
							</div>
							<!-- end widget -->

					{if $o.show_approved_table}
							<div class="jarviswidget jarviswidget-color-blue" id="wid-id-5" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
	
								<header>
									<span class="widget-icon"> <i class="fa fa-calendar"></i> </span>
									<h2>Upcoming Meetings</h2>

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
											<table class="table table-bordered table-striped table-condensed table-hover">
												<thead>
													<tr>
														<th>Host</th>
														<th style="width:300px;">Subject </th>
													</tr>
												</thead>
												<tbody>
													{foreach from=$o.cameet item=mc}
													<tr class="bb_{$mc.bulletin_id}">
														<td><img src="{$o.baseurl}/assets/images/users/{$mc.user_photo}" alt="{$mc.sname|sslash}" class="online" width="50px" height="54px" /></td>
														<td><a href="{$o.baseurl}/meetings/view_request/{$mc.meeting_user_id}"><b>{$mc.subject|sslash}</b></a><div class="note note-info">by: {$mc.sname|sslash} <br> {$mc.user_dept|sslash}<br>Date:{$mc.date_from} - {$mc.date_to}</div></td>	
													</tr>
													{/foreach}
											</tbody>
											</table>
										</div>
										
									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->
							</div>
							{/if}

							<!-- BIRTHDAYS -->
							<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget jarviswidget-color-blue" id="wid-id-6" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
								<header>
									<span class="widget-icon"> <i class="fa fa-cutlery"></i> </span>
									<h2>Birthdays</h2>

								</header>

								<!-- widget div-->
								<div>
									<div class="widget-body" >
										<div class="table-responsive">
											
	<ul class="list-inline friends-list">
		{foreach from=$o.show_birthday item=sv}
		<li><a href="javascript:void(0);" rel="popover" data-placement="top" data-original-title="User Profile" data-content="Name: {$sv.firstname|sslash|capitalize} {$sv.lastname|sslash|capitalize} <div class='note note-info'> Department: {$sv.department_name} </div> <br> <div align=center><a href='{$o.baseurl}/home/send_birthday_greetings/{$sv.user_id}' class='btn btn-success btn-xs'>Send Greetings</a></div>" data-html="true"><img src="{$o.baseurl}/assets/images/users/{$sv.user_photo}" alt="{$sv.name|sslash}"></a>
		</li>
		{/foreach}
	</ul>

										</div>
										
									</div>
									<!-- end widget content -->
<!-- END OF BIRTHDAYS -->

								</div>
							</div>
							<!-- START EXCELSS -->
<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget jarviswidget-color-blue" id="wid-id-7" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
								<header>
									<span class="widget-icon"> <i class="fa fa-flag"></i> </span>
									<h2>EXCELSS</h2>

								</header>

								<!-- widget div-->
								<div>
									<div class="widget-body no-padding" align="center">
										<img src="{$o.baseurl}/assets/images/excelss.png">
									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->
							</div>
							<!-- end widget -->
<!-- END EX -->
								<!-- end widget div -->
						
				<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget jarviswidget-color-blue" id="wid-id-8" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
								<header>
									<span class="widget-icon"> <i class="fa fa-hospital-o"></i> </span>
									<h2>MISSION STATEMENT</h2>

								</header>

								<!-- widget div-->
								<div>
									<div class="widget-body" >
										<div align="center">Consistent with our goals<br>
      to serve the community,<br>
      we commit to deliver a broad range<br>
      of quality health care services,<br>
      to heal and restore to health individuals<br>
      who have entrusted their care to us.<br>
      We shall nurture a team of dedicated, well-trained<br>
      and sensitive health professionals<br>
      who adhere to the highest standards<br>
      of competence and ethics<br>
      and who take pride in being part of the RMCI family.<br>
      We shall continuously improve our services,<br>
      facilities and equipment<br>
      ever mindful of our role to heal the whole person<br>
      and to render this in the best possible way.
</div>
									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->
							</div>
							<!-- end widget -->							

	<!-- Widget ID (each widget will need unique ID)-->
							<div class="jarviswidget jarviswidget-color-blue" id="wid-id-9"  data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
								<header>
									<span class="widget-icon"> <i class="fa fa-institution"></i> </span>
									<h2>VISION MISSION</h2>

								</header>

								<!-- widget div-->
								<div>
									<div class="widget-body" >
										<div align="center">To Be the<br>
      PREMIER CENTER FOR WELLNESS<br>
      in the country and a<br>
      SYMBOL OF QUALITY AND EXCELLENCE<br>
      in the delivery of health care services<br>
      with skilled, competent<br>
      and Compassionate professionals.
</div>
									</div>
									<!-- end widget content -->

								</div>
								<!-- end widget div -->
							</div>
							<!-- end widget -->

				</article>

				</div>
			<!-- END OF THIRD ROW! -->
			</div>
		</div>

	</div>
{/block}
{block name=JsFooter}
{literal}
		<!-- PAGE RELATED PLUGIN(S) -->
		<script src="{/literal}{$o.baseurl}{literal}/assets/js/plugin/fullcalendar/jquery.fullcalendar.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {

	/*
				 * FULL CALENDAR JS
				 */
var baseurl = "{/literal}{$o.baseurl}{literal}";
				if ($("#calendar").length) {

					var calendar = $('#calendar').fullCalendar({

						editable : false,
						draggable : false,
						selectable : false,
						selectHelper : false,
						unselectAuto : false,
						disableResizing : false,

						header : {
							left : 'title', //,today
							center : 'prev, next, today',
							right : 'month, agendaWeek, agenDay' //month, agendaDay,
						},

					
						events : {/literal}{$o.json_econde_cal}{literal},
						eventRender : function(event, element, icon) {
							if (!event.description == "") {
								element.find('.fc-event-title').append("<br/><span class='ultra-light'>" + event.description + "</span>");
							}
							if (!event.icon == "") {
								element.find('.fc-event-title').append("<i class='air air-top-right fa " + event.icon + " '></i>");
							}
						},
						  eventClick: function(calEvent, jsEvent, view) {

						  		window.location.replace(baseurl + '/meetings/view_request/' + calEvent.id);
						        //alert('Event: ' + calEvent.id);
						        //$(this).css('border-color', 'red');

  							 }
					});

				};

							/* hide default buttons */
				$('.fc-header-right, .fc-header-center').hide();

				// calendar prev
				$('#calendar-buttons #btn-prev').click(function() {
					$('.fc-button-prev').click();
					return false;
				});

				// calendar next
				$('#calendar-buttons #btn-next').click(function() {
					$('.fc-button-next').click();
					return false;
				});

				// calendar today
				$('#calendar-buttons #btn-today').click(function() {
					$('.fc-button-today').click();
					return false;
				});

				// calendar month
				$('#mt').click(function() {
					$('#calendar').fullCalendar('changeView', 'month');
				});

				// calendar agenda week
				$('#ag').click(function() {
					$('#calendar').fullCalendar('changeView', 'agendaWeek');
				});

				// calendar agenda day
				$('#td').click(function() {
					$('#calendar').fullCalendar('changeView', 'agendaDay');
				});
});


</script>
	

{/literal}
{/block}				
{block name=footer}
{extends file="_includes/footer.tpl"}
{/block}
{block name=title}Communicator - Dashboard{/block}