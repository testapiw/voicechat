[test]
docker compose run --rm freeswitch ./docker-entrypoint.sh freeswitch

docker ps -a --filter network=voice_appnet
docker stop 3a3b7d123b8c

docker compose down -v


$ docker compose up -d
[+] Running 6/6
 ✔ Network voice_appnet          Created                                                                                                              0.2s 
 ✔ Container voice-php-1         Started                                                                                                              1.6s 
 ✔ Container voice-db-1          Started                                                                                                              1.9s 
 ✔ Container voice-freeswitch-1  Started                                                                                                              6.0s 
 ✔ Container voice-nginx-1       Started                                                                                                              2.4s 
 ✔ Container voice-pgadmin-1     Started                                                                                                              2.6s 
$ docker compose down
[+] Running 6/6
 ✔ Container voice-pgadmin-1     Removed                                                                                                              3.3s 
 ✔ Container voice-freeswitch-1  Removed                                                                                                             11.9s 
 ✔ Container voice-nginx-1       Removed                                                                                                              0.6s 
 ✔ Container voice-php-1         Removed                                                                                                              0.5s 
 ✔ Container voice-db-1          Removed                                                                                                              0.5s 
 ✔ Network voice_appnet          Removed 

 docker exec -it voice-freeswitch-1 fs_cli
 sofia status
 log level debug

TD: звонок между двумя клиентами, регистрация через SIP, ESL-события


fs_cli -H 172.18.0.6 -P 8021 -p ClueCon -x "reloadacl"
fs_cli -H 172.18.0.6 -P 8021 -p ClueCon -x "reload mod_event_socket"

 telnet 172.18.0.6 8021