# PHP 7 Compatibility Checker(php7cc)
#### Introduction
php7cc is a command line tool designed to make migration from PHP 5.3-5.6 to PHP 7 easier.
It searches for potentially troublesome statements in existing code and generates reports containing
file names, line numbers and short problem descriptions. It does not automatically fix
code to work with the new PHP version.

#### What kind of problems does it detect?
It tries to find statements that change their behaviour or lead to fatal errors in PHP 7.
A list of such statements can be found in [php-src repository](https://github.com/php/php-src/blob/PHP-7.0/UPGRADING).
***Although php7cc tries to detect as much problems as accurately as possible, sometimes 100% reliable detection
is very hard to achieve. That's why you should also run a comprehensive test suite for the code
you are going to migrate.***

# Prerequisites
To run php7cc, you need php installed, minimum required version is 5.3.3. PHP 7 is supported,
 but files with syntax errors (for example, invalid numeric literals
 or invalid UTF-8 codepoint escape sequences) can't be processed. You will only get the
 warning message about the first syntax error for such files.
 
You may also need [composer](https://getcomposer.org/) to install php7cc.

# Installation
#### Phar package
You can download a phar package for any stable version from the Github
 [releases](https://github.com/sstalle/php7cc/releases) page.

#### Composer (globally)
Make sure you have composer installed. Then execute the following command:
```bash
composer global require sstalle/php7cc
```
It is also recommended to add ```~/.composer/vendor/bin``` to your ```PATH``` environment
variable:
```bash
export PATH="$PATH:$HOME/.composer/vendor/bin"
```
This makes it possible to run php7cc by entering just the executable name.

#### Composer (locally, per project)
Make sure you have composer installed. Then execute the following command from your project
directory:
```bash
composer require sstalle/php7cc
```


# Usage
Examples in this section assume that you have installed php7cc globally using composer
and that you have added it's vendor binaries directory to your ```PATH```. If this is not
the case, just substitute ```php7cc``` with the correct path to the binary of phar package.
For local per project installation the executable will be located at ```<your_project_path>/vendor/bin/php7cc```.

#### Getting help
To see the full list of available options, run:
```bash
php7cc --help
```

#### Checking a single file or directory
To check a file or a directory, pass its name as the first argument. Directories are checked
recursively.
 
So, to check a file you could run:
```bash
php7cc /path/to/my/file.php
```
To check a directory:
```bash
php7cc /path/to/my/directory/
```

#### Specifying file extensions to check
When checking a directory, you can also specify a comma-separated list of file extensions that
should be checked. By default, only .php files are processed.
 
For example, if you want to check .php, .inc and .lib files, you could run:
```bash
php7cc --extensions=php,inc,lib /path/to/my/directory/
```

#### Excluding file or directories
You can specify a list of absolute or relative paths to exclude from checking.
Relative paths are relative to the checked directories.

So, if you want to exclude vendor and test directories, you could run:
```bash
php7cc --except=vendor --except=/path/to/my/directory/test /path/to/my/directory/
```
In this example, directories ```/path/to/my/directory/vendor```,  ```/path/to/my/directory/test``` and their contents will not be checked.


# Troubleshooting
#### Maximum function nesting level of 100/250/N reached, aborting!
You should increase maximum function nesting level in your PHP or Xdebug config file like this:
```cfg
xdebug.max_nesting_level = 1000
```

# Other useful links
#### Contributing
Please read the [contributing guidelines](CONTRIBUTING.md).
#### Credits
[The list of contributors](https://github.com/sstalle/php7cc/graphs/contributors) is available on the corresponding
 Github page.
