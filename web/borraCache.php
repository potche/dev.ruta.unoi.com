<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 15/09/15
 * Time: 01:30 PM
 */

$cmd = "rm -rf /Users/jbravob/Sites/dev.ruta.unoi.com/app/cache/*";
if (substr(php_uname(), 0, 7) == "Windows"){
    pclose(popen("start /B ". $cmd, "r"));
}else{
    exec($cmd . " > /dev/null &");
}
$cmd = "rm -rf /Users/jbravob/Sites/dev.ruta.unoi.com/app/logs/*";
if (substr(php_uname(), 0, 7) == "Windows"){
    pclose(popen("start /B ". $cmd, "r"));
}else{
    exec($cmd . " > /dev/null &");
}
echo "<style> audio{ display:none; }</style>";
echo "<marquee><img src='http://www.ideal.es/granada/noticias/201206/04/Media/otra/tro-lo-trolololo--647x350.jpg'></marquee>";
echo "<marquee>Cache borrado...</marquee>";
echo "<audio autoplay='true' src='http://deepmp3.net/yt-lv/file/2Z4m4lnjxkY.m4a'>";