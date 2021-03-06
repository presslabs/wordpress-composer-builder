{
  "name": "presslabs-stack/wordpress",
  "description": "Patched, ready for Stack WordPress to be used as drop-in replacement for johnpbloch/wordpress or roots/wordpress",
  "keywords": [
    "wordpress",
    "blog",
    "cms"
  ],
  "type": "wordpress-core",
  "version": "<?php echo $composer_version; ?>",
  "homepage": "http://wordpress.org/",
  "license": "GPL-2.0-or-later",
  "authors": [
    {
      "name": "Presslabs",
      "homepage": "https://www.presslabs.com/stack",
      "email": "ping@presslabs.com",
      "role": "Patchset Maintainer"
    },
    {
      "name": "WordPress Community",
      "homepage": "http://wordpress.org/about/"
    }
  ],
  "support": {
    "issues": "http://core.trac.wordpress.org/",
    "forum": "http://wordpress.org/support/",
    "wiki": "http://codex.wordpress.org/",
    "irc": "irc://irc.freenode.net/wordpress",
    "source": "http://core.trac.wordpress.org/browser"
  },
  "require": {
    "php": ">=<?php echo $php_min_version; ?>",
    "roots/wordpress-core-installer": ">=1.0.0"
  }
}
