FROM php:7.2-apache

# Enable mod_ssl for apache
RUN a2enmod ssl
RUN a2enmod rewrite
RUN a2dissite 000-default

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN openssl genrsa -out /etc/ssl/private/ssl-cert-snakeoil.key 3072
RUN openssl req -new -out website.csr -sha256 -key /etc/ssl/private/ssl-cert-snakeoil.key -subj "/C=NZ/ST=WGN/L=Wellington/O=NA/OU=NA/CN=localhost"
RUN openssl x509 -req -in website.csr -days 365 -signkey /etc/ssl/private/ssl-cert-snakeoil.key -out /etc/ssl/certs/ssl-cert-snakeoil.pem

COPY src/ /var/www/html/

# copy the apache conf and enable the site.
COPY my-apache-site.conf /etc/apache2/sites-available/my-apache-site.conf
RUN a2ensite my-apache-site

EXPOSE 443
EXPOSE 80
