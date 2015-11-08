# Symfony Upgrade Fixer [![Build Status](https://travis-ci.org/umpirsky/Symfony-Upgrade-Fixer.svg)](https://travis-ci.org/umpirsky/Symfony-Upgrade-Fixer)

Analyzes your Symfony project and tries to make it compatible with the new version of Symfony framework.

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
| ----  | ----------- |
| property_access | Renamed PropertyAccess::getPropertyAccessor to PropertyAccess::createPropertyAccessor. |
| form_type_names | Instead of referencing types by name, you should reference them by their fully-qualified class name (FQCN) instead. |
| form_events | The events PRE_BIND, BIND and POST_BIND were renamed to PRE_SUBMIT, SUBMIT and POST_SUBMIT. |
| progress_bar | ProgressHelper has been removed in favor of ProgressBar. |
| inherit_data_aware_iterator | The class VirtualFormAwareIterator was renamed to InheritDataAwareIterator. |

## Contribute

The tool is based on PHP Coding Standards Fixer and the [contributing process](https://github.com/FriendsOfPhp/php-cs-fixer/blob/master/CONTRIBUTING.md) is very similar. I see no sense in re-doing it so far.
