# Youtube Typing Game Generator

あなたの好きな動画でタイピングゲームをしよう

URL(未定)

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
cd /var/www/html  
git clone https://github.com/murase-msk/YoutubeTypingGameGenerator.git  
./provision.sh production   
cd /var/www/html/YoutubeTypingGnameGenerator  
composer install  

//環境変数を設定する  
//PostgreSQL用アカウント  
DB_user="xxxx"  
DB_pass="xxxx"  
//youtubeAPIのAPIkey  
youtubeApiKey="xxxx"  
//YahooAPIのAPIkey  
yahooApiKey="xxxx"  

// DB初期化  
php database/init/initDatabase.php  
sudo chown -R www-data:www-data /var/www/html/YoutubeTypingGameGenerator/  
cd vue-project

//windows Hostとファイル共有する場合のみ  
//node_modulesを共有しないディレクトリに作成し、シンボリックリンクを貼る  
mkdir -p ~slim_app_node_modules/node_modules  
ln -s ~/slim_app_node_modules/node_modules/ node_modules  
npm install  

//provision.shに追加する  
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
composer test

