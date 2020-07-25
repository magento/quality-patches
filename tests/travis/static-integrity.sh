#!/bin/bash

# © Copyright 2013-present Adobe. All rights reserved.
#
# This file is licensed under OSL 3.0 or your existing commercial license or subscription
# agreement with Magento or its Affiliates (the "Agreement).
#
# You may obtain a copy of the OSL 3.0 license at http://opensource.org/licenses/osl-3.0.php Open
# Software License (OSL 3.0) or by contacting engcom@adobe.com for a copy.
#
# Subject to your payment of fees and compliance with the terms and conditions of the Agreement,
# the Agreement supersedes the OSL 3.0 license with respect to this file.

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

./vendor/bin/phpcs ./src --standard=./tests/static/phpcs-ruleset.xml -p -n
./vendor/bin/phpmd ./src xml ./tests/static/phpmd-ruleset.xml
./vendor/bin/phpunit --configuration ./tests/integrity
