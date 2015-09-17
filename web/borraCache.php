<?php
/**
 * Created by PhpStorm.
 * User: isra
 * Date: 15/09/15
 * Time: 01:30 PM
 */

$cmd = "rm -rf /var/www/html/dev.evaluaciones.unoi.com/app/cache/*";
if (substr(php_uname(), 0, 7) == "Windows"){
    pclose(popen("start /B ". $cmd, "r"));
}else{
    exec($cmd . " > /dev/null &");
}
$cmd = "rm -rf /var/www/html/dev.evaluaciones.unoi.com/app/logs/*";
if (substr(php_uname(), 0, 7) == "Windows"){
    pclose(popen("start /B ". $cmd, "r"));
}else{
    exec($cmd . " > /dev/null &");
}
echo "<marquee>Cache Borrado.....</marquee>";