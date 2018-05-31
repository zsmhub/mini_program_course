FROM hub.docker.com/r/dockerzsm/lnp:centos7_nginx1.8.1_php7.1

MAINTAINER zsm <zhangshimian@forgame.com>

COPY ./src /var/www/html

COPY ./build/docker/crontab /var/www/cron

COPY ./build/docker/startup.sh /var/www/startup.sh

RUN chmod +x /var/www/startup.sh && \
	(crontab -l ; cat /var/www/cron) 2>&1 | grep -v "no crontab" | uniq | crontab -  ;\
	mkdir -p /var/www/html/application/cache/cache_driver_data && mkdir -p /var/www/html/application/cache/logs;\
	ln -s /var/www/html/application/cache /var/www/logs/appcache
