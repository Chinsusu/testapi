<?php
$conf = "/etc/3proxy/conf/3proxy.cfg";
$myfile = fopen($conf, "r") or die("Unable to open file!");
echo fread($myfile,filesize($conf));
fclose($myfile);
?>
