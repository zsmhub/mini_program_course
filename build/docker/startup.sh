#!/bin/sh

function_start()
{
    printf "Starting SERVICE...\n"
    function_init
    /usr/sbin/php-fpm
    /usr/sbin/nginx -g 'daemon off;'
}

function_stop()
{
    printf "Stoping SERVICE...\n"
    kill -INT `cat /usr/local/php/var/run/php-fpm.pid`
    /usr/sbin/nginx -s stop
}

function_reload()
{
    printf "reload SERVICE...\n"
    kill -USR2 `cat /usr/local/php/var/run/php-fpm.pid`
    /usr/sbin/nginx -s reload
}

function_init()
{
    if [ ! -f /usr/local/nginx/conf/vhost/default.conf ]; then 
        {
            echo 'server';\
            echo '{';\
            echo '        listen       80;';\
            echo '        server_name localhost;';\
            echo '        index index.html index.htm index.php;';\
            echo '        root  /var/www/html;';\
            echo '        charset utf-8;';\
            echo '       location ~ [^/]\.php(/|$)';\
            echo '        {';\
            echo '            fastcgi_pass  unix:/tmp/php-cgi.sock;';\
            echo '            fastcgi_index index.php;';\
            echo '            include fastcgi.conf;';\
            echo '        }';\
            echo '        error_log  /var/www/logs/www_error.log  error;';\
            echo '        access_log /var/www/logs/www_access.log;';\
            echo '}';\
        } | tee /usr/local/nginx/conf/vhost/default.conf;
    fi;
    chown -R www:www /var/www/html;

    if [ -z "$DISABLED_CRONTAB" -o "$DISABLED_CRONTAB" != TRUE ]; then
        crond -i;
    fi

    if [ -n "$MYSQL_HOST" -a -n "$MYSQL_USER" -a -n "$MYSQL_PASSWORD" -a -n "$MYSQL_DATABASE" ]; then 
        sed -e "s#@mysql.default.hostname@#${MYSQL_HOST}#g" \
        -e "s#@mysql.default.database@#${MYSQL_DATABASE}#g" \
        -e "s#@mysql.default.username@#${MYSQL_USER}#g" \
        -e "s#@mysql.default.password@#${MYSQL_PASSWORD}#g" \
        /var/www/html/application/config/database.php -i;
        if [ -f '/var/www/html/_index.php' ]; then 
            /bin/cp /var/www/html/_index.php /var/www/html/index.php ; 
        fi
    else
        if [ ! -f '/var/www/html/_index.php' ]; then 
            /bin/cp /var/www/html/index.php /var/www/html/_index.php ; 
        fi
        echo "<?php exit('mysql accounts info not found !');?>" > /var/www/html/index.php ;    
    fi


    if [ -z "$WEB_BASEURL" ]; then
        sed -e "s#@base_url@#http:\/\/localhost#g" /var/www/html/application/config/config.php -i;
    else
        sed -e "s#@base_url@#${WEB_BASEURL}#g" /var/www/html/application/config/config.php -i ;
    fi
}


case "$1" in
    start)
        function_start
        ;;
    stop)
        function_stop
        ;;
    restart)
        function_stop
        function_start
        ;;
    reload)
        function_reload
        ;;    
    *)
        function_start
        # printf "Usage: {start|stop|reload|restart}\n"
esac
exit