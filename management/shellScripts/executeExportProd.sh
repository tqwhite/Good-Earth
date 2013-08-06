#!/bin/bash

backupDir="//home/websites/com.goodearthorganiclunches.sales/"
dirname="backup_"`eval date +%Y%m%d`
mkdir $backupDir/backups/$dirname
mysqldump -ugoodearthsite -pglory*snacks  goodEarthProductionData > $backupDir/backups/$dirname/goodEarthProductionData.sql

curl https://sales.goodearthorganiclunches.com//data/export > $backupDir/backups/$dirname/exportTranscript.html