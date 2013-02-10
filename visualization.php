<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['ti_visualization'];?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
  
    <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
   	<?php  } else { ?>
	    <script type="text/javascript" src="js/js_for_visualization.min.js"></script>
    <?php }?>
   
	<script type="text/javascript">
	
      $(function(){
	      $.ajax({
		  type: "POST",
			  url: "php/ctrl/Statistics.php?oper=product_prices_times_years&product_id_array[]=861&product_id_array[]=647&year_array[]=2011&year_array[]=2012",
			  beforeSend : function (){
			  $('#pty_graphic .loadSpinner').show();
		      },
			  success: function(data) {
		      }, 
			  error : function(XMLHttpRequest, textStatus, errorThrown) {
			  alert(textStatus);
		      },
			  complete : function(msg){
			  $('#pty_graphic .loadSpinner').hide();
		      }
		  }); //end ajax
			  

      });  //close document ready
</script>
</head>
<body>
<div id="wrap">
  <div id="headwrap">
    <?php include "php/inc/menu.inc.php" ?>
  </div>
  <!-- end of headwrap -->
	
  <div id="stagewrap">
    <div id="titlewrap" class="ui-widget">
      <div id="titleLeftCol">
        <h1><?php echo $Text['ti_visualization']; ?></h1>
	<div id="products_times_years">
	  <div id="pty_graphic"><img class="loadSpinner" src="img/ajax-loader.gif"/>
	  </div>
	</div>
      </div>
    </div>
  </div>
  <!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>