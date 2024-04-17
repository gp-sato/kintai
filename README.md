# システムの導入

- 任意のフォルダに移動してください。

- GitHubからクローンする

`$ git clone git@github.com:gp-sato/kintai.git`

- kintaiフォルダに入る

`$ cd kintai/`

- ドッカービルドする

`$ docker-compose up -d --build`

- 開発ユーザーでコンテナに入る

`$ docker-compose exec --user kazuhiro app bash`

- Laravelのプロジェクトフォルダに入る

`/var/www$ cd laravel-project/`

- コンポーザーでパッケージを入れる

`/var/www/laravel-project$ composer install`

- .env.exampleファイルをコピーして.envファイルを作る

`/var/www/laravel-project$ cp .env.example .env`

- ジェネリックキーを生成する

`/var/www/laravel-project$ php artisan key:generate`

- 一旦コンテナから出て、rootで入り直す

`/var/www/laravel-project$ exit`

`$ docker-compose exec app bash`

`/var/www# cd laravel-project/`

- storageフォルダ以下の所有者をwww-dataに変更する

`/var/www/laravel-project# chown www-data storage/ -R`

以後、コントローラーの作成を行うなどする時は開発ユーザーでコンテナに入って行ってください。

# システムの削除

- rootでコンテナに入る

`$ docker-compose exec app bash`

`/var/www# cd laravel-project/`

- storageフォルダをフルアクセスに変更する

`/var/www/laravel-project# chmod 777 storage/ -R`

- コンテナを落としてから、エディタ上でローカルリポジトリを削除してください。
