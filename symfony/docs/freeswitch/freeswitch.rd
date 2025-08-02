[test]
docker compose run --rm freeswitch ./docker-entrypoint.sh freeswitch

docker ps -a --filter network=voice_appnet
docker stop 3a3b7d123b8c

docker compose down -v