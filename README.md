# BEA - Remove prefix

Allow to remove prefix when in multisite and/or composer.

It will remove the prefixes added by multisite installs and also handle the custom folder for WordPress when in Composer installation.

# Compatibility

Compatible up to WordPress 4.8.x and especially with following installs :
* single site
* multisites
* multinetworks
* composer

# Warnings

Watchout about your PHP version :

## Depency to php 5.4

Arrays are instancied in php 5.4+ : `[]`.

## Depency to php 7.0

A constant is directly defined with an array as value, which is only available with php 7.0+ :

`const BEA_CHECK_SLUG = [ 'wp', 'blog' ];`

If you want to use it without php 7.0 depedency, you could instead implement an global var with an array.

# Changelog ##

## 1.0.0 - 13 Nov 2017
* Add compatibility for single sites, multisites and multinetworks
* Init
