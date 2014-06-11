<?php
/**
 * ESentryLogRoute class file.
 *
 * @package YiiSentry
 * @author dotzero <mail@dotzero.ru>
 * @link http://www.dotzero.ru
 * @link https://github.com/dotzero/YiiSentry
 * @license MIT
 * @version 1.0 (11-jun-2014)
 */

/**
 * YiiSentry is an extension for the Yii PHP framework that allows developers to push messages and logs
 * to the Sentry (https://getsentry.com/) service or your own Sentry server.
 *
 * Requirements:
 * Yii Framework 1.1.0 or later
 * raven-php
 *
 * Installation:
 * - Extract ESentry folder under 'protected/extensions'
 * - Download and extract raven-php (https://github.com/getsentry/raven-php) under 'protected/vendors'
 * - Add the following to your config file 'components' section:
 *
 *  'sentry' => array(
 *      'class' => 'ext.ESentry.ESentry',
 *      'ravenDir' => 'application.vendors.Raven', // Path alias of the raven-php directory (optional)
 *      'enabled' => true, // Enabled or disabled extension (optional)
 *      'dsn' => '[YOUR_DSN_FROM_SENTRY_SERVER]',
 *      'options' => array( // Raven PHP options (https://github.com/getsentry/raven-php#configuration)
 *          'site' => 'example.com',
 *          'tags' => array(
 *              'php_version' => phpversion(),
 *          ),
 *      ),
 *  ),
 *
 *  - Add the following to your config file 'log' section to enable ESentryLogRoute:
 *
 *  'routes' => array(
 *      ...
 *      array(
 *          'class' => 'application.extensions.ESentry.ESentryLogRoute',
 *          'levels' => 'error, warning',
 *      ),
 *  ),
 */
class ESentryLogRoute extends CLogRoute
{
    /**
     * @var string Component ID of the ESentry
     */
    public $sentryComponent = 'sentry';

    /**
     * Processes log messages and sends them to specific destination.
     * Derived child classes must implement this method.
     *
     * @param array $logs list of messages. Each array element represents one message
     * with the following structure:
     * array(
     *   [0] => message (string)
     *   [1] => level (string)
     *   [2] => category (string)
     *   [3] => timestamp (float, obtained by microtime(true));
     * @return mixed
     */
    protected function processLogs($logs)
    {
        if (count($logs) == 0) {
            return false;
        }

        $client = $this->getRavenClient();

        if ($client) {
            foreach ($logs AS $log) {
                $format = explode("\n", $log[0]);
                $message = strip_tags(array_shift($format));

                $client->captureMessage(
                    $message,
                    array(
                        'level' => $log[1],
                        'category' => $log[2],
                        'timestamp' => $log[3],
                    )
                );
            }
        }
    }

    /**
     * Returns the object of Raven_Client class.
     * Also ensure ESentry that application component exists and is initialised.
     *
     * @return bool|Raven_Client The object of Raven_Client class, or false if the
     * client is not available
     */
    private function getRavenClient()
    {
        if (Yii::app()->hasComponent($this->sentryComponent)) {
            $sentry = Yii::app()->getComponent($this->sentryComponent);

            if ($sentry->getIsInitialized()) {
                return $sentry;
            } else {
                Yii::log(
                    $this->sentryComponent . ' is not initialised',
                    CLogger::LEVEL_TRACE,
                    'application.ESentryLogRoute'
                );
            }
        } else {
            Yii::log(
                $this->sentryComponent . ' does not exist',
                CLogger::LEVEL_TRACE,
                'application.ESentryLogRoute'
            );
        }

        return false;
    }
}
