# Youtube Typing Game Generator

あなたの好きな動画をタイピングゲームにしよう  
https://ytgg.murase-msk.work  

## 機能
  - Youtubeの動画URLからタイピングゲームを自動生成
  - タイピングゲーム
  - アカウント登録

## 動作要件
  - OS : Ubuntu16.04

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
  - Javascript ES2015(ES6)  
    - Vue2.5
      - Vue CLI3
      - vue-youtube
  - Bootstrap 3.3.6
  - Web API
    - Yahoo API (https://developer.yahoo.co.jp/webapi/jlp/furigana/v1/furigana.html)
    - Youtube Data API(https://developers.google.com/youtube/v3/getting-started)
  - Apache2.4
  - PostgreSQL 9.5

## 環境構築方法
//環境変数を設定する  
//PostgreSQL用アカウント  
DB_user="xxxx"  
DB_pass="xxxx"  
//youtubeAPIのAPIkey  
youtubeApiKey="xxxx"  
//YahooAPIのAPIkey  
yahooApiKey="xxxx"  
// 本番環境or開発環境  
env="production" or "develop"  
// Githubからのフッキングを受け取るためのSecretKey.  
Github_Secret = "xxxx"

cd /var/www/html  
git clone https://github.com/murase-msk/YoutubeTypingGameGenerator.git  
./init.sh production   


sudo vim /etc/postgresql/9.5/main/postgresql.conf  
//listen_address=* にする設定


// ssl有効化.  
sudo a2enmod ssl   
service apache2 restart  
sudo vi /etc/apache2/sites-available/default-ssl.conf  
        ServerAdmin xxx@yyy.zzz <- 変更  
        ServerName xxx.yyy.zzz <- 追加  
        DocumentRoot /var/www/html/YoutubeTypingGameGenerator/public  
sudo a2ensite default-ssl  
service apache2 reload  
//Lets EncryptでSSL証明書取得  
sudo add-apt-repository ppa:certbot/certbot  
sudo apt-get update  
apt-get install letsencrypt python-letsencrypt-apache  
sudo letsencrypt run --apache  




## testing
cd ~  
./start_selenium.sh  
cd /var/www/html/YoutubeTypingGameGenerator  
composer test

