# システムの導入

1. GitHubからクローンする<br>
`$ git clone git@github.com:gp-sato/kintai.git`

1. ドッカービルドする<br>
`$ cd kintai/`<br>
`$ docker-compose up -d --build`

1. 開発ユーザーでコンテナに入る<br>
`$ docker-compose exec --user developer app bash`

1. コンポーザーでパッケージを入れる<br>
`/var/www$ cd laravel-project/`<br>
`/var/www/laravel-project$ composer install`

1. .env.exampleファイルをコピーして.envファイルを作る<br>
`/var/www/laravel-project$ cp .env.example .env`

1. ジェネリックキーを生成する<br>
`/var/www/laravel-project$ php artisan key:generate`

1. 一旦コンテナから出て、rootで入り直す<br>
`/var/www/laravel-project$ exit`<br>
`$ docker-compose exec app bash`<br>
`/var/www# cd laravel-project/`

1. storageフォルダ以下の所有者をwww-dataに変更する<br>
`/var/www/laravel-project# chown www-data storage/ -R`

※以後、コントローラーの作成を行うなどする時は開発ユーザーでコンテナに入って行ってください。

# サービスの表示

サービスはブラウザで`http:localhost:8000`にアクセスしてください。<br>
メールは`http:localhost:8025`で表示します。

# システムの削除

1. rootでコンテナに入る<br>
`$ docker-compose exec app bash`<br>
`/var/www# cd laravel-project/`

1. storageフォルダをフルアクセスに変更する<br>
`/var/www/laravel-project# chmod 777 storage/ -R`

1. コンテナを落としてから、エディタ上でローカルリポジトリを削除してください。
