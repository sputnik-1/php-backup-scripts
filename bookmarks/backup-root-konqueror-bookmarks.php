#! /usr/bin/php

<?php

# last updated: Fri Apr  4 14:24:21 BST 2008

# PHP script to backup konqueror bookmarks files

#------------------------------------------------------#
# how many backups to keep before deleting the oldest backup
define("NUMBER_REQUIRED_BACKUPS", 7);

#------------------------------------------------------#
# the source-file that needs backing up.

# full path to the file to be backed up.
# $SOURCE_FILE="/home/keith/.kde/share/apps/konqueror/bookmarks.xml";

# full path to the file to be backed up.
# Currently running as root user due to sound problems.
$SOURCE_FILE="/root/.kde/share/apps/konqueror/bookmarks.xml";

echo "\$SOURCE_FILE is: $SOURCE_FILE";
echo"\n\n";

/*
# the partition label id of the file(s)
# that need backing up. May not be required.
$SOURCE_LABEL_ID="NONE";

# the directory mountpoint of the file(s)
# that need backing up. May not be required.
$SOURCE_MOUNTPOINT="NONE";

echo "\$SOURCE_LABEL_ID is: $SOURCE_LABEL_ID";
echo"\n\n";

echo "\$SOURCE_MOUNTPOINT is: $SOURCE_MOUNTPOINT";
echo"\n\n";
*/

#------------------------------------------------------#
# destination for the backups

# the backup partition label id.
$BACKUP_LABEL_ID="backups";

# the backup partition directory mountpoint.
$BACKUP_MOUNTPOINT="/backup";

# the backup group name
$BACKUP_GROUPNAME="/bookmarks";

# which user's bookmarks to backup
$USERNAME= "/root";

# which browser's bookmark file to backup
$BROWSER_NAME="/konqueror";

# the backup group full pathname
$BACKUP_PATH = "{$BACKUP_MOUNTPOINT}{$BACKUP_GROUPNAME}{$USERNAME}{$BROWSER_NAME}";

echo "\$BACKUP_LABEL_ID is: $BACKUP_LABEL_ID";
echo"\n\n";

echo "\$BACKUP_MOUNTPOINT is: $BACKUP_MOUNTPOINT";
echo"\n\n";

echo "\$BACKUP_GROUPNAME is: $BACKUP_GROUPNAME";
echo"\n\n";

echo "\$USERNAME is: $USERNAME";
echo"\n\n";

echo "\$BROWSER_NAME is: $BROWSER_NAME";
echo"\n\n";

echo "\$BACKUP_PATH is: $BACKUP_PATH";
echo"\n\n";


// exit();


#------------------------------------------------------#

# get the mounted partitions
$bash_command = `df -h`;
echo "$bash_command \n";

# mount the backup partition by label id.
$bash_command = `mount -v -L $BACKUP_LABEL_ID`;
echo "$bash_command \n";
echo"\n";

# display currently mounted partitions
$bash_command = `df -h`;
echo "$bash_command \n";
echo"\n";

#############################################################
# NOTE: tar does not like filenames with a ':' (colon)
# so DO NOT use as part of the filename extension

$CURRENT_DATE=`date +'-%Y-%m-%d_%H.%M.%S'`;

echo "CURRENT_DATE is: " . $CURRENT_DATE;
echo"\n";

$DESTINATION_DIR="{$BACKUP_PATH}{$BACKUP_GROUPNAME}{$CURRENT_DATE}";

echo "DESTINATION_DIR IS: $DESTINATION_DIR \n";

// exit();


# shell command to create the backup group directory
$bash_command = `mkdir $DESTINATION_DIR`;

# shell command to copy konqueror bookmarks file to backup parition
$bash_command = `cp -v --preserve=mode,ownership $SOURCE_FILE  $DESTINATION_DIR`;
echo "$bash_command \n";

# shell command to copy konqueror bookmarks.bak file to backup parition
$bash_command = `cp -v --preserve=mode,ownership $SOURCE_FILE.bak  $DESTINATION_DIR`;
echo "$bash_command \n";

#############################################################

# check the copied directories with diff
#$bash_command = `diff "$SOURCE_FILE" "$DESTINATION_DIR/bookmarks.xml"`;
#echo "$bash_command \n";

# display currently mounted partitions
$bash_command = `df -h`;
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
echo"\n";

# unmount the backup parition by it's mount point.
# $bash_command = `umount -v $BACKUP_MOUNTPOINT`;
# echo "$bash_command \n";
  
# end of script ?>
