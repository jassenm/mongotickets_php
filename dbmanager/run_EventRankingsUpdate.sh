#!/bin/bash
. /var/www/vhosts/cpcworks.com/mongotickets/dbmanager/webServicesEnvVars.sh
date >> ${DBMANAGER_HOME}/run.get_high_sales_log.txt
cd ${AXIS2_HOME}

ant run.get_high_sales >> ${DBMANAGER_HOME}/run.get_high_sales_log.txt 2>&1


date >> ${DBMANAGER_HOME}/run.get_high_sales_log.txt 
