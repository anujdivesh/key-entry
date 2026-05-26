<?php

$dry = 25.4;
$wet = 23.6;

function rh_calculator($dry, $wet){
    $a=240.97 + $dry;
    $b = 17.502 * $dry; 
    $c = $b/$a;
    $d = pow(2.71828,$c);
    $e = 6.112 * $d;
    
    
    $a=240.97 + $wet;
    $b = 17.502 * $wet; 
    $c = $b/$a;
    $d = pow(2.71828,$c);
    $f = 6.112 * $d;

    $g = ($f - .66875 * (1 + .00115 * $wet) * ($dry - $wet))/ $e;
    echo round($g*100,0);
}

rh_calculator($dry, $wet);


?>