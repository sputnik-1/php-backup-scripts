#! /usr/bin/php

<?php

# PHP test script to start mysql database server

#------------------------------------------------------#

# start the mysql database server
$bash_command = `/etc/init.d/mysqld start`;
echo "$bash_command \n";

?>
