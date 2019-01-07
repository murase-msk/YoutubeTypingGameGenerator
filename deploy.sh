#!/bin/sh

#cd $1
#touch test.txt
#pwd > test.txt

if [ $1 = 'master' ]; then
#    echo 'master proc start'
    cd /var/www/html/YoutubeTypingGameGenerator
#    echo 'master proc start2'
    git checkout master
    git pull origin master
#    sudo composer intall
#    cd vue-project
#    sudo chown ubuntu:ubuntu  -R .
#    npm install --production --no-save
#    echo 'master proc end'
fi

if [ $1 = 'develop' ]; then
#    git checkout develop
#    git pull origin develop
#    cd vue-project
#    npm run build
fi

