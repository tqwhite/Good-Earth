#!/bin/bash

backupDir="/home/tqwhite/clients/goodearth"
dirname="backup_"`eval date +%Y%m%d%H%M`
mkdir $backupDir/backups/$dirname
mysqldump -utqorg -pmoney*pie  goodearthStore > $backupDir/backups/$dirname/goodEarthProductionData.sql

curl http://store.goodearth.local/data/export > $backupDir/backups/$dirname/exportTranscript.html