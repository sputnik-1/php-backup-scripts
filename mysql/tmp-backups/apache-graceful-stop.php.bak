#! /usr/bin/php

<?php

# PHP test script to stop apache web server

#------------------------------------------------------#

# seconds to wait for apache to shutdown
$APACHE_GRACE_TIME=10;

# output the current version of apache
$bash_command = `apachectl -v`;
echo "$bash_command \n";

# gracefully shut-down the apache web server
$bash_command = `apachectl graceful-stop`;
echo "$bash_command";
echo "Gracefully shutting down Apache web server...\n\n";

echo "Waiting $APACHE_GRACE_TIME seconds for Apache web server to finish...\n\n";

# wait for apache to shutdown properly
sleep($APACHE_GRACE_TIME);

echo "Apache shutdown OK! \n\n";

?>
