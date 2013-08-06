#!/bin/bash

backupDir="/home/websites/com.genericwhite.goodearth"
dirname="backup_"`eval date +%Y%m%d`
mkdir $backupDir/backups/$dirname
mysqldump -ugoodearthsite -pglory*snacks  goodEarthDemoData > $backupDir/backups/$dirname/goodEarthProductionData.sql

curl -k https://sales.goodearthorganiclunches.com/data/export > $backupDir/backups/$dirname/exportTranscript.html