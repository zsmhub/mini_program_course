#!/bin/bash
CONTAINER_PREFIX=$1
WEB_CONTAINER_PORT=$2
CUR_DIR=`pwd`

DBM_ID=`docker ps -f "name=${CONTAINER_PREFIX}_DBM" -q`
WEB_ID=`docker ps -f "name=${CONTAINER_PREFIX}_WEB" -q`
if [ -z $DBM_ID ]; then
	docker rm -f ${CONTAINER_PREFIX}_DBM
	docker run -d --name ${CONTAINER_PREFIX}_DBM \
	--env-file "${CUR_DIR}/database.env" \
    -v "${CUR_DIR}/dbinit:/work/sql" \
    p.home.forgame.com:4567/docker/dbm:mysql-rsync;   
fi

if [ -z $WEB_ID ]; then
	docker rm -f ${CONTAINER_PREFIX}_WEB
	docker run -d --name ${CONTAINER_PREFIX}_WEB \
	-p "${WEB_CONTAINER_PORT}:80" \
	-v "${CUR_DIR}/web:/var/www/html" \
	-v "${CUR_DIR}/logs:/var/www/logs" \
	p.home.forgame.com:4567/docker/lnp:centos7_nginx1.8.1_php7.1  
else
	docker restart 	${CONTAINER_PREFIX}_WEB	
fi

if [ -f ${CUR_DIR}/dbinit/update.sql ]; then
	SQLHASH=`md5sum ${CUR_DIR}/dbinit/update.sql|cut -d ' ' -f1`
	if [ ! -f ${CUR_DIR}/dbinit/.mysqlexecuted.log ]; then
		touch ${CUR_DIR}/dbinit/.mysqlexecuted.log
	fi
	if [ -z `cat ${CUR_DIR}/dbinit/.mysqlexecuted.log|grep $SQLHASH` ]; then
		docker exec ${CONTAINER_PREFIX}_DBM bash /work/sh/mysql-exec.sh update
		echo "$SQLHASH" >> "${CUR_DIR}/dbinit/.mysqlexecuted.log"
	fi
fi
