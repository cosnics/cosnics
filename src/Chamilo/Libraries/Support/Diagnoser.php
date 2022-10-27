<?php
namespace Chamilo\Libraries\Support;

use Chamilo\Configuration\Service\ConfigurationConsulter;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\SimpleTableRenderer;
use Chamilo\Libraries\Format\Tabs\AbstractTab;
use Chamilo\Libraries\Format\Tabs\ContentTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Format\Tabs\TabsRenderer;
use Chamilo\Libraries\Platform\ChamiloRequest;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Doctrine\DBAL\Connection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Support
 * @author  spou595 Class that is responsible for generating diagnostic information about the system
 */
class Diagnoser
{
    public const STATUS_ERROR = 3;
    public const STATUS_INFORMATION = 4;
    public const STATUS_OK = 1;
    public const STATUS_WARNING = 2;

    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected ConfigurationConsulter $configurationConsulter;

    protected Connection $connection;

    protected DatetimeUtilities $datetimeUtilities;

    protected SimpleTableRenderer $diagnoserTableRenderer;

    protected PathBuilder $pathBuilder;

    protected ChamiloRequest $request;

    protected TabsRenderer $tabsRenderer;

    protected Translator $translator;

    public function __construct(
        ConfigurationConsulter $configurationConsulter, Connection $connection, ChamiloRequest $request,
        PathBuilder $pathBuilder, ConfigurablePathBuilder $configurablePathBuilder, Translator $translator,
        DatetimeUtilities $datetimeUtilities, TabsRenderer $tabsRenderer, SimpleTableRenderer $diagnoserTableRenderer
    )
    {
        $this->configurationConsulter = $configurationConsulter;
        $this->connection = $connection;
        $this->request = $request;
        $this->pathBuilder = $pathBuilder;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->translator = $translator;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->tabsRenderer = $tabsRenderer;
        $this->diagnoserTableRenderer = $diagnoserTableRenderer;
    }

    public function render(): string
    {
        $sections = ['Chamilo', 'Php', 'Database', 'Webserver'];

        $tabs = new TabsCollection();

        foreach ($sections as $section)
        {
            $data = call_user_func([$this, 'get' . $section . 'Data']);
            $table = $this->getDiagnoserTableRenderer()->render($data);

            $tabs->add(
                new ContentTab(
                    $section, $this->getTranslation(ucfirst($section) . 'Title'), $table, null,
                    AbstractTab::DISPLAY_TITLE
                )
            );
        }

        return $this->getTabsRenderer()->render('diagnoser', $tabs);
    }

    /**
     * @param mixed $current_value
     * @param mixed $expected_value
     * @param mixed $formatter
     */
    public function build_setting(
        int $status, string $section, string $title, string $url, $current_value, $expected_value, $formatter,
        string $comment
    ): array
    {
        switch ($status)
        {
            case self::STATUS_OK :
                $glyph = new FontAwesomeGlyph(
                    'check-circle', ['text-success'], $status, 'fas'
                );
                break;
            case self::STATUS_WARNING :
                $glyph = new FontAwesomeGlyph(
                    'exclamation-circle', ['text-warning'], $status, 'fas'
                );
                break;
            case self::STATUS_ERROR :
                $glyph = new FontAwesomeGlyph(
                    'minus-circle', ['text-danger'], $status, 'fas'
                );
                break;
            case self::STATUS_INFORMATION :
            default:
                $glyph = new FontAwesomeGlyph(
                    'lightbulb', ['text-info'], $status, 'fas'
                );
                break;
        }

        $image = $glyph->render();

        if ($url)
        {
            $url = $this->getLink($title, $url);
        }
        else
        {
            $url = $title;
        }

        $formatted_current_value = $current_value;
        $formatted_expected_value = $expected_value;

        if ($formatter)
        {
            if (method_exists($this, 'format_' . $formatter))
            {
                $formatted_current_value = call_user_func([$this, 'format_' . $formatter], $current_value);
                $formatted_expected_value = call_user_func([$this, 'format_' . $formatter], $expected_value);
            }
        }

        return [$image, $section, $url, $formatted_current_value, $formatted_expected_value, $comment];
    }

    public function formatOnOff(string $value): string
    {
        return $value ? $this->getTranslation('ConfirmOn', []) : $this->getTranslation(
            'ConfirmOff', []
        );
    }

    public function formatYesNo(string $value): string
    {
        return $value ? $this->getTranslation('ConfirmYes', []) : $this->getTranslation(
            'ConfirmNo', []
        );
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    public function getChamiloData(): array
    {
        $array = [];

        $writable_folders = [];
        $writable_folders[] = $this->pathBuilder->getPublicStoragePath();
        $writable_folders[] = $this->configurablePathBuilder->getRepositoryPath();
        $writable_folders[] = $this->configurablePathBuilder->getTemporaryPath();

        foreach ($writable_folders as $folder)
        {
            $writable = is_writable($folder);
            $status = $writable ? self::STATUS_OK : self::STATUS_ERROR;
            $array[] = $this->build_setting(
                $status, '[FILES]', $this->getTranslation('IsWritable') . ': ' . $folder,
                'http://be2.php.net/manual/en/function.is-writable.php', $writable, 1, 'yes_no',
                $this->getTranslation('DirectoryMustBeWritable')
            );
        }

        $installationPath = $this->pathBuilder->namespaceToFullPath('Chamilo\Core\Install');

        $exists = !file_exists($installationPath);
        $status = $exists ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[FILES]', $this->getTranslation('DirectoryExists') . ': ' . $installationPath,
            'http://be2.php.net/file_exists', $writable, 0, 'yes_no', $this->getTranslation('DirectoryShouldBeRemoved')
        );

        $date = $this->configurationConsulter->getSetting(['Chamilo\Configuration', 'general', 'install_date']);
        $date = $this->datetimeUtilities->formatLocaleDate(
            $this->getTranslation('DateFormatShort', []) . ', ' . $this->getTranslation('TimeNoSecFormat', []), $date
        );
        $array[] = $this->build_setting(
            1, '[INFORMATION]', $this->getTranslation('InstallDate'), '', $date, '', null,
            $this->getTranslation('InstallDateInfo')
        );

        return $array;
    }

    /**
     * @return string[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getDatabaseData(): array
    {
        $databaseName = $this->connection->getDatabase();
        $driverClass = get_class($this->connection->getDriver());
        $databasePlatformClass = get_class($this->connection->getDatabasePlatform());

        $array = [];

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[Database]', 'databaseName', '', $databaseName, null, null,
            $this->getTranslation('DatabaseName')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[Database]', 'driverClass', '', $driverClass, null, null,
            $this->getTranslation('DriverClass')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[Database]', 'databasePlatformClass', '', $databasePlatformClass, null, null,
            $this->getTranslation('DatabasePlatformClass')
        );

        return $array;
    }

    public function getDiagnoserTableRenderer(): SimpleTableRenderer
    {
        return $this->diagnoserTableRenderer;
    }

    public function getLink(string $title, string $url): string
    {
        return '<a href="' . $url . '" target="about:bank">' . $title . '</a>';
    }

    /**
     * @return string[]
     */
    public function getPhpData(): array
    {
        $array = [];

        // General Functions

        $version = phpversion();
        $status = $version > '5.2' ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[PHP]', 'phpversion()', 'http://www.php.net/manual/en/function.phpversion.php', phpversion(),
            '>= 5.2', null, $this->getTranslation('PHPVersionInfo')
        );

        $setting = ini_get('output_buffering');
        $req_setting = 0;
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'output_buffering',
            'http://www.php.net/manual/en/outcontrol.configuration.php#ini.output-buffering', $setting, $req_setting,
            'on_off', $this->getTranslation('OutputBufferingInfo')
        );

        $setting = ini_get('file_uploads');
        $req_setting = 1;
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'file_uploads', 'http://www.php.net/manual/en/ini.core.php#ini.file-uploads', $setting,
            $req_setting, 'on_off', $this->getTranslation('FileUploadsInfo')
        );

        $req_setting = 0;

        $setting = ini_get('magic_quotes_runtime');
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'magic_quotes_runtime',
            'http://www.php.net/manual/en/ini.core.php#ini.magic-quotes-runtime', $setting, $req_setting, 'on_off',
            $this->getTranslation('MagicQuotesRuntimeInfo')
        );

        $setting = ini_get('safe_mode');
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[INI]', 'safe_mode', 'http://www.php.net/manual/en/ini.core.php#ini.safe-mode', $setting,
            $req_setting, 'on_off', $this->getTranslation('SafeModeInfo')
        );

        $setting = ini_get('register_globals');
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'register_globals', 'http://www.php.net/manual/en/ini.core.php#ini.register-globals',
            $setting, $req_setting, 'on_off', $this->getTranslation('RegisterGlobalsInfo')
        );

        $setting = ini_get('short_open_tag');
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[INI]', 'short_open_tag', 'http://www.php.net/manual/en/ini.core.php#ini.short-open-tag',
            $setting, $req_setting, 'on_off', $this->getTranslation('ShortOpenTagInfo')
        );

        $setting = ini_get('magic_quotes_gpc');
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'magic_quotes_gpc', 'http://www.php.net/manual/en/ini.core.php#ini.magic_quotes_gpc',
            $setting, $req_setting, 'on_off', $this->getTranslation('MagicQuotesGpcInfo')
        );

        $setting = ini_get('display_errors');
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[INI]', 'display_errors', 'http://www.php.net/manual/en/ini.core.php#ini.display_errors',
            $setting, $req_setting, 'on_off', $this->getTranslation('DisplayErrorsInfo')
        );

        $setting = ini_get('upload_max_filesize');
        $req_setting = '10M - 100M - ...';
        if ($setting < 10)
        {
            $status = self::STATUS_ERROR;
        }
        if ($setting >= 10 && $setting < 100)
        {
            $status = self::STATUS_WARNING;
        }
        if ($setting >= 100)
        {
            $status = self::STATUS_OK;
        }
        $array[] = $this->build_setting(
            $status, '[INI]', 'upload_max_filesize',
            'http://www.php.net/manual/en/ini.core.php#ini.upload_max_filesize', $setting, $req_setting, null,
            $this->getTranslation('UploadMaxFilesizeInfo')
        );

        $setting = ini_get('default_charset');
        if ($setting == '')
        {
            $setting = null;
        }
        $req_setting = 'UTF-8';
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'default_charset', 'http://www.php.net/manual/en/ini.core.php#ini.default-charset',
            $setting, $req_setting, null, $this->getTranslation('DefaultCharsetInfo')
        );

        $setting = ini_get('max_execution_time');
        $req_setting = '300 (' . $this->getTranslation('Minimum') . ')';
        $status = $setting >= 300 ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[INI]', 'max_execution_time', 'http://www.php.net/manual/en/ini.core.php#ini.max-execution-time',
            $setting, $req_setting, null, $this->getTranslation('MaxExecutionTimeInfo')
        );

        $setting = ini_get('max_input_time');
        $req_setting = '300 (' . $this->getTranslation('Minimum') . ')';
        $status = $setting >= 300 ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[INI]', 'max_input_time', 'http://www.php.net/manual/en/ini.core.php#ini.max-input-time',
            $setting, $req_setting, null, $this->getTranslation('MaxInputTimeInfo')
        );

        $req_setting = '10M - 100M - ...';

        $setting = ini_get('memory_limit');
        if ($setting < 10)
        {
            $status = self::STATUS_ERROR;
        }
        if ($setting >= 10 && $setting < 100)
        {
            $status = self::STATUS_WARNING;
        }
        if ($setting >= 100)
        {
            $status = self::STATUS_OK;
        }
        $array[] = $this->build_setting(
            $status, '[INI]', 'memory_limit', 'http://www.php.net/manual/en/ini.core.php#ini.memory-limit', $setting,
            $req_setting, null, $this->getTranslation('MemoryLimitInfo')
        );

        $setting = ini_get('post_max_size');
        if ($setting < 10)
        {
            $status = self::STATUS_ERROR;
        }
        if ($setting >= 10 && $setting < 100)
        {
            $status = self::STATUS_WARNING;
        }
        if ($setting >= 100)
        {
            $status = self::STATUS_OK;
        }
        $array[] = $this->build_setting(
            $status, '[INI]', 'post_max_size', 'http://www.php.net/manual/en/ini.core.php#ini.post-max-size', $setting,
            $req_setting, null, $this->getTranslation('PostMaxSizeInfo')
        );

        $setting = ini_get('variables_order');
        $req_setting = 'GPCS';
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'variables_order', 'http://www.php.net/manual/en/ini.core.php#ini.variables-order',
            $setting, $req_setting, null, $this->getTranslation('VariablesOrderInfo')
        );

        $setting = ini_get('session.gc_maxlifetime');
        $req_setting = '4320';
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[SESSION]', 'session.gc_maxlifetime',
            'http://www.php.net/manual/en/ini.core.php#session.gc-maxlifetime', $setting, $req_setting, null,
            $this->getTranslation('SessionGCMaxLifetimeInfo')
        );

        // Extensions
        $extensions = [
            'gd' => 'http://www.php.net/gd',
            'mysqli' => 'http://www.php.net/mysqli',
            'pcre' => 'http://www.php.net/pcre',
            'session' => 'http://www.php.net/session',
            'standard' => 'http://www.php.net/spl',
            'zlib' => 'http://www.php.net/zlib',
            'xsl' => 'http://be2.php.net/xsl'
        ];

        foreach ($extensions as $extension => $url)
        {
            $loaded = extension_loaded($extension);
            $status = $loaded ? self::STATUS_OK : self::STATUS_ERROR;
            $array[] = $this->build_setting(
                $status, '[EXTENSION]', $this->getTranslation('ExtensionLoaded') . ': ' . $extension, $url, $loaded, 1,
                'yes_no', $this->getTranslation('ExtensionMustBeLoaded')
            );
        }

        return $array;
    }

    public function getTabsRenderer(): TabsRenderer
    {
        return $this->tabsRenderer;
    }

    public function getTranslation(
        string $variable, array $parameters = [], string $context = StringUtilities::LIBRARIES
    ): string
    {
        return $this->translator->trans($variable, $parameters, $context);
    }

    /**
     * @return string[]
     */
    public function getWebserverData(): array
    {
        $array = [];

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', '$_SERVER["SERVER_ADDR"]',
            'http://be.php.net/reserved.variables.server', $_SERVER['SERVER_ADDR'], null, null,
            $this->getTranslation('ServerIPInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', '$_SERVER["SERVER_SOFTWARE"]',
            'http://be.php.net/reserved.variables.server', $_SERVER['SERVER_SOFTWARE'], null, null,
            $this->getTranslation('ServerSoftwareInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', '$_SERVER["REMOTE_ADDR"]',
            'http://be.php.net/reserved.variables.server', $_SERVER['REMOTE_ADDR'], null, null,
            $this->getTranslation('ServerRemoteInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', '$_SERVER["HTTP_USER_AGENT"]',
            'http://be.php.net/reserved.variables.server', $_SERVER['HTTP_USER_AGENT'], null, null,
            $this->getTranslation('ServerRemoteInfo')
        );

        $path = $this->request->getUri();
        $request = $_SERVER['REQUEST_URI'];
        $status = $request != $path ? self::STATUS_ERROR : self::STATUS_OK;
        $array[] = $this->build_setting(
            $status, '[SERVER]', '$_SERVER["REQUEST_URI"]', 'http://be.php.net/reserved.variables.server', $request,
            $path, null, $this->getTranslation('RequestURIInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', '$_SERVER["SERVER_PROTOCOL"]',
            'http://be.php.net/reserved.variables.server', $_SERVER['SERVER_PROTOCOL'], null, null,
            $this->getTranslation('ServerProtocolInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', 'php_uname()', 'http://be2.php.net/php_uname', php_uname(), null,
            null, $this->getTranslation('UnameInfo')
        );

        return $array;
    }
}
