<?php
	$user_id = $this->Security->user_id();
	$user = $this->Security->user();
	$this->log("menu.user=" . var_export($user, true), 'debug');
	$user_name = "";
	$greetings = "";
	if($user){
		$user_name = $user['usr_username'];
		$greetings = $this->Html->link($user_name, array('controller' => 'users', 'action' => 'profile', $user_id), 
						array('class' => 'fn-top-bar-greetings')) . ',';
	}
	
	//$user_id = false;
	if(!isset($sidebar))
		$sidebar = "";
?>
<div class="topbar js-topbar">
  <div id="banners" class="js-banners">
  </div>
  <div class="global-nav" data-section-term="top_nav">
    <div class="global-nav-inner">
      <div class="container">
      	   <div class="google-nexus sidebar">
		<?php echo $sidebar;?>
	  </div>          
          <h1 class="foot-nation-logo-icon" style="display: inline-block; width: 24px; height: 21px;">
           <span>z</span>
            <span class="visuallyhidden">FootNation</span>
          </h1>
	  <?php
		if(!$user_id){
		?>
		<div class="top-bar-login-join">
			<a href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'register'));?>" class="join"><?php echo __("Join Free");?></a>
			<span class="h-separator"></span>
			<a href="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'login'));?>"  class="signin"><?php echo __("Sign in");?></a>
		</div>
		<?php
		}
	  ?>
          <div class="pushstate-spinner"></div>    
            <div role="navigation" style="display: inline-block;">
            	<ul class="nav js-global-actions" id="global-actions">
            		<?php
				$items = $menuItems['items'];
				$current_uri = $this->request->here;
				foreach($items as $menuName => $menuItem){
					$target = $menuItem['target'];
					$action = $menuItem['action'];
					$menuIcon = $menuItem['icon'];
					$is_active = strstr($current_uri, $target);
					$activeClass = $is_active? " active" : "";
			?>
            		<li id='global-nav-<?php echo $action;?>' class="<?php echo $action . $activeClass ;?>" data-global-action='<?php echo $action;?>'> 
            			<a class="js-nav js-tooltip" href="<?php echo $target;?>" 
            			     data-component-term="<?php echo $action;?>_nav" data-nav="<?php echo $action;?>"
            			    title="<?php echo $menuName;?>"> 
            			    	<span class="menu-item-icon"><?php echo $menuIcon;?></span>
            				<span class="new-wrapper"><i></i><i class="nav-new"></i></span> 
            				<span class="text"><?php echo $menuName;?></span>  </a> 
            		</li>
            		<?php
            			}
            		?>
            	</ul>
            </div>
          <div class="pull-right" style="display: inline-block;"> <div role="search">
		  <form class="form-search js-search-form"  id="global-nav-search">
			<label class="visuallyhidden" for="search-query">Search query</label>
			<input class="search-input" type="text" id="search-query" placeholder="Search" name="q" autocomplete="off" spellcheck="false" aria-autocomplete="list" aria-haspopup="true" aria-controls="typeahead-dropdown-1">
			<span class="search-icon js-search-action">
				  <button type="submit" class="icon nav-search" tabindex="-1">
					<span class="visuallyhidden">Search</span>
				  </button>
			</span>
			<input disabled="disabled" class="search-input search-hinting-input" type="text" id="search-query-hint" autocomplete="off" spellcheck="false">
	  

		  </form>
	</div> 
	<?php
		if($user_id){
		?> 
 	<i class="topbar-divider"></i> 
	<ul class="nav bootstrap"> 
		<li class="me dropdown session js-session" id="user-dropdown"> 
			<a href="/settings/account" 
			     class="js-tooltip dropdown-toggle js-dropdown-toggle"  
			     data-toggle="dropdown" id="user-dropdown-toggle" 
			     title="Settings and help" 
			     data-placement="bottom" 
			     role="button" aria-haspopup="true">  
			     <span class="new-wrapper">
			     	<span class="icon nav-session">
			     		<span class="visuallyhidden">Settings and help</span>
			     	</span>
			       <span class="icon nav-new"></span>
			    </span>
			    <span class="caret"></span>
			</a>  
			<ul class="dropdown-menu pull-right" role="menu">
				  <li class="current-user" data-name="profile">
					<a href="/users/profile" class="account-summary account-summary-small" data-nav="edit_profile">
					  <div class="content">
						<div class="account-group js-mini-current-user">
						  <?php echo $this->Format->image($user['per_img_id'], 32, 32, array('class' => 'user-icon')); ?>
						  <b class="fullname"><?php echo $this->Format->name($user_id); ?></b>
						  <span class="screen-name hidden" dir="ltr"><?php echo $this->Format->username($user_id); ?></span>
						  <small class="metadata">
							  <?php echo __("Edit profile");?>
						  </small>
						</div>
					  </div>
					</a>
				  </li>
				<li class="dropdown-divider"></li>
				<li><a href="/pages/help" data-nav="help_center"><?php echo __("Help");?></a></li>
				<li class="dropdown-divider"></li>
				<li><a href="/users/settings" data-nav="settings" class="js-nav"><?php echo __("Settings");?></a></li>
				<li class="js-signout-button" id="signout-button" data-nav="logout">
					<a href="/users/logout" data-nav="settings" class="js-nav"><?php echo __("Sign Out");?></a></li>
				</li>
			</ul>
		</li> 
	</ul>
	<?php
	}
	?> 
    </div>
  </div>
</div>
</div></div>