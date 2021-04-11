FROM nginx:alpine

RUN adduser -S -G www-data www-data

COPY build/docker/nginx/nginx.conf  /etc/nginx/
COPY build/docker/nginx/default.conf /etc/nginx/conf.d/
RUN echo "upstream php-upstream { server notifications-fpm:9000; }" > /etc/nginx/conf.d/upstream.conf

EXPOSE 80

CMD ["nginx"]
