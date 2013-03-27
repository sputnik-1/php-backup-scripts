#! /usr/bin/php

<?php

# PHP test script to start apache web server

#------------------------------------------------------#

# output the current version of apache
$bash_command = `apachectl -v`;
echo "$bash_command \n";

# start the apache web server
$bash_command = `apachectl start`;
echo "$bash_command";
echo "Starting Apache web server... \n\n";

?>
