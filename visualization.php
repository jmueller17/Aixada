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
		<script type="text/javascript" src="js/d3.v3.min.js"></script>
   	<?php  } else { ?>
	    <script type="text/javascript" src="js/js_for_visualization.min.js"></script>
    <?php }?>
   
	<script type="text/javascript">
	
      $(function(){
	      d3.json("php/ctrl/Statistics.php?oper=product_prices_times_years&product_id_array[]=861&product_id_array[]=647&year_array[]=2011&year_array[]=2012",
	      function(data) {
		  $('#pty_graphic .loadSpinner').hide();
		  gymax = 0.0;
		  var all = new Array(365);
		  for (var d=0; d<365; d++) {
		      all[d] = new Array(data.length);
		  }
		  for (var i=0; i<data.length; i++) {
		      var pts = data[i][1];
		      for (var j=0; j<pts.length; j++) {
			  var price = pts[j]['price'];
			  all[pts[j]['day']][j] = price;
			  if (price > gymax) {
			      gymax = price;
			  }
		      }
		  }

		      /*
		  for (var i=0; i<data.length; i++) {
		      var plot = data[i],
			  product_id = plot[0][0],
			  year = plot[0][1],
			  pts = plot[1],

		      var pts = data[i][1],
			  lymax = d3.max(pts, function(d) { return d['price']; });
		      if (lymax > gymax) {
			  gymax = lymax;
		      }
		  }
		      */
		  var  w = 800,
		      h = 500,
		      x = d3.scale.linear().domain([0, 365]).range([0, w]),
		      y = d3.scale.linear().domain([0, gymax]).range([h, 0]),
		      p = 30;

		  var vis = d3.select("#paired-line-chart")
		      .data([val_array1])
		      .append("svg:svg")
		      .attr("width", w + p * 2)
		      .attr("height", h + p * 2)
		      .append("svg:g")
		      .attr("transform", "translate(" + p + "," + p + ")");

		  
	      }); //end json
			  

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