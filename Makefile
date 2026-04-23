-include .env
export

# setup for docker-compose-ci build directory
# delete "build" directory to update docker-compose-ci

ifeq (,$(wildcard ./build/))
    $(shell git submodule update --init --remote)
endif

EXTENSION=SemanticGlossary

# docker images
MW_VERSION?=1.43
PHP_VERSION?=8.1
DB_TYPE?=mysql
DB_IMAGE?="mariadb:11.2"

# extensions
SMW_VERSION ?=6.0.1
MM_VERSION ?= 3.1.0
LINGO_VERSION ?= 3.2.3

# composer
# Enables "composer update" inside of extension
COMPOSER_EXT?=true

# nodejs
# Enables node.js related tests and "npm install"
# NODE_JS?=true

# check for build dir and git submodule init if it does not exist
include build/Makefile
