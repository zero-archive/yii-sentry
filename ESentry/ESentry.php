<?php
/**
 * ESentry class file.
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
class ESentry extends CApplicationComponent
{
    /**
     * @var string Path alias of the directory where the Raven PHP can be found.
     */
    public $ravenDir = 'application.vendors.Raven';

    /**
     * @var bool Enabled or disabled extension
     */
    public $enabled = true;

    /**
     * @var string Sentry DSN value
     */
    public $dsn = null;

    /**
     * @var array Raven PHP options
     * @see https://github.com/getsentry/raven-php#configuration
     */
    public $options = array();

    /**
     * @var Raven_Client Raven instance
     */
    private $raven = null;

    /**
     * return Raven_Client
     */
    public function getRavenClient()
    {
        if (!is_array($this->options)) {
            $this->options = array();
        }

        if ($this->raven === null) {
            $this->raven = new Raven_Client($this->dsn, $this->options);
        }

        return $this->raven;
    }

    /**
     * Initializes the application component.
     */
    public function init()
    {
        if (!$this->enabled) {
            return false;
        }

        parent::init();

        // adding Raven library directory to include path
        Yii::import($this->ravenDir . '.*');

        if (!class_exists('Raven_Autoloader', false)) {
            require_once 'lib/Raven/Autoloader.php';
            Yii::registerAutoloader(array('Raven_Autoloader', 'autoload'));
        }
    }

    /**
     * Capture a message to Sentry
     *
     * @param string $message Message string
     * @param string|array $options Additional data with a message
     * @return mixed Event ID
     */
    public function captureMessage($message, $options = array())
    {
        $client = $this->getRavenClient();

        if ($options) {
            $client->extra_context($options);
        }

        return $client->getIdent($client->captureMessage($message));
    }

    /**
     * Capture an exception to Sentry
     *
     * @param $exception Exception
     * @param string|array $options Additional data with a exception
     * @return mixed
     */
    public function captureException($exception, $options = array())
    {
        $client = $this->getRavenClient();

        if ($options) {
            $client->extra_context($options);
        }

        return $client->getIdent($client->captureException($exception));
    }
}
