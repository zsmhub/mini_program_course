#!/bin/bash
FLAG=$1
DEPLOY_VERSION=$2
CUR_DIR=$(cd `dirname $0`; pwd)
WEB_IMAGE=

NGINX_RELOAD='/usr/local/nginx/sbin/nginx -s reload'
NGINX_CONF_PATH=/usr/local/nginx/conf/vhost
PROJECT_NAME=`echo $WEB_IMAGE|awk -F '/' '{print $2"_"$3}'`

if [ ! -f "${CUR_DIR}/config.env" ];then
	echo "配置文件config.env不存在"
	exit 1
fi

if [ "$FLAG" = "install" ]; then
	#安装新版本容器
	if [ -f ${CUR_DIR}/image_${DEPLOY_VERSION}.tar ]; then
		echo "发现本地镜像文件，正在导入..."
		docker load -i ${CUR_DIR}/image_${DEPLOY_VERSION}.tar
		rm -f ${CUR_DIR}/image_${DEPLOY_VERSION}.tar
	fi 
	if [ -f ${CUR_DIR}/dbexec/update.sql -o -f ${CUR_DIR}/dbexec/${DEPLOY_VERSION}.sql ]; then
		if [ -f ${CUR_DIR}/dbexec/${DEPLOY_VERSION}.sql ]; then
			/bin/mv  ${CUR_DIR}/dbexec/${DEPLOY_VERSION}.sql ${CUR_DIR}/dbexec/update.sql
		fi
		echo "发现本地数据库更新文件，正在导入..."
		DBM_CONTAINER_ID=`docker run -d --rm --env-file "${CUR_DIR}/config.env" -v "${CUR_DIR}/dbexec:/work/sql" forgame/dbm:mysql-rsync` 
        docker exec $DBM_CONTAINER_ID bash /work/sh/mysql-exec.sh update
        docker rm -f $DBM_CONTAINER_ID
		# docker run --rm --env-file "${CUR_DIR}/config.env" -v "${CUR_DIR}/dbexec:/work/sql" forgame/dbm:mysql-rsync bash /work/sh/mysql-exec.sh update
	fi

	WEB_CONTAINER_ID=`docker run -d -P --rm --env-file "${CUR_DIR}/config.env" -v ${CUR_DIR}/logs:/var/www/logs ${WEB_IMAGE}:${DEPLOY_VERSION}`  
	if [ -z $WEB_CONTAINER_ID ]; then
		echo "WEB容器启动失败"
		exit 1
	fi
	WEB_CONTAINER_PORT=`docker inspect ${WEB_CONTAINER_ID}|grep "Ports" -A 50|grep "80/tcp" -A 3| grep HostPort|cut -d '"' -f4|head -1` 
	if [ -z $WEB_CONTAINER_PORT ]; then
		echo "WEB容器启动失败"
		exit 1
	else
		echo "当前启动容器端口号为 ${WEB_CONTAINER_PORT}"
		if [ -f ${CUR_DIR}/nginx.conf ]; then
			echo "正在生成nginx配置文件..."
			sed -e "s#@PORT@#${WEB_CONTAINER_PORT}#g" ${CUR_DIR}/nginx.conf -i;
		fi
	fi
elif [ "$FLAG" = "cutover" ]; then 
	#切换到新版本容器
	if [ ! -f ${CUR_DIR}/nginx.conf  -o `cat ${CUR_DIR}/nginx.conf|grep '@PORT@'|wc -l` -gt 1 -o `cat ${CUR_DIR}/nginx.conf|grep '127.0.0.1:;'|wc -l` -gt 1 ]; then
		echo "nginx.conf文件不存在或其配置有误..."
		exit 1
	fi
	\cp ${CUR_DIR}/nginx.conf ${NGINX_CONF_PATH}/${PROJECT_NAME}.conf
	echo "reloading nginx..."
	`${NGINX_RELOAD}`
elif [ "$FLAG" = "stop_old_version" ]; then 	
	#停止老版本容器
	for dockerinfo in ` docker ps --format {{.ID}}###{{.Image}} | grep "${WEB_IMAGE}"`; do 
		if [ -z `echo $dockerinfo|grep $DEPLOY_VERSION` ]; then
			echo "正在停止容器`echo $dockerinfo | awk -F'###' '{print $1}'`（`echo $dockerinfo | awk -F'###' '{print $2}'`）..."
			docker stop `echo $dockerinfo | awk -F'###' '{print $1}'`
		fi
	done
fi
