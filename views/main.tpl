{extends file="_includes/default.tpl"}
{block name=body}
		<!-- possible classes: minified, no-right-panel, fixed-ribbon, fixed-header, fixed-width-->
		<header id="header" style="margin: 0;
height: 71px;
border-bottom: 1px solid #eee !important;
overflow: hidden;
padding: 0 30px;
border-width: 0;
min-height: 28px;
background: #058dc7 !important;">

			<div id="logo-group">
				<span id="logo"> <img src="{$o.baseurl}/assets/images/communicator-home.png" alt="DPOTMH Communicator"> </span>
			</div>



		</header>

		<div id="main" role="main">

			<!-- MAIN CONTENT -->
			<div id="content" class="container">

				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-7 col-lg-8 hidden-xs hidden-sm">
						<div class="hero">

							
<!-- well -->
{if $o.show_slideshow_table}
								<div class="col-xs-12">
									<div class="well">
										<div id="myCarousel" class="carousel fade">
											<ol class="carousel-indicators">
												{foreach from=$o.show_gall item=sg}

												<li data-target="#myCarousel" data-slide-to="{$sg.count}" {if $sg.count == '0'}class="active"{else}class=""{/if}></li>
												{/foreach}
											</ol>
											<div class="carousel-inner">
												<!-- Slide 1 -->
												{foreach from=$o.show_gall item=sg2}
												<div class="item {if $sg2.count == '0'}active{/if}">
													<img src="{$o.baseurl}/assets/images/login-banner/{$sg2.pic_path|sslash}" alt="">
													<div class="carousel-caption caption-right">
														<h4>{$sg2.gall_head|sslash}</h4>
														<p>
															{$sg2.gall_text|sslash} 
														</p>
													</div>
												</div>
												{/foreach}
												<!-- Slide 2 -->
												
											</div>
											<a class="left carousel-control" href="#myCarousel" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left"></span> </a>
											<a class="right carousel-control" href="#myCarousel" data-slide="next"> <span class="glyphicon glyphicon-chevron-right"></span> </a>
										</div>
				
									</div>
								</div>
									<!-- end well-->
{/if}
						</div>

						

					</div>
					<div class="col-xs-12 col-sm-12 col-md-5 col-lg-4">
						<div class="well no-padding">
							<form action="{$o.baseurl}/main/" id="login-form" class="smart-form client-form" method="POST">
								<header>
									Sign In
								</header>
								
								<fieldset> 
									{if $o.message}
									<div class="alert alert-{$o.msg_type} fade in">
								<button class="close" data-dismiss="alert">×</button>
								<i class="fa-fw fa {if $o.msg_type == 'warning'}fa-warning{else}fa-check{/if}"></i>
								{$o.message|sslash}
							</div>
								{/if}
									<section>
										<label class="label">ID number</label>
										<label class="input"> <i class="icon-append fa fa-user"></i>
											<input type="idnumber" name="idnumber">
											<b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter id number</b></label>
											<div class="note">For hospital employee, login using your Active Directory account</div>
									</section>

									<section>
										<label class="label">Password</label>
										<label class="input"> <i class="icon-append fa fa-lock"></i>
											<input type="password" name="password">
											<b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Enter your password</b> </label>
										
									</section>

								</fieldset>
								<footer>
									<button type="submit" class="btn btn-primary" value="login" name="p">
										Sign in
									</button>
								</footer>
							</form>

						</div>
						
					</div>
				</div>
			</div>

		</div>

{/block}
{block name=JsFooter}
{literal}
<script type="text/javascript">
			runAllForms();

			$('.carousel.fade').carousel({
				interval : 3000,
				cycle : true
			});

			$(function() {
				// Validation
				$("#login-form").validate({
					// Rules for form validation
					rules : {
						idnumber : {
							required : true,
							minlength : 3
						},
						password : {
							required : true,
							minlength : 3,
							maxlength : 20
						}
					},

					// Messages for form validation
					messages : {
						idnumber : {
							required : 'Please enter your username'
						},
						password : {
							required : 'Please enter your password'
						}
					},

					// Do not change code below
					errorPlacement : function(error, element) {
						error.insertAfter(element.parent());
					}
				});
			});
		</script>
{/literal}		
{/block}
{block name=title}Communicator{/block}
{block name=meta_image}/assets/img/logo-o.png{/block}