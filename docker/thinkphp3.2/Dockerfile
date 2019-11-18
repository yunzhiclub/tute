#  设置源
FROM registry.cn-beijing.aliyuncs.com/mengyunzhi/thinkphp:3.2-development
ENV TZ=Asia/Shanghai
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
RUN chmod 777 /var/log/
RUN echo 'xdebug.idekey=PHPSTORM' >> /usr/local/etc/php/php.ini