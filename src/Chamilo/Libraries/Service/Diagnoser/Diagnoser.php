<?php
namespace Chamilo\Libraries\Service\Diagnoser;

use Chamilo\Libraries\Architecture\Domain\ChamiloRequest;
use Chamilo\Libraries\Architecture\Enum\DisplayTypeEnum;
use Chamilo\Libraries\Architecture\Enum\StatusEnum;
use Chamilo\Libraries\Filesystem\Service\ConfigurablePathBuilder;
use Chamilo\Libraries\Filesystem\Service\SystemPathBuilder;
use Chamilo\Libraries\Service\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\ContentTab;
use Chamilo\Libraries\UserInterface\Tab\Architecture\Domain\TabsCollection;
use Chamilo\Libraries\UserInterface\Tab\Service\TabsRenderer;
use Chamilo\Libraries\UserInterface\Table\Service\SimpleTableRenderer;
use Doctrine\DBAL\Connection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Service\Diagnoser
 * @author  spou595 Class that is responsible for generating diagnostic information about the system
 */
class Diagnoser
{
    protected ConfigurablePathBuilder $configurablePathBuilder;

    protected Connection $connection;

    protected DatetimeUtilities $datetimeUtilities;

    protected SimpleTableRenderer $diagnoserTableRenderer;

    protected int $installationDate;

    protected ChamiloRequest $request;

    protected SystemPathBuilder $systemPathBuilder;

    protected TabsRenderer $tabsRenderer;

    protected Translator $translator;

    public function __construct(
        Connection $connection, ChamiloRequest $request, SystemPathBuilder $systemPathBuilder,
        ConfigurablePathBuilder $configurablePathBuilder, Translator $translator, DatetimeUtilities $datetimeUtilities,
        TabsRenderer $tabsRenderer, SimpleTableRenderer $diagnoserTableRenderer, int $installationDate
    )
    {
        $this->connection = $connection;
        $this->request = $request;
        $this->systemPathBuilder = $systemPathBuilder;
        $this->configurablePathBuilder = $configurablePathBuilder;
        $this->translator = $translator;
        $this->datetimeUtilities = $datetimeUtilities;
        $this->tabsRenderer = $tabsRenderer;
        $this->diagnoserTableRenderer = $diagnoserTableRenderer;
        $this->installationDate = $installationDate;
    }

    /**
     * @throws \TableException
     */
    public function render(): string
    {
        $sections = ['Chamilo', 'Php', 'Database', 'Webserver'];

        $tabs = new TabsCollection();

        foreach ($sections as $section) {
            $data = call_user_func([$this, 'get' . $section . 'Data']);
            $table = $this->getDiagnoserTableRenderer()->render($data);

            $tabs->add(
                new ContentTab(
                    $section, $this->getTranslation(ucfirst($section) . 'Title'), $table, null, DisplayTypeEnum::LABEL
                )
            );
        }

        return $this->getTabsRenderer()->renderNavigationAndContent('diagnoser', $tabs);
    }

    public function buildSetting(
        StatusEnum $status, string $section, string $title, string $url, mixed $currentValue, mixed $expectedValue,
        mixed $formatter, string $comment
    ): array
    {
        $image = $status->getGlyph()->render();

        if ($url) {
            $url = $this->getLink($title, $url);
        }
        else {
            $url = $title;
        }

        $formattedCurrentValue = $currentValue;
        $formattedExpectedValue = $expectedValue;

        if ($formatter) {
            if (method_exists($this, 'format_' . $formatter)) {
                $formattedCurrentValue = call_user_func([$this, 'format_' . $formatter], $currentValue);
                $formattedExpectedValue = call_user_func([$this, 'format_' . $formatter], $expectedValue);
            }
        }

        return [$image, $section, $url, $formattedCurrentValue, $formattedExpectedValue, $comment];
    }

    public function formatOnOff(string $value): string
    {
        return $this->getTranslation($value ? 'ConfirmOn' : 'ConfirmOff');
    }

    public function formatYesNo(string $value): string
    {
        return $this->getTranslation($value ? 'ConfirmYes' : 'ConfirmNo');
    }

    /**
     * @return string[]
     * @throws \Exception
     */
    public function getChamiloData(): array
    {
        $array = [];

        $writableFolders = [];
        $writableFolders[] = $this->systemPathBuilder->getPublicStoragePath();
        $writableFolders[] = $this->configurablePathBuilder->getTemporaryPath();

        foreach ($writableFolders as $folder) {
            $writable = is_writable($folder);
            $status = $writable ? StatusEnum::OK : StatusEnum::ERROR;
            $array[] = $this->buildSetting(
                $status, '[FILES]', $this->getTranslation('IsWritable') . ': ' . $folder,
                'https://www.php.net/manual/en/function.is-writable.php', $writable, 1, 'yes_no',
                $this->getTranslation('DirectoryMustBeWritable')
            );
        }

        $date = $this->getInstallationDate();
        $date = $this->getDatetimeUtilities()->formatLocaleDate($date);
        $array[] = $this->buildSetting(
            StatusEnum::OK, '[INFORMATION]', $this->getTranslation('InstallDate'), '', $date, '', null,
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

        $array[] = $this->buildSetting(
            StatusEnum::INFORMATION, '[Database]', 'databaseName', '', $databaseName, null, null,
            $this->getTranslation('DatabaseName')
        );

        $array[] = $this->buildSetting(
            StatusEnum::INFORMATION, '[Database]', 'driverClass', '', $driverClass, null, null,
            $this->getTranslation('DriverClass')
        );

        $array[] = $this->buildSetting(
            StatusEnum::INFORMATION, '[Database]', 'databasePlatformClass', '', $databasePlatformClass, null, null,
            $this->getTranslation('DatabasePlatformClass')
        );

        return $array;
    }

    public function getDatetimeUtilities(): DatetimeUtilities
    {
        return $this->datetimeUtilities;
    }

    public function getDiagnoserTableRenderer(): SimpleTableRenderer
    {
        return $this->diagnoserTableRenderer;
    }

    public function getInstallationDate(): int
    {
        return $this->installationDate;
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
        $status = $version > '5.2' ? StatusEnum::OK : StatusEnum::ERROR;
        $array[] = $this->buildSetting(
            $status, '[PHP]', 'phpversion()', 'https://www.php.net/manual/en/function.phpversion.php', phpversion(),
            '>= 5.2', null, $this->getTranslation('PHPVersionInfo')
        );

        $setting = ini_get('output_buffering');
        $reqSetting = 0;
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::ERROR;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'output_buffering',
            'https://www.php.net/manual/en/outcontrol.configuration.php#ini.output-buffering', $setting, $reqSetting,
            'on_off', $this->getTranslation('OutputBufferingInfo')
        );

        $setting = ini_get('file_uploads');
        $reqSetting = 1;
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::ERROR;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'file_uploads', 'https://www.php.net/manual/en/ini.core.php#ini.file-uploads', $setting,
            $reqSetting, 'on_off', $this->getTranslation('FileUploadsInfo')
        );

        $reqSetting = 0;

        $setting = ini_get('magic_quotes_runtime');
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::ERROR;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'magic_quotes_runtime',
            'https://www.php.net/manual/en/ini.core.php#ini.magic-quotes-runtime', $setting, $reqSetting, 'on_off',
            $this->getTranslation('MagicQuotesRuntimeInfo')
        );

        $setting = ini_get('safe_mode');
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::WARNING;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'safe_mode', 'https://www.php.net/manual/en/ini.core.php#ini.safe-mode', $setting,
            $reqSetting, 'on_off', $this->getTranslation('SafeModeInfo')
        );

        $setting = ini_get('register_globals');
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::ERROR;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'register_globals', 'https://www.php.net/manual/en/ini.core.php#ini.register-globals',
            $setting, $reqSetting, 'on_off', $this->getTranslation('RegisterGlobalsInfo')
        );

        $setting = ini_get('short_open_tag');
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::WARNING;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'short_open_tag', 'https://www.php.net/manual/en/ini.core.php#ini.short-open-tag',
            $setting, $reqSetting, 'on_off', $this->getTranslation('ShortOpenTagInfo')
        );

        $setting = ini_get('magic_quotes_gpc');
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::ERROR;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'magic_quotes_gpc', 'https://www.php.net/manual/en/ini.core.php#ini.magic_quotes_gpc',
            $setting, $reqSetting, 'on_off', $this->getTranslation('MagicQuotesGpcInfo')
        );

        $setting = ini_get('display_errors');
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::WARNING;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'display_errors', 'https://www.php.net/manual/en/ini.core.php#ini.display_errors',
            $setting, $reqSetting, 'on_off', $this->getTranslation('DisplayErrorsInfo')
        );

        $setting = ini_get('upload_max_filesize');
        $reqSetting = '10M - 100M - ...';
        if ($setting < 10) {
            $status = StatusEnum::ERROR;
        }
        if ($setting >= 10 && $setting < 100) {
            $status = StatusEnum::WARNING;
        }
        if ($setting >= 100) {
            $status = StatusEnum::OK;
        }
        $array[] = $this->buildSetting(
            $status, '[INI]', 'upload_max_filesize',
            'https://www.php.net/manual/en/ini.core.php#ini.upload_max_filesize', $setting, $reqSetting, null,
            $this->getTranslation('UploadMaxFilesizeInfo')
        );

        $setting = ini_get('default_charset');
        if ($setting == '') {
            $setting = null;
        }
        $reqSetting = 'UTF-8';
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::ERROR;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'default_charset', 'https://www.php.net/manual/en/ini.core.php#ini.default-charset',
            $setting, $reqSetting, null, $this->getTranslation('DefaultCharsetInfo')
        );

        $setting = ini_get('max_execution_time');
        $reqSetting = '300 (' . $this->getTranslation('Minimum') . ')';
        $status = $setting >= 300 ? StatusEnum::OK : StatusEnum::WARNING;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'max_execution_time', 'https://www.php.net/manual/en/ini.core.php#ini.max-execution-time',
            $setting, $reqSetting, null, $this->getTranslation('MaxExecutionTimeInfo')
        );

        $setting = ini_get('max_input_time');
        $reqSetting = '300 (' . $this->getTranslation('Minimum') . ')';
        $status = $setting >= 300 ? StatusEnum::OK : StatusEnum::WARNING;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'max_input_time', 'https://www.php.net/manual/en/ini.core.php#ini.max-input-time',
            $setting, $reqSetting, null, $this->getTranslation('MaxInputTimeInfo')
        );

        $reqSetting = '10M - 100M - ...';

        $setting = ini_get('memory_limit');
        if ($setting < 10) {
            $status = StatusEnum::ERROR;
        }
        if ($setting >= 10 && $setting < 100) {
            $status = StatusEnum::WARNING;
        }
        if ($setting >= 100) {
            $status = StatusEnum::OK;
        }
        $array[] = $this->buildSetting(
            $status, '[INI]', 'memory_limit', 'https://www.php.net/manual/en/ini.core.php#ini.memory-limit', $setting,
            $reqSetting, null, $this->getTranslation('MemoryLimitInfo')
        );

        $setting = ini_get('post_max_size');
        if ($setting < 10) {
            $status = StatusEnum::ERROR;
        }
        if ($setting >= 10 && $setting < 100) {
            $status = StatusEnum::WARNING;
        }
        if ($setting >= 100) {
            $status = StatusEnum::OK;
        }
        $array[] = $this->buildSetting(
            $status, '[INI]', 'post_max_size', 'https://www.php.net/manual/en/ini.core.php#ini.post-max-size', $setting,
            $reqSetting, null, $this->getTranslation('PostMaxSizeInfo')
        );

        $setting = ini_get('variables_order');
        $reqSetting = 'GPCS';
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::ERROR;
        $array[] = $this->buildSetting(
            $status, '[INI]', 'variables_order', 'https://www.php.net/manual/en/ini.core.php#ini.variables-order',
            $setting, $reqSetting, null, $this->getTranslation('VariablesOrderInfo')
        );

        $setting = ini_get('session.gc_maxlifetime');
        $reqSetting = '4320';
        $status = $setting == $reqSetting ? StatusEnum::OK : StatusEnum::WARNING;
        $array[] = $this->buildSetting(
            $status, '[SESSION]', 'session.gc_maxlifetime',
            'https://www.php.net/manual/en/ini.core.php#session.gc-maxlifetime', $setting, $reqSetting, null,
            $this->getTranslation('SessionGCMaxLifetimeInfo')
        );

        // Extensions
        $extensions = [
            'gd' => 'https://www.php.net/gd',
            'mysqli' => 'https://www.php.net/mysqli',
            'pcre' => 'https://www.php.net/pcre',
            'session' => 'https://www.php.net/session',
            'standard' => 'https://www.php.net/spl',
            'zlib' => 'https://www.php.net/zlib',
            'xsl' => 'https://www.php.net/xsl'
        ];

        foreach ($extensions as $extension => $url) {
            $loaded = extension_loaded($extension);
            $status = $loaded ? StatusEnum::OK : StatusEnum::ERROR;
            $array[] = $this->buildSetting(
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

        $array[] = $this->buildSetting(
            StatusEnum::INFORMATION, '[SERVER]', '$_SERVER["SERVER_ADDR"]',
            'https://www.php.net/reserved.variables.server', $_SERVER['SERVER_ADDR'], null, null,
            $this->getTranslation('ServerIPInfo')
        );

        $array[] = $this->buildSetting(
            StatusEnum::INFORMATION, '[SERVER]', '$_SERVER["SERVER_SOFTWARE"]',
            'https://www.php.net/reserved.variables.server', $_SERVER['SERVER_SOFTWARE'], null, null,
            $this->getTranslation('ServerSoftwareInfo')
        );

        $array[] = $this->buildSetting(
            StatusEnum::INFORMATION, '[SERVER]', '$_SERVER["REMOTE_ADDR"]',
            'https://www.php.net/reserved.variables.server', $_SERVER['REMOTE_ADDR'], null, null,
            $this->getTranslation('ServerRemoteInfo')
        );

        $array[] = $this->buildSetting(
            StatusEnum::INFORMATION, '[SERVER]', '$_SERVER["HTTP_USER_AGENT"]',
            'https://www.php.net/reserved.variables.server', $_SERVER['HTTP_USER_AGENT'], null, null,
            $this->getTranslation('ServerRemoteInfo')
        );

        $path = $this->request->getUri();
        $request = $_SERVER['REQUEST_URI'];
        $status = $request != $path ? StatusEnum::ERROR : StatusEnum::OK;
        $array[] = $this->buildSetting(
            $status, '[SERVER]', '$_SERVER["REQUEST_URI"]', 'https://www.php.net/reserved.variables.server', $request,
            $path, null, $this->getTranslation('RequestURIInfo')
        );

        $array[] = $this->buildSetting(
            StatusEnum::INFORMATION, '[SERVER]', '$_SERVER["SERVER_PROTOCOL"]',
            'https://www.php.net/reserved.variables.server', $_SERVER['SERVER_PROTOCOL'], null, null,
            $this->getTranslation('ServerProtocolInfo')
        );

        $array[] = $this->buildSetting(
            StatusEnum::INFORMATION, '[SERVER]', 'php_uname()', 'https://www.php.net/php_uname', php_uname(), null,
            null, $this->getTranslation('UnameInfo')
        );

        return $array;
    }
}
