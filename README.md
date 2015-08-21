# PHP 7 Compatibility Checker(php7cc)
#### Introduction
php7cc is a command line tool designed to make migration from PHP 5.3-5.6 to PHP 7 easier.
It searches for potentially troublesome statements in existing code and generates reports containing
file names, line numbers and short problem descriptions. It does not automatically fix
code to work with the new PHP version.

#### What kind of problems does it detect?
It tries to find statements that change their behaviour or lead to fatal errors in PHP 7.
A list of such statements can be found in [php-src repository](https://github.com/php/php-src/blob/master/UPGRADING).
***php7cc is a work in progress, so not all of them are detected at the time of writing.***

# Usage
#### Prerequisites
To run php7cc, you need php installed, minimum required version is 5.3.3. Required version
 also depends on the code you are going to check: your php interpreter must support all
 the language features that are used in the code. For example, if traits are used
 somewhere in the checked code, you'll need PHP 5.4 or higher.
 
You also need [composer](https://getcomposer.org/) to install php7cc.

#### Installation
The only way to install php7cc as of now is using composer:
```bash
composer create-project sstalle/php7cc php7cc --stability=dev
```

During the installation process, composer will ask:
```
Do you want to remove the existing VCS (.git, .svn..) history? 
```
It's better to answer "no", as it will allow updating php7cc code using ```git pull```.

#### Running
***Note that argument names and order can change until the first stable version is released.
Please consult readme.md file in the installation directory for correct way of passing parameters to your particular
version of the tool.***

Main executable file is bin/php7cc.php. To see the full list of available options, run:
```bash
php bin/php7cc.php --help
```

To check a file or a directory, pass its name as the first argument. Directories are checked
recursively.
 
So, to check a file you could run:
```bash
php bin/php7cc.php /path/to/my/file.php
```
To check a directory:
```bash
php bin/php7cc.php /path/to/my/directory/
```

When checking a directory, you can also specify a comma-separated list of file extensions that
should be checked. By default, only .php files are processed.
 
For example, if you want to check .php, .inc and .lib files, you could run:
```bash
php bin/php7cc.php --extensions=php,inc,lib /path/to/my/directory/
```

You can specify a list of absolute or relative paths to exclude from checking.
Relative paths are relative to the checked directories.

So, if you want to exclude vendor and test directories, you could run:
```bash
php bin/php7cc.php --except=vendor --except=/path/to/my/directory/test /path/to/my/directory/
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