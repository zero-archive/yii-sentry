# YiiSentry

**YiiSentry** is an extension for the Yii PHP framework that allows developers to push messages and logs to the [Sentry](https://getsentry.com/) service or your own **Sentry server**.

## Requirements:

* Yii Framework 1.1.0 or later
* [raven-php](https://github.com/getsentry/raven-php)

## Installation:

- Extract the release folder `ESentry` under `protected/extensions`
- Download and extract [raven-php](https://github.com/getsentry/raven-php) under `protected/vendors/Raven`
- Add the following to your **config file** `components` section:

```php
<?php
    'sentry' => array(
        'class' => 'ext.ESentry.ESentry',
        'ravenDir' => 'application.vendors.Raven', // Path alias of the raven-php directory (optional)
        'enabled' => true, // Enabled or disabled extension (optional)
        'dsn' => '[YOUR_DSN_FROM_SENTRY_SERVER]',
        // Raven PHP options (https://github.com/getsentry/raven-php#configuration)
        'options' => array( 
            'site' => 'example.com',
            'tags' => array(
                'php_version' => phpversion(),
            ),
        ),
    ),
```

- Add the following to your config file 'log' section to enable ESentryLogRoute:

```php
<?php
    'routes' => array(
        ...
        array(
            'class' => 'application.extensions.ESentry.ESentryLogRoute',
            'levels' => 'error, warning',
        ),
    ),
```
