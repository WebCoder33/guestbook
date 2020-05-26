#!/bin/bash

symfony.exe console doctrine:fixtures:load -n
symfony php bin/phpunit
