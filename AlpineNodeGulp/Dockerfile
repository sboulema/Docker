FROM shito/alpine-node:edge
MAINTAINER Samir L. Boulema <sboulema@gmail.com>

WORKDIR /tmp

RUN apk add --update \
    python \
    python-dev \
    py-pip \
    build-base \
    git \
    openssh-client \
    curl \
  && pip install virtualenv \
  && rm -rf /var/cache/apk/*

RUN npm install gulp -g
RUN npm install -g bower

WORKDIR /usr/share/nginx/html

RUN npm install gulp --save-dev

ENTRYPOINT ["gulp"]
CMD ["--version"]