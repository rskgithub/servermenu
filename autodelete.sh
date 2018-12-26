#/bin/sh

# This script deletes old files when the disk is getting full.
# It will always delete the oldest file or directory. Can be run 
# in a cronjob every few minutes.

# Configurables

FILESYSTEM=/dev/vda1 # or whatever filesystem to monitor
CAPACITY=95 # delete if FS is over 95% of usage 
STORAGEDIR=/downloads

CURR_CAPACITY=$(df -P $FILESYSTEM | awk '{ gsub("%",""); capacity = $5 }; END { print capacity }')

if [ $CURR_CAPACITY -gt $CAPACITY ]
then
	FILENAME=$(ls -1t $STORAGEDIR | tail -n1)
	rm -rf $STORAGEDIR/$FILENAME
fi 	