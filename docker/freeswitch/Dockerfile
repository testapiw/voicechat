FROM php:8.4-fpm

ARG UID=1000
ARG GID=1000

USER root

ENV LANG=en_US.utf8

# Install required packages and dependencies
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    gosu git zip unzip curl ca-certificates \
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

# Clone FreeSWITCH and its dependencies source code
RUN git clone https://github.com/signalwire/freeswitch /usr/src/freeswitch \
    && git clone https://github.com/signalwire/libks /usr/src/libs/libks \
    && git clone https://github.com/freeswitch/sofia-sip /usr/src/libs/sofia-sip \
    && git clone https://github.com/freeswitch/spandsp /usr/src/libs/spandsp
    # && git clone https://github.com/signalwire/signalwire-c /usr/src/libs/signalwire-c

# Build and install libks
RUN cd /usr/src/libs/libks && cmake . -DCMAKE_INSTALL_PREFIX=/usr -DWITH_LIBBACKTRACE=1 && make install

# Build and install sofia-sip
RUN cd /usr/src/libs/sofia-sip && ./bootstrap.sh && ./configure CFLAGS="-g -ggdb" --with-pic --with-glib=no --without-doxygen --disable-stun --prefix=/usr && make -j$(nproc) && make install

# Build and install spandsp
RUN cd /usr/src/libs/spandsp && ./bootstrap.sh && ./configure CFLAGS="-g -ggdb" --with-pic --prefix=/usr && make -j$(nproc) && make install

# Build and install signalwire-c
# RUN cd /usr/src/libs/signalwire-c && PKG_CONFIG_PATH=/usr/lib/pkgconfig cmake . -DCMAKE_INSTALL_PREFIX=/usr && make install


# Enable mod_shout module in FreeSWITCH build config
RUN sed -i 's|#formats/mod_shout|formats/mod_shout|' /usr/src/freeswitch/build/modules.conf.in

# Build and install FreeSWITCH
RUN cd /usr/src/freeswitch && ./bootstrap.sh -j && \
    sed -i 's/^applications\/mod_signalwire/#applications\/mod_signalwire/' modules.conf && \
    ./configure && make -j$(nproc) && make install

RUN ln -s /usr/local/freeswitch/bin/freeswitch /usr/bin/freeswitch

# Create freeswitch group and user with specified UID and GID
RUN groupadd -g ${GID} freeswitch \
    && useradd -u ${UID} -g freeswitch -m freeswitch

# Change ownership to freeswitch user and group
RUN chown -R freeswitch:freeswitch /usr/local/freeswitch /usr/src/freeswitch \
    && chown -R freeswitch:freeswitch /usr/local/freeswitch/conf /usr/local/freeswitch/log

COPY ./docker/freeswitch/docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

# Clean up source directories to reduce image size
#RUN rm -rf /usr/src/freeswitch /usr/src/libs

WORKDIR /

# Expose necessary ports
EXPOSE 5060/udp 5060/tcp 5080/tcp 8021/tcp
# — SIP-TLS (secure SIP) ports.
# — alternative SIP-TLS ports. 5081/tcp and 5081/udp
EXPOSE 5061/tcp 5061/udp 5081/tcp 5081/udp
# — WebSocket SIP port.
EXPOSE 5066/tcp
# — WebSocket Secure (WSS) port.
EXPOSE 7443/tcp
# — ports for Vertо protocol (JavaScript WebRTC client FreeSWITCH).
EXPOSE 8081/tcp 8082/tcp
# — RTP ports (media streams).
EXPOSE 64535-65535/udp
# — extended range of RTP ports.
EXPOSE 16384-32768/udp

# Volumes
## Freeswitch Configuration
VOLUME ["/etc/freeswitch"]
## Tmp so we can get core dumps out
VOLUME ["/tmp"]

# Limits Configuration
COPY    ./docker/freeswitch/build/freeswitch.limits.conf /etc/security/limits.d/

# Switch to freeswitch user
#USER freeswitch

# Healthcheck to make sure the service is running
#SHELL       ["/bin/bash", "-c"]
#HEALTHCHECK --interval=15s --timeout=5s \
#    CMD  /usr/local/freeswitch/bin/fs_cli -x status | grep -q ^UP || exit 1

ENTRYPOINT ["/docker-entrypoint.sh"]
CMD ["freeswitch"]
