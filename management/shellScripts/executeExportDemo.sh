#!/bin/bash

backupDir="/home/websites/com.genericwhite.goodearth"
dirname="backup_"`eval date +%Y%m%d%H%M`
mkdir $backupDir/backups/$dirname
mysqldump -ugoodearthsite -pglory*snacks  goodEarthDemoData > $backupDir/backups/$dirname/goodEarthDemoData.sql

curl -k http://goodearth.genericwhite.com/data/export > $backupDir/backups/$dirname/exportTranscript.html