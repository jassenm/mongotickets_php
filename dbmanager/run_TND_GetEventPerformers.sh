#!/bin/bash
. /var/www/vhosts/cpcworks.com/mongotickets/dbmanager/webServicesEnvVars.sh

date >> ${DBMANAGER_HOME}/run.epv3p_cron_log.txt
cd ${AXIS2_HOME}

ant run.epv3p >> ${DBMANAGER_HOME}/run.epv3p_cron_log.txt 2>&1

date >> ${DBMANAGER_HOME}/run.epv3p_cron_log.txt
