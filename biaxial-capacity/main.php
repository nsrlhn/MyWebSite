<?php
$N = filter_input(INPUT_GET, "N");
$b = filter_input(INPUT_GET, "b");
$h = filter_input(INPUT_GET, "h");
$fck = filter_input(INPUT_GET, "fck");
$k1 = filter_input(INPUT_GET, "k1");
$e_cu = filter_input(INPUT_GET, "e_cu");
$xs = explode(",",filter_input(INPUT_GET, "xs"));
$ys = explode(",",filter_input(INPUT_GET, "ys"));
$As = explode(",",filter_input(INPUT_GET, "As"));
$fyk = filter_input(INPUT_GET, "fyk");
$Es = filter_input(INPUT_GET, "Es");
$e_su = filter_input(INPUT_GET, "e_su");
$steps = filter_input(INPUT_GET, "steps");

$result = getMxMy($steps/4,$N,$b,$h,$fck,$k1,$e_cu,$xs,$ys,$As,$fyk,$Es,$e_su);
$Mx_1 = $result[0];
$My_1 = $result[1];

$xs_2 = minus($ys);
$ys_2 = $xs;
$result = getMxMy($steps/4,$N,$b,$h,$fck,$k1,$e_cu,$xs_2,$ys_2,$As,$fyk,$Es,$e_su);
$Mx_2 = array_reverse($result[1]);
$My_2 = array_reverse(minus($result[0]));

$xs_3 = minus($ys_2);
$ys_3 = $xs_2;
$result = getMxMy($steps/4,$N,$b,$h,$fck,$k1,$e_cu,$xs_3,$ys_3,$As,$fyk,$Es,$e_su);
$Mx_3 = minus($result[0]);
$My_3 = minus($result[1]);

$xs_4 = minus($ys_3);
$ys_4 = $xs_3;
$result = getMxMy($steps/4,$N,$b,$h,$fck,$k1,$e_cu,$xs_4,$ys_4,$As,$fyk,$Es,$e_su);
$Mx_4 = array_reverse(minus($result[1]));
$My_4 = array_reverse($result[0]);

$Mx_sum = array_merge($Mx_1,$Mx_2,$Mx_3,$Mx_4);
$My_sum = array_merge($My_1,$My_2,$My_3,$My_4);

function minus($arr){
    for ($i=0; $i < count($arr); $i++) { 
        $r[$i] = -$arr[$i];
    }
    return $r;
}

function steelForce($fyk,$Es,$e_su,$h,$b,$ky,$teta,$xs,$ys,$As,$e_cu,$c){
    $e_sy= $fyk/$Es;
    if ($c == 0) {
        return [0,0,0];
    }
    $P=0;
    $Fs=0;
    $Msy=0;
    $Msx=0;
    $teta_radyan = deg2rad($teta);
    for ($i=0; $i<count($ys); $i++) { 
        $P=-$xs[$i]*sin($teta_radyan)+$ys[$i]*cos($teta_radyan)-$b/2*sin($teta_radyan)+($ky*$h-$h/2)*cos($teta_radyan);
        $e_s=$e_cu/$c*$P;
        if (abs($e_s) <= $e_sy ) {
            $sigma_s = $Es*$e_s;
        } else if ($e_sy<abs($e_s) && abs($e_s)<=$e_su) {
            $sigma_s = $fyk*$e_s/abs($e_s);
        } else {
            $sigma_s = 0;
        }
    $F=$sigma_s*$As[$i];
    $Msy=$Msy+$F*$xs[$i];
    $Msx=$Msx+$F*$ys[$i];
    $Fs=$Fs+$F;
    }
    return array($Fs,$Msx,$Msy);
}
function shadedArea($h,$b,$ky,$teta,$k1){
    $teta_radyan = deg2rad($teta);
    $x=$k1*$ky*$h;
    if ($x < $h) {
        if ($b*tan($teta_radyan) < $x) {
            $Ac=$b*$x-$b**2*tan($teta_radyan)/2;
            $k=$x-$b*tan($teta_radyan);
            $yc=$h/2-($b*$k*$k/2+$b/2*($x-$k)*(($x-$k)/3+$k))/$Ac;
            $xc=($b*$k*$b/2+($x-$k)*$b/2*$b/3)/$Ac-$b/2;
            #Case=1
        } else {
            $Ac=($x)**2/tan($teta_radyan)/2;
            $yc=$h/2-$x/3;
            $xc=$x/tan($teta_radyan)/3-$b/2;
            #Case=2
        }
    } else {
        if ($b*tan($teta_radyan) < $x) {
            $k=$x-$b*tan($teta_radyan);
            if ($k > $h) {
                $Ac=$b*$h;
                $yc=0;
                $xc=0;
            } else {
                $Ac=$b*$h-($h-$k)**2/tan($teta_radyan)/2;
                $yc=($k*($h-$k)/tan($teta_radyan)*($h/2-$k/2)+(2/3*($h-$k)-$h/2)*($h-$k)**2/tan($teta)/2)/$Ac;
                $x2=$b-($h-$k)/tan($teta_radyan);
                $xc=(($h-$k)*$x2*($x2/2-$b/2)+($h-$k)**2/tan($teta_radyan)/2*($b/2-2/3*($h-$k)/tan($teta)))/$Ac;
            }
         #case=3   
        } else {
            $Ac=$h**2*(2*$x/$h-1)/tan($teta_radyan)/2;
            $yc=(($h/2-$h/3)*$h**2/2/tan($teta_radyan))/$Ac;
            $xc=($h*($x-$h)/tan($teta_radyan)*(($x-$h)/tan($teta_radyan)-$b)/2+$h**2/2/tan($teta_radyan)*($h/tan($teta_radyan)/3+($x-$h)/tan($teta_radyan)-$b/2))/$Ac;
            #Case=4
        }
        
    }
    return array($Ac,$xc,$yc);
}



#$N_max=0.85*fck*b*h+sum(As)*365;
#$N_min=0;
#$loop1=50;
#$N = numpy.arange(N_min, N_max, (N_max-N_min)/(loop1-1));

function getMxMy($steps,$N,$b,$h,$fck,$k1,$e_cu,$xs,$ys,$As,$fyk,$Es,$e_su){

    $error=0.000001;
    if ($N*$error > 1) {
        $errorlimit = $N*$error;
    } else {
        $errorlimit = 1;
    }

    $teta_max=90;
    $teta_min=0;
    $teta=$teta_min;
    $Mx = array();
    $My = array();
    for ($i=0; $i < $steps; $i++) {
        $teta_radyan = deg2rad($teta);
        if (abs($teta) < 10) {
            $ky_max=1;
        } else {
            $ky_max=4;
        }
        $ky_min=0;
        $ky=($ky_max+$ky_min)/2;
        $c = $ky*$h*cos($teta_radyan);
        $sf = steelForce($fyk,$Es,$e_su,$h,$b,$ky,$teta,$xs,$ys,$As,$e_cu,$c);
        $Fs = $sf[0];
        $Msx = $sf[1];
        $Msy = $sf[2];
        $sa = shadedArea($h,$b,$ky,$teta,$k1);
        $Ac = $sa[0];
        $xc = $sa[1];
        $yc = $sa[2];
        $Fc = 0.85*$fck*$Ac;
        $error=$N-$Fc-$Fs;
        $j=0;
        while (abs($error)>$errorlimit) {
            if ($error<0) {
                $ky_max=$ky;
                $ky=0.5*($ky_min+$ky);
            } else {
                $ky_min=$ky;
                $ky=0.5*($ky+$ky_max);
            }
            $c = $ky*$h*cos($teta_radyan);
            $sf = steelForce($fyk,$Es,$e_su,$h,$b,$ky,$teta,$xs,$ys,$As,$e_cu,$c);
            $Fs = $sf[0];
            $Msx = $sf[1];
            $Msy = $sf[2];
            $sa = shadedArea($h,$b,$ky,$teta,$k1);
            $Ac = $sa[0];
            $xc = $sa[1];
            $yc = $sa[2];
            $Fc = 0.85*$fck*$Ac;
            $error=$N-$Fc-$Fs; #sum(Fs) !!!!!!!!!!!!
            $j=$j+1;
            if ($j > 10000) {
                break;
            }
        }
        if ($ky > 3.9) {
            $Mx[$i]= 0;
            $My[$i]= 0;
        } else if ($j > 999) {
            $Mx[$i]= 0;
            $My[$i]= 0;
        } else {
            $Mx[$i]= ($Fc*$yc+$Msx)/1000000; #kNm
            $My[$i]= ($Fc*$xc+$Msy)/1000000; #kNm
        }
        $teta=$teta+($teta_max-$teta_min)/$steps;
    }
    return array(minus($Mx),$My);
}

?>
<span id="tooltip" display="none" style="position: absolute; display: none;background-color:rgba(255, 255, 255, 0.7);padding: 2px;"></span>
<svg width="100%" height=100vw> Your browser does not support the svg element.
    <line id="x_axis" x1=0 y1=50% x2=100% y2=50% style="stroke:black;stroke-width:1" />
    <line id="y_axis" x1=50% y1=0 x2=50% y2=100% style="stroke:black;stroke-width:1" />

<?php
    $Mx_max = max(array_map('abs', $Mx_sum));
    $My_max = max(array_map('abs', $My_sum));
    for ($i = 0; $i < $steps; $i++) {
        $x = 50+$Mx_sum[$i]/$Mx_max*40;
        $y = 50-$My_sum[$i]/$My_max*40;
        $text = "'( ".number_format($Mx_sum[$i],5)." , ".number_format($My_sum[$i],2)." )'";
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