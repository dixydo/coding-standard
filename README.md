# coding-standard

Dixydo coding standard for [PHP CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

This coding standard is based on [PSR-12](https://www.php-fig.org/psr/psr-12/) with some additional
[3rd party](https://github.com/slevomat/coding-standard) and custom sniffs to enforce better code formatting.

Install as a dev dependency using composer
```bash
composer require --dev dixydo/coding-standard
```

Create `ruleset.xml` in the root of your project
```xml
<?xml version="1.0"?>
<ruleset>
    <arg name="basepath" value="."/>
    <arg name="cache" value=".phpcs-cache"/>

    <file>src/</file>

    <config name="installed_paths" value="vendor/dixydo/coding-standard"/>
    <rule ref="Dixydo"/>
</ruleset>
```

## Run PHP CodeSniffer

Run the check and display report
```bash
vendor/bin/phpcs --standard=ruleset.xml
```

Run and automatically fix issues
```bash
vendor/bin/phpcbf --standard=ruleset.xml
```
