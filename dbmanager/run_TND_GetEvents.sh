#!/bin/bash
. /var/www/vhosts/cpcworks.com/mongotickets/dbmanager/webServicesEnvVars.sh

date >> ${DBMANAGER_HOME}/run.evv3p_cron_log.txt 
cd ${AXIS2_HOME}

ant run.evv3p >> ${DBMANAGER_HOME}/run.evv3p_cron_log.txt 2>&1

date >> ${DBMANAGER_HOME}/run.evv3p_cron_log.txt 
