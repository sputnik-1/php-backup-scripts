#! /usr/bin/php

<?php

# last updated: Thu May 29 15:52:14 BST 2008

# PHP script to backup *all* MySQL databases to /backup-databases/mysql
# this is the version for a default Fedora 8 installation
# of mysql

#------------------------------------------------------#
# how many backups to keep before deleting the oldest backup
define("NUMBER_REQUIRED_BACKUPS", 7);

#------------------------------------------------------#
# source directory/files that need backing up.

# the source directory to be backed up.
$SOURCE_DIR="/var/lib/databases/mysql";

# the partition label id of the directory/files
# that need backing up. May not be required.
$SOURCE_LABEL_ID="NONE";

# the directory mountpoint of the directory/files
# that need backing up. May not be required.
$SOURCE_MOUNTPOINT="NONE";

echo "\$SOURCE_DIR is: $SOURCE_DIR";
echo"\n\n";

echo "\$SOURCE_LABEL_ID is: $SOURCE_LABEL_ID";
echo"\n\n";

echo "\$SOURCE_MOUNTPOINT is: $SOURCE_MOUNTPOINT";
echo"\n\n";

#------------------------------------------------------#
# destination for the backups

# the backup partition label id.
$BACKUP_LABEL_ID="database-backups";

# the backup partition directory mountpoint.
$BACKUP_MOUNTPOINT="/backup-databases";

# the backup group name
$BACKUP_GROUPNAME="/mysql";

# the backup group full pathname
$BACKUP_PATH = "{$BACKUP_MOUNTPOINT}{$BACKUP_GROUPNAME}";

echo "\$BACKUP_LABEL_ID is: $BACKUP_LABEL_ID";
echo"\n\n";

echo "\$BACKUP_MOUNTPOINT is: $BACKUP_MOUNTPOINT";
echo"\n\n";

echo "\$BACKUP_GROUPNAME is: $BACKUP_GROUPNAME";
echo"\n\n";

echo "\$BACKUP_PATH is: $BACKUP_PATH";
echo"\n\n";

#------------------------------------------------------#

function showMountedPartitions() {
  # display currently mounted partitions
  $bash_command = `df -h`;
  echo "Mounted Partitions: \n$bash_command \n";
}

showMountedPartitions();

# mount the backup destination partition by label id.
$bash_command = `mount -v -L $BACKUP_LABEL_ID`;
echo "$bash_command \n";

showMountedPartitions();

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

# stop the mysql database server
$bash_command = `/etc/init.d/mysqld stop`;
echo "$bash_command";

#############################################################
# NOTE: tar does not like filenames with a ':' (colon)
# so DO NOT use as part of the filename extension

$CURRENT_DATE=`date +'-%Y-%m-%d_%H.%M.%S'`;

echo "CURRENT_DATE is: " . $CURRENT_DATE;
echo"\n";

$DESTINATION_DIR="{$BACKUP_PATH}{$BACKUP_GROUPNAME}{$CURRENT_DATE}";

echo "DESTINATION_DIR IS: $DESTINATION_DIR";

# shell command to copy mysql databases to /backup-databases/mysql
$bash_command = `cp -R --preserve=mode,ownership $SOURCE_DIR $DESTINATION_DIR`;

#############################################################

# check the copied directories with diff
$bash_command = `diff -r $SOURCE_DIR $DESTINATION_DIR`;
echo "$bash_command \n";

$backup_dir = "{$BACKUP_PATH}/";
$dir_name_count = 0;

# Open the backup directory, and proceed to read its contents
if (is_dir($backup_dir)) {
    if ($dh = opendir($backup_dir)) {
        while (($file = readdir($dh)) !== false) {
            if (!is_dir($file) && ".." <> $file) {
                // build an array of directory names
                $directory_name_array[] = $file;
                $dir_name_count ++;
            }
        }
        closedir($dh);
    }
}

# sort the array so the oldest backup is first in the array,
# and the newest backup is last in the array.
sort($directory_name_array);

echo "SORTED contents of \$directory_name_array[] \n";
while(list($key, $value) = each($directory_name_array)) {
   echo "$key => $value \n";
   }

echo"\n";

# get the oldest backup
$oldest_backup = $directory_name_array[0];

# print the name of the oldest backup directory in the array
echo "Oldest backup dir is: $oldest_backup \n\n";

# print number of backups available
echo "There are $dir_name_count backups available. \n\n";

# print how many backups are required
echo 'NUMBER_REQUIRED_BACKUPS: ' . NUMBER_REQUIRED_BACKUPS . "\n\n";

$BACKUP_DELETION_DIR = "{$BACKUP_PATH}/$oldest_backup \n";

echo "The oldest backup dir to be deleted is: \n";
echo "$BACKUP_DELETION_DIR \n";

# delete the oldest backup dir if necessary
if ($dir_name_count > NUMBER_REQUIRED_BACKUPS) {
    # force recursive deletion of directory
    $bash_command = `rm -fR $BACKUP_DELETION_DIR`;

    echo "There are more than " .NUMBER_REQUIRED_BACKUPS. " backups in this directory, \n";
    echo "and oldest backup $oldest_backup has been deleted. \n\n";
    }
else
{
    echo "There are " .NUMBER_REQUIRED_BACKUPS. " or less backups in this directory. \n";
    echo "No old backups have been deleted. \n\n";
}

echo "Contents of $BACKUP_PATH backup dir is: \n\n";
$bash_command = `ls $BACKUP_PATH`;
echo "$bash_command \n";

showMountedPartitions();

# before starting mysql, remove old mysql binary log files
echo "Removing old mysql binary log files... \n\n";
$bash_command = `rm -vf /var/lib/databases/mysql/karsites-bin.*`;
echo "$bash_command \n";

# before starting mysql, remove old mysql text log files
echo "Removing old mysql text log files... \n\n";
$bash_command = `rm -vf /var/lib/databases/mysql/5-0-45.*`;
echo "$bash_command";

# start the mysql database server
$bash_command = `/etc/init.d/mysqld start`;
echo "$bash_command";

# output the current version of apache
$bash_command = `apachectl -v`;
echo "$bash_command \n";

# start the apache web server
$bash_command = `apachectl start`;
echo "$bash_command";
echo "Restarting Apache web server... \n\n";

# unmount the /backup-databases partition by it's mount point.
$bash_command = `umount -v $BACKUP_MOUNTPOINT`;
echo "$bash_command \n";

showMountedPartitions();
  
# end of script ?>
