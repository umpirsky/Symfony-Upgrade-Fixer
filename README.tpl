# Symfony Upgrade Fixer [![Build Status](https://travis-ci.org/umpirsky/Symfony-Upgrade-Fixer.svg)](https://travis-ci.org/umpirsky/Symfony-Upgrade-Fixer)

Analyzes your Symfony project and tries to make it compatible with the new version of Symfony framework.

## Installation

#### Local

```bash
$ composer require umpirsky/symfony-upgrade-fixer
```

#### Global

```bash
$ composer global require umpirsky/symfony-upgrade-fixer
```

Make sure you have ``~/.composer/vendor/bin`` in your ``PATH`` and
you're good to go:

```bash
$ export PATH="$PATH:$HOME/.composer/vendor/bin"
```
Don't forget to add this line in your `.bashrc` file if you want to keep this change after reboot.

## Usage

The ``fix`` command tries to fix as much upgrade issues as possible on a given file or directory:

```bash
$ symfony-upgrade-fixer fix /path/to/dir
$ symfony-upgrade-fixer fix /path/to/file
```

The `--dry-run` option displays the files that need to be fixed but without actually modifying them:

```bash
$ symfony-upgrade-fixer fix /path/to/code --dry-run
```

## Fixers available

| Name  | Description |
| ----  | ----------- |%s

## Contribute

The tool is based on PHP Coding Standards Fixer and the [contributing process](https://github.com/FriendsOfPhp/php-cs-fixer/blob/master/CONTRIBUTING.md) is very similar. I see no sense in re-doing it so far.

If you want to contribute to README, please don't edit README.md directly - it is autogenerated. Edit README.tpl instead and run the command
```bash
$ ./bin/symfony-upgrade-fixer readme > README.md
```
