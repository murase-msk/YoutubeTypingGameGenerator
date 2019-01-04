#!/bin/sh

# boxファイル
# vagrantフォルダ作成
# vagrant box add 名前 https://app.vagrantup.com/ubuntu/boxes/xenial64/versions/20180917.0.0/providers/virtualbox.box
# vagrant init 名前
# vagrant up

# ./provision.sh production
# ENV=production: 本番環境, ENV=development: 開発環境
#ENV=production
#ENV=development
ENV=$1
if [ ${ENV} != 'development' -a ${ENV} != 'production' ]; then
    echo "第一引数は[development]か[production]を指定してください"
    exit 1
fi

# システム最新化
sudo apt-get update
sudo apt-get -y upgrade

# ... > /dev/null 2>&1は、コンソールには結果が表示されなくなります。

# インストール
# unzip
sudo apt-get -y install unzip
# expect
echo '----installing expect----'
if ! type expect >/dev/null 2>&1; then
  sudo apt-get -y install expect
else
  echo 'already installed expect'
fi
echo '----installed expect----'

# ubuntuユーザのパスワード設定
USER='ubuntu'
PASS='ubuntu'
echo '---- setting password ----'
expect -c "
spawn sudo passwd ${USER}
expect \"password:\"
send \"${PASS}\n\"
expect \"password:\"
send \"${PASS}\n\"
expect \"\$\"
exit 0
"
echo '---- setted password ----'

# タイムゾーンを日本にする変更
echo '---- setting timezone ----'
timezone='Asia/Tokyo'
#timezone='Etc/UTC'
if ! [ `timedatectl | grep -o ${timezone}` ] ; then
  # パスワードが求められたときは入力する
  expect -c "
spawn timedatectl set-timezone ${timezone}
expect \"Password:\"
send \"${PASS}\n\"
expect \"\$\"
exit 0
"
else
  echo "already timezone setted Asia/Tokyo"
fi
echo '---- setted timezone ----'


##          ##
##  Apache  ##
##          ##
apache2 -v > /dev/null 2>&1
if [ $? -eq 127 ]; then
  echo '---- installing apache2 ----';
  sudo apt-get install -y apache2
  ## 設定
  ## virtualHost
  PROJECT=YoutubeTypingGameGenerator
  HOST_NAME=localhost
  # document root を /var/www/html/{project}/publicに変更する
  CONF=/etc/apache2/sites-available/${PROJECT}.conf
  sudo touch ${CONF}
  sudo chmod 666 /etc/apache2/sites-available/${PROJECT}.conf
  sudo echo "<VirtualHost *:80>" >> ${CONF}
  #sudo echo "  ServerName ${HOST_NAME}" >> ${CONF}
  #sudo echo "  ServerAdmin webmaster@virtual.host" >> ${CONF}
  sudo echo "  DocumentRoot /var/www/html/${PROJECT}/public" >> ${CONF}
  sudo echo '  ErrorLog ${APACHE_LOG_DIR}/error.log' >> ${CONF}
  sudo echo '  CustomLog ${APACHE_LOG_DIR}/access.log combined' >> ${CONF}
  sudo echo "  <Directory /var/www/html/${PROJECT}/public>" >> ${CONF}
  sudo echo '    Options FollowSymLinks' >> ${CONF}
  sudo echo '    AllowOverride All' >> ${CONF}
  sudo echo '    Require all granted' >> ${CONF}
  sudo echo '  </Directory>' >> ${CONF}
  sudo echo "</VirtualHost>" >> ${CONF}
  sudo chmod 644 /etc/apache2/sites-available/${PROJECT}.conf
  # virtualhost 設定有効化
  sudo a2ensite ${PROJECT}
  # デフォルトのvirtualhost設定無効化
  sudo a2dissite 000-default.conf
  # mod_rewrite有効化
  sudo a2enmod rewrite
  sudo service apache2 restart
else
  echo '---- already installed apache2 ----'
fi

##       ##
##  PHP  ##
##       ##
# PHP用リポジトリ追加  エンターを押す
php -v > /dev/null 2>&1
if [ $? -eq 127 ]; then
  echo '---- installing PHP ----';
  expect -c "
spawn sudo add-apt-repository ppa:ondrej/php
expect \"Press\"
send \"\n\"
expect \"\$\"
exit 0
"
  sudo apt-get update
  # php7.2
  sudo apt-get install -y php7.2 php7.2-curl php7.2-mbstring php7.2-pgsql php7.2-xml php7.2-zip

  ## xdebug
   if [ ${ENV} = 'development' ]; then
      sudo apt-get install -y php7.2-dev
      sudo apt-get install -y php-xdebug
      phpIni=/etc/php/7.2/apache2/php.ini
      echo 'zend_extension=/usr/lib/php/20170718/xdebug.so' | sudo tee -a ${phpIni}
      echo '[XDebug]' | sudo tee -a ${phpIni}
      echo 'xdebug.remote_autostart=1' | sudo tee -a ${phpIni}
      echo 'xdebug.remote_enable=1' | sudo tee -a ${phpIni}
      echo 'xdebug.remote_host=10.0.2.2' | sudo tee -a ${phpIni}
      echo 'xdebug.remote_port=9000' | sudo tee -a ${phpIni}
      echo 'xdebug.remote_log=/var/log/xdebug.log' | sudo tee -a ${phpIni}
      echo 'xdebug.idekey="PHPSTORM"' | sudo tee -a ${phpIni}
      echo '[Date] | sudo tee -a ${phpIni}'
      echo 'date.timezone = Asia/Tokyo' | sudo tee -a ${phpIni}
      sudo service apache2 restart
   fi
  # ## 設定
  # if [ ${ENV} = 'development' ]; then
  # fi
else
  echo '---- already installed PHP ----'
fi
## Composer
composer -V > /dev/null 2>&1
if [ $? -eq 127 ]; then
  echo '---- installing Composer ----';
  cd ~/
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  #php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
  php composer-setup.php
  php -r "unlink('composer-setup.php');"
  sudo mv composer.phar /usr/local/bin/composer
else
  echo '---- already installed Composer ----'
fi
##################
## PostgreSQL##
################
psql -V > /dev/null 2>&1
if [ $? -eq 127 ]; then
  echo '---- installing PostgreSQL ----';
  sudo apt-get install -y postgresql-9.5
  # Postgres 設定
  SettingFile=/etc/postgresql/9.5/main/pg_hba.conf
  TMPFILE=/tmp/output.txt
  MATCH1='\# "local" is for Unix domain socket connections only'
  MATCH2='\#\sIPv4\slocal\sconnections\:'
  touch ${TMPFILE}
  sudo sed -e "/${MATCH1}/{n;d;}" ${SettingFile} \
  | sudo sed -e "/${MATCH1}/a local   all             all                                     md5" \
  > ${TMPFILE}
  sudo cp ${TMPFILE} ${SettingFile}
  sudo rm ${TMPFILE}
  touch ${TMPFILE}
  sudo sed -e "/${MATCH2}/{n;d;}" ${SettingFile} \
    | sudo sed -e "/${MATCH2}/a host    all             all             0.0.0.0/0               trust" \
    > ${TMPFILE}
  sudo cp ${TMPFILE} ${SettingFile}
  sudo rm ${TMPFILE}

  # postgresユーザのパスワード設定
  USER='postgres'
  PASS='postgres'
  expect -c "
spawn sudo passwd ${USER}
expect \"password:\"
send \"${PASS}\n\"
expect \"password:\"
send \"${PASS}\n\"
expect \"\$\"
exit 0
"
  # 再起動
  sudo /etc/init.d/postgresql  restart
  sudo service apache2 restart
else
  echo '---- already installed PostgreSQL ----'
fi

##              ##
## selenium関係 ## dev環境のときのみ
##              ##
if [ ${ENV} = 'development' ]; then
    cd ~/
    # openJDK
    IS_INSTALLED=`dpkg -l | grep openjdk-8-jdk`
    if [ -z "${IS_INSTALLED}" ];then
      echo '---- installing OpenJDK ----';
      sudo apt-get install -y openjdk-8-jdk
      sudo wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | sudo apt-key add -
      sudo sh -c 'echo "deb http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list'
      sudo apt-get update
    else
      echo '---- already installed OpenJDK ----'
    fi

    # google chrome
    IS_INSTALLED=`dpkg -l | grep google-chrome-stable`
    if [ -z "${IS_INSTALLED}" ];then
      cd ~/
      sudo apt-get install -y google-chrome-stable
      # # google chrome ドライバ
      wget -N https://chromedriver.storage.googleapis.com/2.42/chromedriver_linux64.zip
      unzip chromedriver_linux64.zip
      sudo rm chromedriver_linux64.zip
      sudo mv chromedriver /usr/local/bin/
      sudo chmod +x /usr/local/bin/chromedriver
    fi

    # selenium
    cd ~/
    SELENIUM_SERVER=selenium-server-standalone-3.9.1.jar
    if [ ! -e ${SELENIUM_SERVER} ]; then
      sudo wget http://selenium-release.storage.googleapis.com/3.9/${SELENIUM_SERVER}
      # Xvfb & 日本語フォント のインストール
      sudo apt-get install -y xvfb
      sudo apt-get install -y xfonts-100dpi xfonts-75dpi xfonts-scalable xfonts-cyrillic
      sudo apt-get install -y fonts-ipafont-gothic fonts-ipafont-mincho
    fi

    # selenium起動コマンド sudo sh start_selenium.sh
    StartSelenium=start_selenium.sh
    cd ~/
    if [ ! -e ${StartSelenium} ]; then
      touch ${StartSelenium}
      echo '#!/bin/bash' >> ${StartSelenium}
      echo '# Xvfbの起動' >> ${StartSelenium}
      echo 'sudo Xvfb :1 -screen 0 1366x768x24 &' >> ${StartSelenium}
      echo 'export DISPLAY=:1' >> ${StartSelenium}
      echo '# seleniumの起動' >> ${StartSelenium}
    #  echo '# firefox用？' >> ${StartSelenium}
    #  echo "# java -Dwebdriver.gecko.driver=/usr/local/bin/geckodriver -jar ${SELENIUM_SERVER} &" >> ${StartSelenium}
      echo '# chrome用' >> ${StartSelenium}
      echo "java -jar ${SELENIUM_SERVER} &" >> ${StartSelenium}
    fi
    chmod 755 ${StartSelenium}
    # TODO:自動起動させる

    # selenium killコマンド sudo sh kill_selenium.sh
    KillSelenium=kill_selenium.sh
    cd ~/
    if [ ! -e ${KillSelenium} ]; then
      touch ${KillSelenium}
      echo '#!/bin/bash' >> ${KillSelenium}
      echo '# Xvfb(仮想ディスプレイ)のkill' >> ${KillSelenium}
      echo 'ps aux | grep [X]vfb | awk '\''{ print "sudo kill -9", $2 }'\'' | sh' >> ${KillSelenium}
      echo '# seleniumのkill' >> ${KillSelenium}
      echo 'ps aux | grep [s]elenium-server-standalone | awk '\''{ print "kill -9", $2 }'\'' | sh' >> ${KillSelenium}
      echo '# geckodriverのkill' >> ${KillSelenium}
      echo 'ps aux | grep [g]eckodriver | awk '\''{ print "kill -9", $2 }'\'' | sh' >> ${KillSelenium}
    fi
    chmod 755 ${KillSelenium}
fi
# アプリ初期設定
#echo 'installing appication....'
#cd /var/www/html/
#sudo git clone https://github.com/murase-msk/slim_app.git /var/www/html/slim_app
#cd /var/www/html/slim_app
# パーミッション変更
#sudo chown -R www-data:www-data /var/www/html/slim_app/
#sudo composer install
#php database/init/initDatabase.php


# ngrok
# cd ~
# sudo wget https://bin.equinox.io/c/4VmDzA7iaHb/ngrok-stable-linux-amd64.zip
# sudo unzip ngrok-stable-linux-amd64.zip
# sudo rm ngrok-stable-linux-amd64.zip
# sudo mv ngrok /usr/bin/
# sudo ngrok http 8080

#
# Node.js関係
#
# nodebrew
cd ~
curl -L git.io/nodebrew | perl - setup
# パスを通す
echo 'export PATH=$HOME/.nodebrew/current/bin:$PATH' >> ~/.profile
# .profile読み込み  「.」はsourceコマンド
. ~/.profile
# nodeインストール
nodebrew install-binary v10.14.1
nodebrew use v10.14.1
# http://secon.hatenablog.com/entry/2017/09/24/021055
npm i -D --no-bin-links --no-optional
#
# Vue関係
#
# Vue CLI 3
npm install -g @vue/cli
