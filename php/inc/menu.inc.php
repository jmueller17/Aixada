<div class="container">
	<div class="row">
      	<!-- Static navbar -->
      	<div class="navbar navbar-default" role="navigation">
        	<div class="container-fluid">
          		<div class="navbar-header">
            		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		              <span class="sr-only">Toggle navigation</span>
		              <span class="icon-bar"></span>
		              <span class="icon-bar"></span>
		              <span class="icon-bar"></span>
		            </button>
            		<a class="navbar-brand" href="#"><?=$Text['coop_name'];?> </a>
          		</div>

          		<div class="navbar-collapse collapse">
	            	<?php 

	            		try {
	            			$nav = new menu(configuration_vars::get_instance()->main_nav);
	            			echo $nav->get_menu(); 
	            		} catch(Exception $e) {
	   						header('HTTP/1.0 401 ' . $e->getMessage());
	    					die ($e->getMessage());
						} 

	            	?>	
            
				    <ul class="nav navbar-nav navbar-right">
				       
				        <li class="dropdown">
				          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> &nbsp;
				          	<?php 
				          		echo "Hola " . $_SESSION['userdata']['login'] . " (".$Text['uf_short'].$_SESSION['userdata']['uf_id'] .")";
				          	?> <b class="caret"></b>
				          </a>
				          <ul class="dropdown-menu">
				          	<?php
		 						foreach ($_SESSION['userdata']['roles'] as $role) {
		       						echo '<li><a href="'.$role.'">';
		       							$rt = (isset($Text[$role]) ? $Text[$role] : "TRANSLATE[$role]");
		       								if ($role == $_SESSION['userdata']['current_role']){
			 									echo '<span class="glyphicon glyphicon-check"></span>';
			 								}
		    								echo ' ' . $rt . '</a></li>';
		    					}		
		    				?>
				            <li class="divider"></li>
							<li><a href="manage_mysettings.php"><?php echo $Text['nav_myaccount_settings'];?></a></li>
							<li><a href="manage_mysettings.php?what=pwd"><?php echo $Text['nav_changepwd'];?></a></li>
							<li><a href="report_account.php?what=my_account"><?php echo $Text['nav_myaccount_account'];?></a></li>		
				            <li class="divider"></li>
				            <li><a href="#"><span class="glyphicon glyphicon-log-out"></span> <?=$Text['nav_logout'];?></a></li>
				          </ul>
				        </li>
				     </ul>
          
          		</div><!--/.nav-collapse -->
        	</div><!--/.container-fluid -->
      	</div>
  	</div>
 </div>


