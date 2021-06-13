<?php

	$fck = filter_input(INPUT_GET, "fck");
	$epsilon_co = filter_input(INPUT_GET, "epsilon_co");
	$epsilon_cu = filter_input(INPUT_GET, "epsilon_cu");
	$steps_c = filter_input(INPUT_GET, "steps_c");
	$increment = $epsilon_cu*1.5/$steps_c;

    $sigma = [];
    $epsilon = [];
    for ($i = 0; $i < $steps_c; $i++) {
    	$sigma[$i] = 0;
    	$epsilon[$i] = 0;
    }
    $epsilon_c = 0;
    $sigma_c = 0;
    for ($i = 0; $i < $steps_c; $i++) {
    	if (0 < $epsilon_c && $epsilon_c <= $epsilon_co) {
        	$sigma_c = 0.85*$fck*(2*($epsilon_c/$epsilon_co)-($epsilon_c/$epsilon_co)**2);
    	} else if ($epsilon_co < $epsilon_c && $epsilon_c <= $epsilon_cu) {
        	$sigma_c = 0.85*$fck-0.15*0.85*$fck*($epsilon_c-$epsilon_co)/($epsilon_cu-$epsilon_co);
        } else {
         	$sigma_c = 0;
       	}
       	$sigma[$i] = $sigma_c;
       	$epsilon[$i] = $epsilon_c;
        $epsilon_c = $increment + $epsilon_c;
    }
?>
<span id="tooltip" display="none" style="position: absolute; display: none;background-color:rgba(255, 255, 255, 0.7);padding: 2px;"></span>
<svg id="svg_concrete" width="100%" height=100vw> Your browser does not support the svg element.
    <line id="x_axis" x1=0 y1=50% x2=100% y2=50% style="stroke:black;stroke-width:1" />
    <line id="y_axis" x1=50% y1=0 x2=50% y2=100% style="stroke:black;stroke-width:1" />
<?php
    $gap_x = 100/2/$steps_c;
    $gap_y = 100/2/$fck;
    for ($i = 0; $i < $steps_c; $i++) {
    	$x = 50+$gap_x*$i;
    	$y = 50-$gap_y*$sigma[$i];
        $text = "'( ".number_format($epsilon[$i],5)." , ".number_format($sigma[$i],2)." )'";
        echo '<circle r=2 cx='.$x.'% cy='.$y.'% onmousemove="showTooltip(evt, '.$text.');" onmouseout="hideTooltip(evt);"/>';
    }
?>
    <script>
        var tooltip = document.getElementById("tooltip");
        function showTooltip(evt, text) {
          tooltip.innerHTML = text;
          tooltip.style.display = "block";
          tooltip.style.left = evt.pageX + 10 + 'px';
          tooltip.style.top = evt.pageY + 10 + 'px';
          evt.path[0].attributes["r"].value = 6;
        }
        function hideTooltip(evt) {
          tooltip.style.display = "none";
          evt.path[0].attributes["r"].value = 2;
        }
    </script>
</svg>