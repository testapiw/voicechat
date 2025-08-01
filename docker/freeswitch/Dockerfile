FROM php:8.4-fpm

USER root

# Installing dependencies
#RUN apt-get update -qq \
#    && apt-get install -y --no-install-recommends ca-certificates gnupg2 gosu locales wget \
#    && localedef -i en_US -c -f UTF-8 -A /usr/share/locale/locale.alias en_US.UTF-8
#ENV LANG en_US.utf8

RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    git zip unzip curl ca-certificates \
    libpq-dev libonig-dev libzip-dev \
    build-essential autoconf pkg-config libtool-bin \
    cmake automake autoconf libssl-dev zlib1g-dev libdb-dev unixodbc-dev libncurses5-dev libexpat1-dev libgdbm-dev bison \
    libtiff5-dev uuid-dev libpcre2-dev libsqlite3-dev libcurl4-openssl-dev nasm \
    libogg-dev libspeex-dev libspeexdsp-dev libldns-dev python3-dev \
    libavformat-dev libswscale-dev liblua5.3-dev \
    libavcodec-dev libavfilter-dev libavutil-dev \
    libopus-dev libsndfile1-dev libflac-dev libvorbis-dev libshout3-dev libmpg123-dev libmp3lame-dev \
    erlang-base erlang-dev erlang-tools \
    libtpl-dev libedit2 libedit-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# https://github.com/signalwire/freeswitch/blob/master/docker/examples/Debian11/Dockerfile

RUN git clone https://github.com/signalwire/freeswitch /usr/src/freeswitch \
    && git clone https://github.com/signalwire/libks /usr/src/libs/libks \
    && git clone https://github.com/freeswitch/sofia-sip /usr/src/libs/sofia-sip \
    && git clone https://github.com/freeswitch/spandsp /usr/src/libs/spandsp \
    && git clone https://github.com/signalwire/signalwire-c /usr/src/libs/signalwire-c

# signalwire/libks
RUN cd /usr/src/libs/libks && cmake . -DCMAKE_INSTALL_PREFIX=/usr -DWITH_LIBBACKTRACE=1 && make install
# freeswitch/sofia-sip
RUN cd /usr/src/libs/sofia-sip && ./bootstrap.sh && ./configure CFLAGS="-g -ggdb" --with-pic --with-glib=no --without-doxygen --disable-stun --prefix=/usr && make -j`nproc --all` && make install
# Build spandsp
RUN cd /usr/src/libs/spandsp && ./bootstrap.sh && ./configure CFLAGS="-g -ggdb" --with-pic --prefix=/usr && make -j`nproc --all` && make install
# signalwire/signalwire-c
RUN cd /usr/src/libs/signalwire-c && PKG_CONFIG_PATH=/usr/lib/pkgconfig cmake . -DCMAKE_INSTALL_PREFIX=/usr && make install


# disable
# signalwire/libks
# RUN sed -i '/endpoints\/mod_verto/d' /usr/src/freeswitch/modules.conf.in
# RUN sed -i '/event_handlers\/mod_signalwire/d' /usr/src/freeswitch/modules.conf.in

# Enable modules
RUN sed -i 's|#formats/mod_shout|formats/mod_shout|' /usr/src/freeswitch/build/modules.conf.in

# Build freeswitch
RUN cd /usr/src/freeswitch && ./bootstrap.sh -j && ./configure && make -j$(nproc) && make install

RUN chown -R freeswitch:freeswitch /usr/local/freeswitch /usr/src/freeswitch

# Cleanup the image
RUN apt-get clean

# Uncomment to cleanup even more
#RUN rm -rf /usr/src/*

# 4. Create a user
RUN groupadd -g ${GID} freeswitch \
    && useradd -u ${UID} -g freeswitch -m freeswitch

WORKDIR /var/www/symfony

# 5. switch to user
USER freeswitch

EXPOSE 5060/udp 5060/tcp 5080/tcp 8021/tcp

CMD ["/usr/local/freeswitch/bin/freeswitch", "-nc", "-nonat"]