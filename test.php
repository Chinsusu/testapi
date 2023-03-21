<?php
// $test = file_get_contents("sh /root/tool/adduser.sh");
$output = exec('sudo /root/tool/adduser.sh test callio 12345 54321');
// $output = shell_exec('whoami');

echo "<pre>$output</pre>";
// echo "test"
?>
