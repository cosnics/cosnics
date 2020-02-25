<?php
namespace Chamilo\Libraries\Support;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\SimpleTable;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\Doctrine\Connection;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Support
 * @author spou595 Class that is responsible for generating diagnostic information about the system
 */
class Diagnoser
{

    const STATUS_ERROR = 3;

    const STATUS_INFORMATION = 4;

    /**
     * The status's
     */
    const STATUS_OK = 1;

    const STATUS_WARNING = 2;

    /**
     * The manager where this diagnoser runs on
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $manager;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $manager
     */
    public function __construct($manager = null)
    {
        $this->manager = $manager;
    }

    /**
     * @param $status
     * @param $section
     * @param $title
     * @param $url
     * @param $current_value
     * @param $expected_value
     * @param $formatter
     * @param $comment
     * @param null $img_path
     *
     * @return array
     */
    public function build_setting(
        $status, $section, $title, $url, $current_value, $expected_value, $formatter, $comment, $img_path = null
    )
    {
        switch ($status)
        {
            case self::STATUS_OK :
                $glyph = new FontAwesomeGlyph(
                    'check-circle', array('text-success'), $status, 'fas'
                );
                break;
            case self::STATUS_WARNING :
                $glyph = new FontAwesomeGlyph(
                    'exclamation-circle', array('text-warning'), $status, 'fas'
                );
                break;
            case self::STATUS_ERROR :
                $glyph = new FontAwesomeGlyph(
                    'minus-circle', array('text-danger'), $status, 'fas'
                );
                break;
            case self::STATUS_INFORMATION :
            default:
                $glyph = new FontAwesomeGlyph(
                    'lightbulb', array('text-info'), $status, 'fas'
                );
                break;
        }

        $image = $glyph->render();

        if ($url)
        {
            $url = $this->get_link($title, $url);
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
                $formatted_current_value = call_user_func(array($this, 'format_' . $formatter), $current_value);
                $formatted_expected_value = call_user_func(array($this, 'format_' . $formatter), $expected_value);
            }
        }

        return array($image, $section, $url, $formatted_current_value, $formatted_expected_value, $comment);
    }

    /**
     *
     * @param string $value
     *
     * @return string
     */
    public function format_on_off($value)
    {
        return $value ? Translation::get('ConfirmOn', null, Utilities::COMMON_LIBRARIES) : Translation::get(
            'ConfirmOff', null, Utilities::COMMON_LIBRARIES
        );
    }

    /**
     *
     * @param string $value
     *
     * @return string
     */
    public function format_yes_no($value)
    {
        return $value ? Translation::get('ConfirmYes', null, Utilities::COMMON_LIBRARIES) : Translation::get(
            'ConfirmNo', null, Utilities::COMMON_LIBRARIES
        );
    }

    /**
     * Functions to get the data for the chamilo diagnostics
     *
     * @return string[]
     */
    public function get_chamilo_data()
    {
        $array = array();

        $pathRenderer = Path::getInstance();

        $writable_folders = array();
        $writable_folders[] = $pathRenderer->getStoragePath();
        $writable_folders[] = $pathRenderer->getRepositoryPath();
        $writable_folders[] = $pathRenderer->getTemporaryPath();

        foreach ($writable_folders as $index => $folder)
        {
            $writable = is_writable($folder);
            $status = $writable ? self::STATUS_OK : self::STATUS_ERROR;
            $array[] = $this->build_setting(
                $status, '[FILES]', Translation::get('IsWritable') . ': ' . $folder,
                'http://be2.php.net/manual/en/function.is-writable.php', $writable, 1, 'yes_no',
                Translation::get('DirectoryMustBeWritable')
            );
        }

        $installationPath = $pathRenderer->namespaceToFullPath('Chamilo\Core\Install');

        $exists = !file_exists($installationPath);
        $status = $exists ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[FILES]', Translation::get('DirectoryExists') . ': ' . $installationPath,
            'http://be2.php.net/file_exists', $writable, 0, 'yes_no', Translation::get('DirectoryShouldBeRemoved')
        );

        $date = Configuration::get('Chamilo\Configuration', 'general', 'install_date');
        $date = DatetimeUtilities::format_locale_date(
            Translation::get('DateFormatShort', null, Utilities::COMMON_LIBRARIES) . ', ' .
            Translation::get('TimeNoSecFormat', null, Utilities::COMMON_LIBRARIES), $date
        );
        $array[] = $this->build_setting(
            1, '[INFORMATION]', Translation::get('InstallDate'), '', $date, '', null,
            Translation::get('InstallDateInfo')
        );

        return $array;
    }

    /**
     * Functions to get the data for the mysql diagnostics
     *
     * @return string[]
     */
    public function get_database_data()
    {
        // Direct use of mysql_* functions without specifying
        // a connection is not reliable here. See Bug #2499.
        // $host_info = mysql_get_host_info();
        // $server_info = mysql_get_server_info();
        // $proto_info = mysql_get_proto_info();
        // $client_info = mysql_get_client_info();
        // due to abstraction of storage we can not rely on the mysql settings anymore
        $connection = Connection::getInstance()->get_connection()->connection;

        $host_info = $connection->host_info;
        $server_info = $connection->server_info;
        $proto_info = $connection->protocol_version;
        $client_info = $connection->client_info;

        $array = array();

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[Database]', 'host_info',
            'http://www.php.net/manual/en/function.mysql-get-host-info.php', $host_info, null, null,
            Translation::get('HostInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[Database]', 'server_info',
            'http://www.php.net/manual/en/function.mysql-get-server-info.php', $server_info, null, null,
            Translation::get('ServerInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[Database]', 'client_info',
            'http://www.php.net/manual/en/function.mysql-get-client-info.php', $client_info, null, null,
            Translation::get('ClientInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[Database]', 'protocol_version',
            'http://www.php.net/manual/en/function.mysql-get-proto-info.php', $proto_info, null, null,
            Translation::get('ProtoInfo')
        );

        return $array;
    }

    /**
     * Create a link with a url and a title
     *
     * @param $title
     * @param $url
     *
     * @return string the url
     */
    public function get_link($title, $url)
    {
        return '<a href="' . $url . '" target="about:bank">' . $title . '</a>';
    }

    /**
     * Functions to get the data for the php diagnostics
     *
     * @return string[]
     */
    public function get_php_data()
    {
        $array = array();

        // General Functions

        $version = phpversion();
        $status = $version > '5.2' ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[PHP]', 'phpversion()', 'http://www.php.net/manual/en/function.phpversion.php', phpversion(),
            '>= 5.2', null, Translation::get('PHPVersionInfo')
        );

        $setting = ini_get('output_buffering');
        $req_setting = 0;
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'output_buffering',
            'http://www.php.net/manual/en/outcontrol.configuration.php#ini.output-buffering', $setting, $req_setting,
            'on_off', Translation::get('OutputBufferingInfo')
        );

        $setting = ini_get('file_uploads');
        $req_setting = 1;
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'file_uploads', 'http://www.php.net/manual/en/ini.core.php#ini.file-uploads', $setting,
            $req_setting, 'on_off', Translation::get('FileUploadsInfo')
        );

        $setting = ini_get('magic_quotes_runtime');
        $req_setting = 0;
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'magic_quotes_runtime',
            'http://www.php.net/manual/en/ini.core.php#ini.magic-quotes-runtime', $setting, $req_setting, 'on_off',
            Translation::get('MagicQuotesRuntimeInfo')
        );

        $setting = ini_get('safe_mode');
        $req_setting = 0;
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[INI]', 'safe_mode', 'http://www.php.net/manual/en/ini.core.php#ini.safe-mode', $setting,
            $req_setting, 'on_off', Translation::get('SafeModeInfo')
        );

        $setting = ini_get('register_globals');
        $req_setting = 0;
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'register_globals', 'http://www.php.net/manual/en/ini.core.php#ini.register-globals',
            $setting, $req_setting, 'on_off', Translation::get('RegisterGlobalsInfo')
        );

        $setting = ini_get('short_open_tag');
        $req_setting = 0;
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[INI]', 'short_open_tag', 'http://www.php.net/manual/en/ini.core.php#ini.short-open-tag',
            $setting, $req_setting, 'on_off', Translation::get('ShortOpenTagInfo')
        );

        $setting = ini_get('magic_quotes_gpc');
        $req_setting = 0;
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'magic_quotes_gpc', 'http://www.php.net/manual/en/ini.core.php#ini.magic_quotes_gpc',
            $setting, $req_setting, 'on_off', Translation::get('MagicQuotesGpcInfo')
        );

        $setting = ini_get('display_errors');
        $req_setting = 0;
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[INI]', 'display_errors', 'http://www.php.net/manual/en/ini.core.php#ini.display_errors',
            $setting, $req_setting, 'on_off', Translation::get('DisplayErrorsInfo')
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
            Translation::get('UploadMaxFilesizeInfo')
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
            $setting, $req_setting, null, Translation::get('DefaultCharsetInfo')
        );

        $setting = ini_get('max_execution_time');
        $req_setting = '300 (' . Translation::get('Minimum') . ')';
        $status = $setting >= 300 ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[INI]', 'max_execution_time', 'http://www.php.net/manual/en/ini.core.php#ini.max-execution-time',
            $setting, $req_setting, null, Translation::get('MaxExecutionTimeInfo')
        );

        $setting = ini_get('max_input_time');
        $req_setting = '300 (' . Translation::get('Minimum') . ')';
        $status = $setting >= 300 ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[INI]', 'max_input_time', 'http://www.php.net/manual/en/ini.core.php#ini.max-input-time',
            $setting, $req_setting, null, Translation::get('MaxInputTimeInfo')
        );

        $setting = ini_get('memory_limit');
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
            $status, '[INI]', 'memory_limit', 'http://www.php.net/manual/en/ini.core.php#ini.memory-limit', $setting,
            $req_setting, null, Translation::get('MemoryLimitInfo')
        );

        $setting = ini_get('post_max_size');
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
            $status, '[INI]', 'post_max_size', 'http://www.php.net/manual/en/ini.core.php#ini.post-max-size', $setting,
            $req_setting, null, Translation::get('PostMaxSizeInfo')
        );

        $setting = ini_get('variables_order');
        $req_setting = 'GPCS';
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_ERROR;
        $array[] = $this->build_setting(
            $status, '[INI]', 'variables_order', 'http://www.php.net/manual/en/ini.core.php#ini.variables-order',
            $setting, $req_setting, null, Translation::get('VariablesOrderInfo')
        );

        $setting = ini_get('session.gc_maxlifetime');
        $req_setting = '4320';
        $status = $setting == $req_setting ? self::STATUS_OK : self::STATUS_WARNING;
        $array[] = $this->build_setting(
            $status, '[SESSION]', 'session.gc_maxlifetime',
            'http://www.php.net/manual/en/ini.core.php#session.gc-maxlifetime', $setting, $req_setting, null,
            Translation::get('SessionGCMaxLifetimeInfo')
        );

        // Extensions
        $extensions = array(
            'gd' => 'http://www.php.net/gd', 'mysqli' => 'http://www.php.net/mysqli',
            'pcre' => 'http://www.php.net/pcre', 'session' => 'http://www.php.net/session',
            'standard' => 'http://www.php.net/spl', 'zlib' => 'http://www.php.net/zlib',
            'xsl' => 'http://be2.php.net/xsl'
        );

        foreach ($extensions as $extension => $url)
        {
            $loaded = extension_loaded($extension);
            $status = $loaded ? self::STATUS_OK : self::STATUS_ERROR;
            $array[] = $this->build_setting(
                $status, '[EXTENSION]', Translation::get('ExtensionLoaded') . ': ' . $extension, $url, $loaded, 1,
                'yes_no', Translation::get('ExtensionMustBeLoaded')
            );
        }

        return $array;
    }

    /**
     * Functions to get the data for the webserver diagnostics
     *
     * @return string[]
     */
    public function get_webserver_data()
    {
        $array = array();

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', '$_SERVER["SERVER_ADDR"]',
            'http://be.php.net/reserved.variables.server', $_SERVER["SERVER_ADDR"], null, null,
            Translation::get('ServerIPInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', '$_SERVER["SERVER_SOFTWARE"]',
            'http://be.php.net/reserved.variables.server', $_SERVER["SERVER_SOFTWARE"], null, null,
            Translation::get('ServerSoftwareInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', '$_SERVER["REMOTE_ADDR"]',
            'http://be.php.net/reserved.variables.server', $_SERVER["REMOTE_ADDR"], null, null,
            Translation::get('ServerRemoteInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', '$_SERVER["HTTP_USER_AGENT"]',
            'http://be.php.net/reserved.variables.server', $_SERVER["HTTP_USER_AGENT"], null, null,
            Translation::get('ServerRemoteInfo')
        );

        $path = $this->manager->get_url(array('section' => Request::get('section')));
        $request = $_SERVER["REQUEST_URI"];
        $status = $request != $path ? self::STATUS_ERROR : self::STATUS_OK;
        $array[] = $this->build_setting(
            $status, '[SERVER]', '$_SERVER["REQUEST_URI"]', 'http://be.php.net/reserved.variables.server', $request,
            $path, null, Translation::get('RequestURIInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', '$_SERVER["SERVER_PROTOCOL"]',
            'http://be.php.net/reserved.variables.server', $_SERVER["SERVER_PROTOCOL"], null, null,
            Translation::get('ServerProtocolInfo')
        );

        $array[] = $this->build_setting(
            self::STATUS_INFORMATION, '[SERVER]', 'php_uname()', 'http://be2.php.net/php_uname', php_uname(), null,
            null, Translation::get('UnameInfo')
        );

        return $array;
    }

    /**
     *
     * @return string
     */
    public function to_html()
    {
        $sections = array('chamilo', 'php', 'database', 'webserver');

        $current_section = Request::get('section');
        $current_section = $current_section ? $current_section : 'chamilo';
        $html[] = '<br /><div class="tabbed-pane"><ul class="tabbed-pane-tabs">';

        foreach ($sections as $section)
        {
            $html[] = '<li><a';
            if ($current_section == $section)
            {
                $html[] = ' class="current"';
            }
            $params = $this->manager->get_parameters();
            $params['section'] = $section;
            $html[] = ' href="' . $this->manager->get_url($params) . '">' .
                htmlentities(Translation::get(ucfirst($section) . 'Title')) . '</a></li>';
        }

        $html[] = '</ul><div class="tabbed-pane-content">';

        $data = call_user_func(array($this, 'get_' . $current_section . '_data'));

        $table = new SimpleTable($data, new DiagnoserCellRenderer(), null, 'diagnoser');
        $html[] = $table->toHTML();

        $html[] = '</div></div>';

        return implode(PHP_EOL, $html);
    }
}
