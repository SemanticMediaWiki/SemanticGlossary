#! /bin/bash

# Utility adapted from the tool for creating SMW tarballs
# By Jeroen De Dauw < jeroendedauw@gmail.com >
# Released under the GNU GPL v2+

# Parameters:
# $1: version fed to composer, defaults to dev-master
# $2: version used in the tarball name, defaults to $1

COMPOSER_VERSION="$1"
VERSION="$2"
if [ "$COMPOSER_VERSION" == "" ]; then
	COMPOSER_VERSION="dev-master"
fi

if [ "$VERSION" == "" ]; then
	VERSION=$COMPOSER_VERSION
fi

NAME="Semantic Glossary $VERSION (+dependencies)"
DIR="SemanticGlossary"

BUILD_DIR="build-$VERSION"

rm -rf $BUILD_DIR
mkdir $BUILD_DIR
cd $BUILD_DIR

composer create-project mediawiki/semantic-glossary $DIR $COMPOSER_VERSION -s dev --prefer-dist --no-dev --no-install --ignore-platform-reqs

cd $DIR
composer remove mediawiki/semantic-media-wiki --update-no-dev --ignore-platform-reqs --optimize-autoloader
cd -

zip -qro9 "$NAME.zip" $DIR
#7z a "$NAME.7z" $DIR
tar -czf "$NAME.tar.gz" $DIR

cd ..
set -x
ls -lap $BUILD_DIR