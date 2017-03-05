<?php include "php/inc/header.inc.php" ?>
<?php include "php/utilities/statistics.php"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_stats'] ;?></title>

	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    

   <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<?php echo aixada_js_src(); ?>

</head>

    <body>
        <div id="wrap">
      		<div id="headwrap">
      			<?php include "php/inc/menu.inc.php" ?>
      		</div>
      
	
	
      
      		<div id="stagewrap">
      
      
      			<div id="titlewrap">
      				<h1><?php echo $Text['ti_timeline']; ?> </h1>
          		</div>
          
          
	          	<div id="timeline">
     <?php echo make_active_time_lines($_REQUEST['oper'])?>
    	  	  	</div>
    	  	</div>
      <!-- end of stage wrap -->
	</div>
<!-- end of wrap -->
<!-- / END -->
</body>
</html>			
		
