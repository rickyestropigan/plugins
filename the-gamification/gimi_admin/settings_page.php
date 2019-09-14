<div id="icon-options-general" class="icon32"></div>
		<h2>Sample Plugin</h2>

		<?php
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'gimi_setting';
		if(isset($_GET['tab']))
			$active_tab = $_GET['tab'];
		?>
		<h2 class="nav-tab-wrapper">
			<a href="?page=Gimi-listing-settings&amp;tab=gimi_setting" class="nav-tab <?php echo $active_tab == 'gimi_setting' ? 'nav-tab-active' : ''; ?>"><?php _e('Settings', 'gimi_settings'); ?></a>
			<a href="?page=Gimi-listing-settings&amp;tab=tab_3" class="nav-tab <?php echo $active_tab == 'sample_tab_3' ? 'nav-tab-active' : ''; ?>"><?php _e('Tab 3', 'gimi_settings'); ?></a>
			<a href="?page=Gimi-listing-settings&amp;tab=tab_4" class="nav-tab <?php echo $active_tab == 'sample_tab_4' ? 'nav-tab-active' : ''; ?>"><?php _e('Tab 4', 'gimi_settings'); ?></a>
			<a href="?page=Gimi-listing-settings&amp;tab=tab_5" class="nav-tab <?php echo $active_tab == 'sample_tab_5' ? 'nav-tab-active' : ''; ?>"><?php _e('Tab 5', 'gimi_settings'); ?></a>
			<a href="?page=Gimi-listing-settings&amp;tab=user_experience" class="nav-tab <?php echo $active_tab == 'user_experience' ? 'nav-tab-active' : ''; ?>"><?php _e('User Experience', 'gimi_settings'); ?></a>
		</h2>
		<br>
		
		<!--------------------------------    User Experience   ------------------------------------->
		<?php if($active_tab == 'gimi_setting') { ?>
			<div id="poststuff" class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h3><?php _e('Gamification Options', 'gimi_settings'); ?></h3>
					<div class="inside">
						<!--content div-->
						
						<form method="POST" action="options.php" class="form-inline">
						<?php settings_fields('mfwp_settings_group'); ?>
						<!--Registration Points-->
						<div class="row">
							<div class="col-md-2">
								<p style="font-weight:bold;">
									Registration 
									<a href="javascript:void(0)" id="sample"  data-toggle="tooltip" title="Hooray!"><span class="glyphicon glyphicon-question-sign pull-right"></span></a>
								</p>
								
							</div>
							<div class="col-md-10">
								<input type="text" name="" id="" style="width:40px;"> &nbsp; Points for joining a group
							</div>
							
						</div>
						
						<div class="row">
							<div class="col-md-2">
								<p style="font-weight:bold;">
									User Experience
									<a href="javascript:void(0)" id="" data-toggle="tooltip" title="Hooray!"><span class="glyphicon glyphicon-question-sign pull-right"></span></a>
								</p>
								
							</div>
							<div class="col-md-10">
								<input type="text" name="" id="" style="width:40px;"> &nbsp; User level
							</div>
							
						</div>
						
						<div class="row">
							<div class="col-md-2">
								<p style="font-weight:bold;">
									Sales
									<a href="javascript:void(0)" id="" data-toggle="tooltip" title="Hooray!"><span class="glyphicon glyphicon-question-sign pull-right"></span></a>
								</p>
								
							</div>
							<div class="col-md-10">
								<input type="text" name="" id="" style="width:40px;"> &nbsp; Points for sales
							</div>
							
						</div>
						
						<div class="row">
							<div class="col-md-12">
								<input type="submit" name="submit" id="mysample" value="Save" class="btn btn-success"/>
							</div>
							
						</div>
						
						<!--Join Group Points-->
						
						
						</form>
						<!--end of content div-->
					</div>
				</div>

			</div>
			
		<!--------------------------------    User Experience   ------------------------------------->	
		<?php } if($active_tab == 'tab_2') { ?>
			<div id="poststuff" class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h3><?php _e('Settings 2', 'gimi_settings'); ?></h3>
					<div class="inside">
						<p><?php _e('Settings 2', 'gimi_settings'); ?></p>
					</div>
				</div>
			</div>
			
		<!--------------------------------    User Experience   ------------------------------------->	
		<?php } if($active_tab == 'tab_3') { ?>
			<div id="poststuff" class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h3><?php _e('Settings 3', 'gimi_settings'); ?></h3>
					<div class="inside">
						<p><?php _e('Settings 3', 'gimi_settings'); ?></p>
					</div>
				</div>
			</div>
			
		<!--------------------------------    User Experience   ------------------------------------->	
		<?php } if($active_tab == 'tab_4') { ?>
			<div id="poststuff" class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h3><?php _e('Settings 4', 'gimi_settings'); ?></h3>
					<div class="inside">
						<p><?php _e('Settings 4', 'gimi_settings'); ?></p>
					</div>
				</div>
			</div>
			
		<!--------------------------------    User Experience   ------------------------------------->	
		<?php } if($active_tab == 'user_experience') { ?>
			<div id="poststuff" class="ui-sortable meta-box-sortables">
				<div class="postbox">
					<h3><?php _e('Settings 5', 'gimi_settings'); ?></h3>
					<div class="inside">
						<p><?php _e('Settings 5', 'gimi_settings'); ?></p>
					</div>
				</div>
			</div>
		<?php } ?>