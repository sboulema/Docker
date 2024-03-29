FROM ubuntu
LABEL maintainer="sboulema@gmail.com"

ENV PHP_VERSION=8.1
ENV DEBIAN_FRONTEND=noninteractive 

RUN useradd ttrss

# add repositories: PHP, Git
RUN apt-get update && \
    apt-get install -y software-properties-common && \
    add-apt-repository ppa:ondrej/php && \
    add-apt-repository ppa:git-core/ppa && \
    apt-get update

# install tools
RUN apt-get install -y \
    wget \
    git

# install PHP and extensions
RUN apt-get install -y \
  php${PHP_VERSION} php${PHP_VERSION}-fpm \
  php${PHP_VERSION}-pdo php${PHP_VERSION}-gd php${PHP_VERSION}-pgsql php${PHP_VERSION}-mbstring \
  php${PHP_VERSION}-intl php${PHP_VERSION}-xml php${PHP_VERSION}-curl \
  php${PHP_VERSION}-dom php${PHP_VERSION}-fileinfo php${PHP_VERSION}-iconv \
  php${PHP_VERSION}-posix php${PHP_VERSION}-zip \
  nginx supervisor sudo \
  && apt-get clean && rm -rf /var/lib/apt/lists/* && mkdir -p /var/run/php

# enable PHP modules
RUN phpenmod -v ${PHP_VERSION} mbstring && \
    phpenmod -v ${PHP_VERSION} xml

# enable PHP ENV
RUN sed -i.bak 's/;clear_env = .*/clear_env = no/i' /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf

# add ttrss as the only nginx site
ADD ttrss.nginx.conf /etc/nginx/sites-available/ttrss
RUN sed -i "s/PHP_VERSION/${PHP_VERSION}/" /etc/nginx/sites-available/ttrss
RUN ln -s /etc/nginx/sites-available/ttrss /etc/nginx/sites-enabled/ttrss
RUN rm /etc/nginx/sites-enabled/default

# expose only nginx HTTP port
EXPOSE 80

# install ttrss
WORKDIR /var/www
RUN rm -rf *
RUN git clone https://gitlab.tt-rss.org/tt-rss/tt-rss.git .
RUN git config --global --add safe.directory /var/www

# install first-party ttrss plugins
RUN git clone https://gitlab.tt-rss.org/tt-rss/plugins/ttrss-close-button.git plugins/close_button && \
    git clone https://gitlab.tt-rss.org/tt-rss/plugins/ttrss-favicon-unread.git plugins/favicon_unread && \
    git clone https://gitlab.tt-rss.org/tt-rss/plugins/ttrss-no-url-hashes.git plugins/no_url_hashes

# install third-party ttrss plugins
RUN wget -c https://github.com/nneul/tt-rss-one-click-mark/archive/refs/tags/v0.3.tar.gz -O - | sudo tar -xz --strip-components 1 --one-top-level=plugins.local tt-rss-one-click-mark-0.3/one_click_mark

RUN git clone https://github.com/VerifiedJoseph/ttrss-plugin-gotify plugins.local/gotify_notifications

# set default settings
ENV TTRSS_DB_NAME="ttrss"
ENV TTRSS_DB_USER="ttrss"
ENV TTRSS_DB_PASS="ttrss"
ENV TTRSS_DB_TYPE="pgsql"
ENV TTRSS_DB_PORT="5432"
ENV TTRSS_SESSION_COOKIE_LIFETIME="86400000"
ENV TTRSS_CACHE_DIR="cache"
ENV TTRSS_ICONS_DIR="feed-icons"
ENV TTRSS_LOCK_DIRECTORY="lock"
ENV TTRSS_PHP_EXECUTABLE="/usr/bin/php${PHP_VERSION}"

# set permissions on directories
RUN mkdir ${TTRSS_CACHE_DIR}/feed-icons
RUN chown www-data:www-data -R /var/www && \
    chmod -R 777 ${TTRSS_CACHE_DIR}/images && \
    chmod -R 777 ${TTRSS_CACHE_DIR}/upload && \
    chmod -R 777 ${TTRSS_CACHE_DIR}/export && \
    chmod -R 777 ${TTRSS_CACHE_DIR}/feed-icons && \
    chmod -R 777 ${TTRSS_ICONS_DIR} && \
    chmod -R 777 ${TTRSS_LOCK_DIRECTORY}

# always re-configure database with current ENV when RUNning container, then monitor all services
ADD configure-db.php /configure-db.php
ADD supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN sed -i "s/PHP_VERSION/${PHP_VERSION}/" /etc/supervisor/conf.d/supervisord.conf
CMD php /configure-db.php && \
    sudo -E -u ttrss php update.php --update-schema=force-yes && \
    supervisord -c /etc/supervisor/conf.d/supervisord.conf