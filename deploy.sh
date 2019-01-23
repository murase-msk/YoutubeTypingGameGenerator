#!/bin/sh

#cd $1
#touch test.txt
#pwd > test.txt

if [ $1 = 'master' ]; then
    # cd /var/www/html/
    # sudo chown -R www-data:www-data YoutubeTypingGameGenerator
    # sudo chmod 777 -R YoutubeTypingGameGenerator
    cd /var/www/html/YoutubeTypingGameGenerator
    #git checkout master
    sudo git pull origin master
    # キャッシュ削除
    cd cache
    sudo rm -Rf ./* ./.*
fi

if [ $1 = 'develop' ]; then
#    git checkout develop
#    git pull origin develop
#    cd vue-project
#    npm run build
fi

