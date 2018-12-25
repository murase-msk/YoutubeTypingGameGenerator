# Youtube Typing Game Generator

あなたの好きな動画でタイピングゲームをしよう

## 機能
  - Youtubeの動画URLからタイピングゲームを自動生成
  - タイピングゲーム
  - アカウント登録

## 動作要件
  - OS : Ubuntu16.04
  - Web Server, AP Server : Apache2.4
  - RDBMS : PostgreSQL 9.x

## 使用技術
  - PHP7.2
    - Composer (Package Manager)
    - Selenium (E2E Test)
    - PHPUnit (Unit Test)
    - Slim3 (Web Application FrameWork)
      - twig (template)
      - pimple (DI Container)
      - slim-csrf
      - slim-flash
    - Goutte (Web Crawler)
  - Javascript ES2015(ES6) (babel transpiled)
    - Vue2.5
      - Vue CLI3
      - vue-youtube
  - Bootstrap 3.3.6
  - Web API
    - Yahoo API (https://developer.yahoo.co.jp/webapi/jlp/furigana/v1/furigana.html)
    - Youtube Data API(https://developers.google.com/youtube/v3/getting-started)

## 環境構築方法
vagrantでUbuntu16.04を作成

git clone https://github.com/murase-msk/slim_app.git

sh ./provision.sh

composer install

php database/init/initDatabase.php

sudo chown -R www-data:www-data /var/www/html/slim_app/

cd vue-project

npm install

