FROM mysql/mysql-server:8.0

ENV TZ=Asia/Tokyo
# ENV MYSQL_DATABASE=laravel_docker \
#   MYSQL_USER=laravel_docker \
#   MYSQL_PASSWORD=laravel_docker \
#   MYSQL_ROOT_PASSWORD=root \
#   TZ=Asia/Tokyo

COPY ./my.cnf /etc/my.cnf
RUN chmod 644 /etc/my.cnf