#!/usr/bin/env bash
set -eu

if [[ ${1-} = 'backup' ]] ; then

    #Store a backup of the current WordPress folder in case something goes wrong.
    backup_name=$(date +%s)_$(date +%Y-%m-%d__%H:%M)
    cp -R ./wordpress backups/${backup_name}

    #Clean the wordpress folder and keep the plugins & themes.
    rm -Rf ./tmp
    mkdir ./tmp
    cp -R ./wordpress/wp-content/plugins ./tmp/plugins
    cp -R ./wordpress/wp-content/themes ./tmp/themes
    # rm -Rf ./wordpress/*
    # mkdir -p ./wordpress/wp-content

    rm -Rf ./wordpress/wp-content/*
    mv ./tmp/plugins ./wordpress/wp-content
    mv ./tmp/themes ./wordpress/wp-content

    # backup database
    scripts/export.sh ${backup_name}

fi

# Start docker containers.
docker-compose stop
docker-compose rm --all --force
docker-compose build
docker-compose up
