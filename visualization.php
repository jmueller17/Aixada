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

	 function do_plot(tag, data, max_val, legend) {
		 var  n = data[0].length,
		      w = 800,
		      h = 500,
		      x = d3.scale.linear().domain([0, 52]).range([0, w]),
		      y = d3.scale.linear().domain([0, max_val]).range([h, 0]),
		      p = 30;

		  var vis = d3.select(tag)
		      .data([data])
		      .append("svg:svg")
		      .attr("width", w + p * 2)
		      .attr("height", h + p * 2)
		      .append("svg:g")
		      .attr("transform", "translate(" + 2*p + "," + p + ")");

		  var rules = vis.selectAll("g.rule")
		      .data(x.ticks(15))
		      .enter().append("svg:g")
		      .attr("class", "rule");

		  // Draw grid lines
		  rules.append("svg:line")
		      .attr("x1", x)
		      .attr("x2", x)
		      .attr("y1", 0)
		      .attr("y2", h - 1);

		  rules.append("svg:line")
		      .attr("class", function(d) { return d ? null : "axis"; })
		      .data(y.ticks(10))
		      .attr("y1", y)
		      .attr("y2", y)
		      .attr("x1", 0)
		      .attr("x2", w - 10);

		  // Place axis tick labels
		  rules.append("svg:text")
		      .attr("x", x)
		      .attr("y", h + 15)
		      .attr("dy", ".71em")
		      .attr("text-anchor", "middle")
		      .text(x.tickFormat(10))
		      .text(String);

		  rules.append("svg:text")
		      .data(y.ticks(12))
		      .attr("y", y)
		      .attr("x", -10)
		      .attr("dy", ".35em")
		      .attr("text-anchor", "end")
		      .text(y.tickFormat(5));
		  
		  var colors = ['blue', 'magenta', 'lightsalmon', 'chartreuse', 'mediumvioletred'];

		  for (var i=0; i<n; i++) {
		      vis.append("svg:path")
			  .attr("class", "line")
			  .attr("fill", "none")
			  .attr("stroke", colors[i % colors.length])
			  .attr("stroke-width", 2)
			  .attr("d", d3.svg.line()
				.x(function(d) { return x(d[0]); })
				.y(function(d) { return y(d[i+1]); }));

		      vis.select("circle.line")
			  .data(data)
			  .enter().append("svg:circle")
			  .attr("class", "line")
			  .attr("fill", colors[i % colors.length] )
			  .attr("cx", function(d) { return x(d[0]); })
			  .attr("cy", function(d) { return y(d[i+1]); })
			  .attr("r", 10);
		  }

		  vis.append("svg:text")
		      .attr("x", w/4)
		      .attr("y", 20)
		      .text("Evolution of prices");

		  for (var i=0; i<n; i++) {
		      vis.append("svg:rect")
			  .attr("x", w/2 - 150)
			  .attr("y", 50 + 30*i)
			  .attr("stroke", colors[i % n])
			  .attr("height", 2)
			  .attr("width", 40);

		      vis.append("svg:text")
			  .attr("x", w/2 - 100)
			  .attr("y", 55 + 30*i)
			  .attr("stroke", colors[i % n])
			  .text(legend[i]);
		  }
	     }
	
function data_to_plot(data, key_tag, val_tag, n_pts=52) {
		  var max_val = 0.0,
		      m = data.length,
		      plot = new Array(n_pts);
		  for (var w=0; w<n_pts; w++) {
		      plot[w] = new Array(m+1);
		      plot[w][0] = w;
		  }
		  for (var i=0; i<m; i++) {
		      var pts = data[i][1],
			  j = 0,
			  w = 0,
			  value = 0;

		      for (; w<n_pts && j<pts.length; w++) {
			  if (pts[j][key_tag] <= w) {
			      value = pts[j][val_tag];
			      j++;
			  }
			  plot[w][i+1] = value;
			  if (value > max_val) {
			      max_val = value;
			  }
		      }
		      while (w<n_pts) {
			  plot[w][i+1] = value;
			  w++;
		      }
		  }
		  return { 'max_val': max_val, 'plot': plot };
   }

      $(function(){
	      d3.json("php/ctrl/Statistics.php?oper=product_prices_times_years&product_id_array[]=1198&product_id_array[]=1078&year_array[]=2011&year_array[]=2012",
	      function(data) {
		  $('#pty_graphic .loadSpinner').hide();
		  var m = data.length;
		  var plot_data = data_to_plot(data, 'week', 'price');
		  var legend = new Array(m);
		  for (var i=0; i<m; i++) {
		      legend[i] = data[i][0][0] + ' ' + data[i][0][1] + ' ' + data[i][0][2];
		  }
		  
		  do_plot('#pty_graphic', plot_data['plot'], plot_data['max_val'], legend);
		  
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