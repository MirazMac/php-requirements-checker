![PHP Requirements Checker](https://i.postimg.cc/PxhHbVsY/pcr.png)

# PHP Requirements Checker
A PHP library to check the current environment against a set of defined requirements. Currently it supports checking for PHP version, OS, extensions, php.ini values, functions, classes, apache modules and local files and folders.

### Install via composer

```shell
composer require mirazmac/php-requirements-checker
```

### Manual Install

Download the latest release. Extract and require **src/Checker.php** in your code. But it's highly recommended to use [Composer](http://getcomposer.org).

```php
require 'src/Checker.php';
```



## Usage

```php
use MirazMac\Requirements\Checker;

$checker = new Checker;

// Define requirements
$checker->requirePhpVersion('>=5.6')
        ->requirePhpExtensions(['ffmpeg', 'mbstring'])
        ->requireFunctions(['random_bytes'])
        ->requireFile('../composer.json', Checker::CHECK_FILE_EXISTS)
        ->requireDirectory('../src', Checker::CHECK_IS_READABLE)
        ->requireIniValues([
            'allow_url_fopen' => true,
            'short_open_tag' => true,
            'memory_limit'  => '>=64M',
        ]);

// Runs the check and returns parsed requirements as an array
// Contains parsed requirements with state of the current values
// and their comparison result
$output = $checker->check();

// Should be called after running check() to see if requirements has met or not
$satisfied = $checker->isSatisfied();

if ($satisfied) {
    echo "Requirements are met.";
} else {
    echo join(', ', $checker->getErrors());
}

```

## Supported Requirement Checks

Every supported requirements check begins with the word **require**. They return the class instance that means they're chain-able. These are the supported checks:

### requirePhpVersion(string $version);
You can check if current PHP version matches your desired version using this method. The parameter ``$version`` should be a string containing your desired PHP version. Comparison operators can be prepended at the very beginning of the string.

```php
$checker->requirePhpVersion('7.0.0');

// Note the comparison operator
// Supports comparison operators: <, >, =, >=
$checker->requirePhpVersion('>=7.0.0');
```

### requireOS(string $os);
You can check if current OS matches with your desired operating system. The parameter ``$os`` must have one of the following values:
``Checker::OS_UNIX``, ``Checker::OS_DOS``

```php
$checker->requireOS(Checker::OS_UNIX);
```


### requireIniValues(array $values)
Use this to validate a set of php.ini config values to compare against your provided values. The parameter ``$values`` should be an array as key => value fashion, where the key would contain the php.ini config var and the value should be the desired value. Like ``requirePhpVersion();`` comparison operators can be prepended at the very beginning of the value.
To keep things simple and neat, use ``boolean`` instead of using ``On/1/Off/0`` for the check.

```php
$checker->requireIniValues([
    // Will check if file_uploads is enabled or not
    // Notice the usage of boolean instead of On/Off/1/0
    'file_uploads' => true,

    // Note the comparison operator > before the desired value
    // This means the library will check if post_max_size is greater than 2M or not
    'post_max_size' => '>2M',

    // Set a value to `NULL` to just skip the check for that value
    // Useful when you don't wanna compare but want to fetch the
    // current value on the parsed requirements array
    'safe_mode'   => null,
]);
```

### requirePhpExtensions(array $extensions)
To make sure provided extensions are loaded. Parameter ``$extenstions`` should be an array with the extension names.

```php
$checker->requirePhpExtensions([
    'openssl', 'mbstring', 'curl'
]);
```

### requireFunctions(array $functions)
To make sure provided functions are loaded. Parameter ``$functions`` should be an array with the function names.

```php
$checker->requireFunctions([
    'apcu_fetch', 'mb_substr', 'curl_init'
]);
```

### requireClasses(array $classes)
To make sure provided classes are loaded. Parameter ``$classes`` should be an array with the class names (namespaced or global namespace will be used).

```php
$checker->requireClasses([
    'PDO', 'finfo', 'stdClass'
]);
```

### requireApacheModules(array $modules)
To make sure provided modules are loaded. Parameter ``$modules`` should be an array with the module names.
NOTE: This check will only run if current server is Apache.

```php
$checker->requireApacheModules([
    'mod_rewrite', 'mod_mime'
]);
```

### requireFile(string $path, string $check = self::CHECK_FILE_EXISTS)
To check permissions and existence of certain files and directories. The parameter ``$path`` should be path to any file or directory.
The parameter ``$check`` is the check name that would be performed on the path. The supported values are:
* ``Checker::CHECK_IS_FILE`` - Runs ``is_file()`` on the path.

* ``Checker::CHECK_IS_DIR`` Runs ``is_dir()`` on the path.

* ``Checker::CHECK_IS_READABLE`` Runs ``is_readable()`` on the path.

* ``Checker::CHECK_IS_WRITABLE`` Runs ``is_writable()`` on the path.

* ``Checker::CHECK_FILE_EXISTS`` Runs ``file_exists()`` on the path.

NOTE: ``requireDirectory()`` is an alias of this method.

```php
$checker->requireFile('app/config.ini', Checker::CHECK_IS_FILE)
        ->requireFile('app/cache', Checker::CHECK_IS_WRITABLE);
        ->requireDirectory('app/cache', Checker::CHECK_IS_DIR);
```

## Todos
* Write tests
* Write extended docs
