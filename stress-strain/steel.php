<?php

    $fyk = filter_input(INPUT_GET, "fyk");
    $Es = filter_input(INPUT_GET, "Es");
    $epsilon_su = filter_input(INPUT_GET, "epsilon_su");
    $epsilon_sy= $fyk/$Es;
    $steps_s = filter_input(INPUT_GET, "steps_s");
    $increment = $epsilon_su*2.1/$steps_s;

    $sigma = [];
    $epsilon = [];
    for ($i = 0; $i < $steps_s; $i++) {
        $sigma[$i] = 0;
        $epsilon[$i] = 0;
    }
    $epsilon_s= -$epsilon_su*1.05;
    $sigma_c = 0;
    for ($i = 0; $i < $steps_s; $i++) {
        if (abs($epsilon_s) <= $epsilon_sy ) {
          $sigma_s = $Es*$epsilon_s;
        } else if (($epsilon_sy < abs($epsilon_s)) && (abs($epsilon_s) <= $epsilon_su)) {
          $sigma_s = $fyk*$epsilon_s/abs($epsilon_s);
        } else { 
          $sigma_s = 0;
        }
        $sigma[$i] = $sigma_s;
        $epsilon[$i] = $epsilon_s;
        $epsilon_s = $epsilon_s+$increment;
    }
?>
<span id="tooltip" display="none" style="position: absolute; display: none;background-color:rgba(255, 255, 255, 0.7);padding: 2px;"></span>
<svg id="svg_steel" width="100%" height=100vw> Your browser does not support the svg element.
    <line id="x_axis" x1=0 y1=50% x2=100% y2=50% style="stroke:black;stroke-width:1" />
    <line id="y_axis" x1=50% y1=0 x2=50% y2=100% style="stroke:black;stroke-width:1" />

<?php
    $gap_x = 100/($steps_s);
    $gap_y = 100/2/$fyk/1.2;
    for ($i = 0; $i < $steps_s; $i++) {
        $x = $gap_x*$i;
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