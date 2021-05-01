<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.8
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

/*
 * Configure paths required to find CakePHP + general filepath constants
 */
require __DIR__ . '/paths.php';

/*
 * Bootstrap CakePHP.
 *
 * Does the various bits of setup that CakePHP needs to do.
 * This includes:
 *
 * - Registering the CakePHP autoloader.
 * - Setting the default application paths.
 */
require CORE_PATH . 'config' . DS . 'bootstrap.php';

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ConsoleErrorHandler;
use Cake\Error\ErrorHandler;
use Cake\Http\ServerRequest;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Detection\MobileDetect;

/*
 * See https://github.com/josegonzalez/php-dotenv for API details.
 *
 * Uncomment block of code below if you want to use `.env` file during development.
 * You should copy `config/.env.example` to `config/.env` and set/modify the
 * variables as required.
 *
 * The purpose of the .env file is to emulate the presence of the environment
 * variables like they would be present in production.
 *
 * If you use .env files, be careful to not commit them to source control to avoid
 * security risks. See https://github.com/josegonzalez/php-dotenv#general-security-information
 * for more information for recommended practices.
*/
// if (!env('APP_NAME') && file_exists(CONFIG . '.env')) {
//     $dotenv = new \josegonzalez\Dotenv\Loader([CONFIG . '.env']);
//     $dotenv->parse()
//         ->putenv()
//         ->toEnv()
//         ->toServer();
// }

/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    Configure::config('default', new PhpConfig());
    Configure::load('app', 'default', false);
} catch (Exception $e) {
    exit($e->getMessage() . "\n");
}

/*
 * Load an environment local configuration file to provide overrides to your configuration.
 * Notice: For security reasons app_local.php **should not** be included in your git repo.
 */
if (file_exists(CONFIG . 'app_local.php')) {
    Configure::load('app_local', 'default');
}

/*
 * When debug = true the metadata cache should only last
 * for a short time.
 */
if (Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
    // disable router cache during development
    Configure::write('Cache._cake_routes_.duration', '+2 seconds');
}

/*
 * Set the default server timezone. Using UTC makes time calculations / conversions easier.
 * Check http://php.net/manual/en/timezones.php for list of valid timezone strings.
 */
date_default_timezone_set(Configure::read('App.defaultTimezone'));

/*
 * Configure the mbstring extension to use the correct encoding.
 */
mb_internal_encoding(Configure::read('App.encoding'));

/*
 * Set the default locale. This controls how dates, number and currency is
 * formatted and sets the default language to use for translations.
 */
ini_set('intl.default_locale', Configure::read('App.defaultLocale'));

/*
 * Register application error and exception handlers.
 */
$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    (new ConsoleErrorHandler(Configure::read('Error')))->register();
} else {
    (new ErrorHandler(Configure::read('Error')))->register();
}

/*
 * Include the CLI bootstrap overrides.
 */
if ($isCli) {
    require __DIR__ . '/bootstrap_cli.php';
}

/*
 * Set the full base URL.
 * This URL is used as the base of all absolute links.
 */
$fullBaseUrl = Configure::read('App.fullBaseUrl');
if (!$fullBaseUrl) {
    $s = null;
    if (env('HTTPS')) {
        $s = 's';
    }

    $httpHost = env('HTTP_HOST');
    if (isset($httpHost)) {
        $fullBaseUrl = 'http' . $s . '://' . $httpHost;
    }
    unset($httpHost, $s);
}
if ($fullBaseUrl) {
    Router::fullBaseUrl($fullBaseUrl);
}
unset($fullBaseUrl);

Cache::setConfig(Configure::consume('Cache'));
ConnectionManager::setConfig(Configure::consume('Datasources'));
TransportFactory::setConfig(Configure::consume('EmailTransport'));
Mailer::setConfig(Configure::consume('Email'));
Log::setConfig(Configure::consume('Log'));
Security::setSalt(Configure::consume('Security.salt'));

/*
 * Setup detectors for mobile and tablet.
 */
ServerRequest::addDetector('mobile', function ($request) {
    $detector = new MobileDetect();

    return $detector->isMobile();
});
ServerRequest::addDetector('tablet', function ($request) {
    $detector = new MobileDetect();

    return $detector->isTablet();
});


function getCourses($grouped = false, $toLower = false)
{
    Cache::enable();
    if (($coursesCSV = Cache::read('courses', 'longTerm')) === null) {
        $coursesCSV = file_get_contents("http://ics.mosbach.dhbw.de/ics/calendars.list");
        Cache::write('courses', $coursesCSV, 'longTerm');
    }
    $courses = [];
    foreach (explode("\n", $coursesCSV) as $line) {
        $courseInfo = str_getcsv($line, ';');
        $courses[] = $courseInfo;
    }
    foreach ($courses as $key => &$course) {
        $course = preg_filter("/(([-a-zA-Z]+)\d+\w?)/", '$0', $course[0]);
        if (empty($course)) unset($courses[$key]);
        if ($toLower && $course != null) $courses[$key] = strtolower($courses[$key]);
    }
    sort($courses);
    if (!$grouped) return $courses;

    $courseGroup = [];

    foreach ($courses as $key => &$course) {
        $courseGroup[preg_filter("/(([-a-zA-Z]+)\d+\w?)/", '$2', $course)][strtolower(preg_filter("/(([-a-zA-Z]+)\d+\w?)/", '$1', $course))] = preg_filter("/(([-a-zA-Z]+)\d+\w?)/", '$0', $course);
        if (empty($course)) unset($courses[$key]);
    }

    return $courseGroup;
}

/*
 * You can set whether the ORM uses immutable or mutable Time types.
 * The default changed in 4.0 to immutable types. You can uncomment
 * below to switch back to mutable types.
 *
 * You can enable default locale format parsing by adding calls
 * to `useLocaleParser()`. This enables the automatic conversion of
 * locale specific date formats. For details see
 * @link https://book.cakephp.org/4/en/core-libraries/internationalization-and-localization.html#parsing-localized-datetime-data
 */
// TypeFactory::build('time')
//    ->useMutable();
// TypeFactory::build('date')
//    ->useMutable();
// TypeFactory::build('datetime')
//    ->useMutable();
// TypeFactory::build('timestamp')
//    ->useMutable();
// TypeFactory::build('datetimefractional')
//    ->useMutable();
// TypeFactory::build('timestampfractional')
//    ->useMutable();
// TypeFactory::build('datetimetimezone')
//    ->useMutable();
// TypeFactory::build('timestamptimezone')
//    ->useMutable();

/*
 * Custom Inflector rules, can be set to correctly pluralize or singularize
 * table, model, controller names or whatever other string is passed to the
 * inflection functions.
 */
//Inflector::rules('plural', ['/^(inflect)or$/i' => '\1ables']);
//Inflector::rules('irregular', ['red' => 'redlings']);
//Inflector::rules('uninflected', ['dontinflectme']);
//Inflector::rules('transliteration', ['/å/' => 'aa']);

/**
 * Browsersprache ermitteln
 * 
 * @param array allowed languages
 * @param string default language
 * @param mixed requested language (null = server default)
 * @param bool strict mode (default true)
 * @return string result language
 */
function lang_getfrombrowser ($allowed_languages, $default_language, $lang_variable = null, $strict_mode = true) {
    // $_SERVER['HTTP_ACCEPT_LANGUAGE'] verwenden, wenn keine Sprachvariable mitgegeben wurde
    if ($lang_variable === null) {
      $lang_variable = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }
  
    // wurde irgendwelche Information mitgeschickt?
    if (empty($lang_variable)) {
      // Nein? => Standardsprache zurückgeben
      return $default_language;
    }
  
    // Den Header auftrennen
    $accepted_languages = preg_split('/,\s*/', $lang_variable);
  
    // Die Standardwerte einstellen
    $current_lang = $default_language;
    $current_q = 0;
  
    // Nun alle mitgegebenen Sprachen abarbeiten
    foreach ($accepted_languages as $accepted_language) {
      // Alle Infos über diese Sprache rausholen
      $res = preg_match (
        '/^([a-z]{1,8}(?:-[a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i',
        $accepted_language,
        $matches
      );
  
      // war die Syntax gültig?
      if (!$res) {
        // Nein? Dann ignorieren
        continue;
      }
  
      // Sprachcode holen und dann sofort in die Einzelteile trennen
      $lang_code = explode ('-', $matches[1]);
  
      // Wurde eine Qualität mitgegeben?
      if (isset($matches[2])) {
        // die Qualität benutzen
        $lang_quality = (float)$matches[2];
      } else {
        // Kompabilitätsmodus: Qualität 1 annehmen
        $lang_quality = 1.0;
      }
  
      // Bis der Sprachcode leer ist...
      while (count ($lang_code)) {
        // mal sehen, ob der Sprachcode angeboten wird
        if (in_array (strtolower (join ('-', $lang_code)), $allowed_languages)) {
          // Qualität anschauen
          if ($lang_quality > $current_q) {
            // diese Sprache verwenden
            $current_lang = strtolower (join ('-', $lang_code));
            $current_q = $lang_quality;
            // Hier die innere while-Schleife verlassen
            break;
          }
        }
        // Wenn wir im strengen Modus sind, die Sprache nicht versuchen zu minimalisieren
        if ($strict_mode) {
          // innere While-Schleife aufbrechen
          break;
        }
        // den rechtesten Teil des Sprachcodes abschneiden
        array_pop ($lang_code);
      }
    }
  
    // die gefundene Sprache zurückgeben
    return $current_lang;
  }
