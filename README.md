# YiiSentry

[![Latest Stable Version](https://poser.pugx.org/dotzero/yii-sentry/version)](https://packagist.org/packages/dotzero/yii-sentry)
[![License](https://poser.pugx.org/dotzero/yii-sentry/license)](https://packagist.org/packages/dotzero/yii-sentry)

**YiiSentry** is an extension for the Yii PHP framework that allows developers to push messages and logs to the [Sentry](https://getsentry.com/) service or your own **Sentry server**.

## Requirements:

- [Yii Framework](https://github.com/yiisoft/yii) 1.1.14 or above
- [Composer](http://getcomposer.org/doc/)

## Install

### Via composer:

```bash
$ composer require dotzero/yii-sentry
```

- Add vendor path to your configuration file, attach component and set properties.

```php
'aliases' => array(
    ...
    'vendor' => realpath(__DIR__ . '/../../vendor'),
),
'components' => array(
    ...
    'sentry' => array(
        'class' => 'vendor.dotzero.yii-sentry.ESentry',
        'sentryDir' => 'vendor.sentry.sentry', // Path alias of the sentry-php directory (optional)
        'enabled' => true, // Enabled or disabled extension (optional)
        'dsn' => '[YOUR_DSN_FROM_SENTRY_SERVER]',
        // Raven PHP options (https://github.com/getsentry/sentry-php#configuration)
        'options' => array(
            'site' => 'example.com',
            'tags' => array(
                'php_version' => phpversion(),
            ),
        ),
    ),
),
```

- Add the following to your config file `log` section to enable `ESentryLogRoute`:

```php
'routes' => array(
    ...
    array(
        'class' => 'vendor.dotzero.yii-sentry.ESentryLogRoute',
        'levels' => 'error, warning',
    ),
),
```

## Usage:

```php
// To capture Message
$sentry = Yii::app()->sentry;
$sentry->captureMessage('test', array(
    'param1' => 'value1',
    'param2' => 'value2',
));

// To capture Exception
try {
    throw new Exception('Error Processing Request', 1);
} catch (Exception $e) {
    $sentry = Yii::app()->sentry;
    $sentry->captureException($e);
}
```

## License

Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
