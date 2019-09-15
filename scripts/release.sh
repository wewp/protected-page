#!/bin/sh

# The slug of your WordPress.org plugin
PLUGIN_SLUG="protected-page"

set -e
clear

# ASK INFO
echo "--------------------------------------------"
echo "      WordPress.org RELEASER      "
echo "--------------------------------------------"
read -p "TAG AND RELEASE VERSION: " VERSION
echo "--------------------------------------------"
echo ""
echo "Before continuing, confirm that you have done the following :)"
echo ""
read -p " - Added a changelog for "${VERSION}"?"
read -p " - Set version in the readme.txt and main file to "${VERSION}"?"
read -p " - Set stable tag in the readme.txt file to "${VERSION}"?"
read -p " - Updated the POT file?"
read -p " - Committed all changes up to GITHUB?"
echo ""
read -p "PRESS [ENTER] TO BEGIN RELEASING "${VERSION}
clear


# VARS
ROOT_PATH=$(pwd)"/"
TEMP_SVN_REPO=${PLUGIN_SLUG}"-svn"
SVN_REPO="http://plugins.svn.wordpress.org/"${PLUGIN_SLUG}"/"
CURRENT_VERSION_PATH=$ROOT_PATH'versions/'$VERSION

# CHECKOUT #svn DIR IF NOT EXISTS
if [[ ! -d $TEMP_SVN_REPO ]];
then
	echo "Checking out WordPress.org plugin repository"
	svn checkout $SVN_REPO $TEMP_SVN_REPO || { echo "Unable to checkout repo."; exit 1; }
fi

# MOVE INTO SVN DIR
cd $ROOT_PATH$TEMP_SVN_REPO

# REMOVE UNWANTED FILES & FOLDERS
echo "Removing unwanted files"
rm -Rf .git
rm -Rf .github
rm -Rf tests
rm -Rf apigen
rm -f .gitattributes
rm -f .gitignore
rm -f .gitmodules
rm -f .travis.yml
rm -f Gruntfile.js
rm -f package.json
rm -f .jscrsrc
rm -f .jshintrc
rm -f composer.json
rm -f phpunit.xml
rm -f phpunit.xml.dist
rm -f README.md
rm -f .coveralls.yml
rm -f .editorconfig
rm -f .scrutinizer.yml
rm -f apigen.neon
rm -f CHANGELOG.txt
rm -f CONTRIBUTING.md

# UPDATE SVN
echo "Updating SVN"
svn update || { echo "Unable to update SVN."; exit 1; }

# DELETE TRUNK
echo "Replacing trunk"
rm -Rf $ROOT_PATH$TEMP_SVN_REPO'/trunk'

# COPY CURRENT VERSION DIR TO TRUNK
cp -R $CURRENT_VERSION_PATH $ROOT_PATH$TEMP_SVN_REPO'/trunk'

# MOVE INTO wp-repo/assets FOR COPY ASSETS FILES
cd '../..'
cp -R 'wp-repo/assets' $ROOT_PATH$TEMP_SVN_REPO'/'

# MOVE INTO SVN DIR
cd $ROOT_PATH$TEMP_SVN_REPO

#REMOVE SCRIPTS DIR
rm -rf $ROOT_PATH$TEMP_SVN_REPO'/trunk/scripts'
rm -rf $ROOT_PATH$TEMP_SVN_REPO'/trunk/wp-repo'

# DO THE ADD ALL NOT KNOWN FILES UNIX COMMAND
svn add --force . --auto-props --parents --depth infinity -q

# DO THE REMOVE ALL DELETED FILES UNIX COMMAND
MISSING_PATHS=$( svn status | sed -e '/^!/!d' -e 's/^!//' )

# iterate over filepaths
for MISSING_PATH in $MISSING_PATHS; do
    svn rm --force "$MISSING_PATH"
done

# COPY TRUNK TO TAGS/$VERSION
echo "Copying trunk to new tag"
svn copy trunk tags/${VERSION} || { echo "Unable to create tag."; exit 1; }

# DO SVN COMMIT
clear
echo "Showing SVN status"
svn status

# PROMPT USER
echo ""
read -p "PRESS [ENTER] TO COMMIT RELEASE "${VERSION}" TO WORDPRESS.ORG"
echo ""

#SET IMAGE MIME TYPE FOR SVN -> https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/#issues
#svn propset svn:mime-type image/png **/*.png
svn propset svn:mime-type image/jpeg **/*.jpg

# DEPLOY
echo ""
echo "Committing to WordPress.org...this may take a while..."
svn commit -m "Release "${VERSION}", see readme.txt for the changelog." || { echo "Unable to commit."; exit 1; }

# REMOVE THE TEMP DIRS
echo "CLEANING UP"
rm -Rf $ROOT_PATH$TEMP_SVN_REPO


