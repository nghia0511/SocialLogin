How to run
1.clone source and install docker
2.run docker compose --env-file ./config/.env up -d
3. docker exec -it cakephp-app composer install
4. create .env and app_local.php from .env.example and app_local.example.php (input FACEBOOK_APP_ID, FACEBOOK_APP_SECRET, ...)
5. run migrate: docker exec -it cakephp-app bin/cake migrations migrate