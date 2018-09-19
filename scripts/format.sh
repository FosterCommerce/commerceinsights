#!/bin/bash

yarn prettify
./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix src
