#! /usr/bin/php

<?php

# PHP test script to shutdown mysql database server

#------------------------------------------------------#

# stop the mysql database server
$bash_command = `/etc/init.d/mysqld stop`;
echo "$bash_command";

?>
