<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\BootstrapGlyph;
use Chamilo\Libraries\Format\Table\SimpleTable;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Support\Diagnoser;
use Chamilo\Libraries\Support\DiagnoserCellRenderer;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Platform\Session\Session;

/**
 *
 * @package Chamilo\Core\Install\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RequirementsComponent extends Manager implements NoAuthenticationSupport
{

    /**
     *
     * @var boolean
     */
    private $fatal = false;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkInstallationAllowed();
        
        $table = new SimpleTable($this->get_data(), new DiagnoserCellRenderer(), null, 'diagnoser');
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $table->toHtml();
        $html[] = $this->getButtons();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function getButtons()
    {
        $buttonToolBar = new ButtonToolBar();
        
        $buttonToolBar->addItem(
            new Button(
                Translation::get('Previous', null, Utilities::COMMON_LIBRARIES), 
                new BootstrapGlyph('chevron-left'), 
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_INTRODUCTION))));
        
        $buttonToolBar->addItem(
            new Button(
                Translation::get('Refresh', null, Utilities::COMMON_LIBRARIES), 
                new BootstrapGlyph('refresh'), 
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_REQUIREMENTS, self::PARAM_LANGUAGE => Session::retrieve(self::PARAM_LANGUAGE)))));
        
        $buttonToolBar->addItem(
            new Button(
                Translation::get('Next', null, Utilities::COMMON_LIBRARIES), 
                new BootstrapGlyph('chevron-right'), 
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_LICENSE, self::PARAM_LANGUAGE => Session::retrieve(self::PARAM_LANGUAGE))),
                Button::DISPLAY_ICON_AND_LABEL, 
                false, 
                'btn-primary'));
        
        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        
        return $buttonToolbarRenderer->render();
    }

    /**
     *
     * @return string[]
     */
    public function get_data()
    {
        $array = array();
        $diagnoser = new Diagnoser();
        $files_folder = Path::getInstance()->getStoragePath();
        
        if (! file_exists($files_folder))
        {
            mkdir($files_folder);
        }
        
        $exists = file_exists($files_folder);
        $writable = is_writable($files_folder);
        
        if (! $exists || ! $writable)
        {
            $this->fatal = true;
        }
        
        $status = $exists && $writable ? Diagnoser::STATUS_OK : Diagnoser::STATUS_ERROR;
        
        $array[] = $diagnoser->build_setting(
            $status, 
            '[FILES]', 
            Translation::get($exists ? 'IsWritable' : 'DirectoryExists', null, Utilities::COMMON_LIBRARIES) . ': ' .
                 $files_folder, 
                $exists ? 'http://php.net/manual/en/function.is-writable.php' : 'http://php.net/manual/en/function.file-exists.php', 
                $writable, 
                1, 
                'yes_no', 
                Translation::get(
                    $exists ? 'DirectoryMustBeWritable' : 'DirectoryMustExist', 
                    null, 
                    Utilities::COMMON_LIBRARIES));
        
        $version = phpversion();
        $status = $version > '5.3' ? Diagnoser::STATUS_OK : Diagnoser::STATUS_ERROR;
        
        if ($status == Diagnoser::STATUS_ERROR)
        {
            $this->fatal = true;
        }
        
        $array[] = $diagnoser->build_setting(
            $status, 
            '[PHP]', 
            'phpversion()', 
            'http://www.php.net/manual/en/function.phpversion.php', 
            phpversion(), 
            '>= 5.4', 
            null, 
            Translation::get('PHPVersionInfo', null, Utilities::COMMON_LIBRARIES));
        
        $setting = ini_get('magic_quotes_gpc');
        $req_setting = 0;
        $status = $setting == $req_setting ? Diagnoser::STATUS_OK : Diagnoser::STATUS_ERROR;
        
        if ($status == Diagnoser::STATUS_ERROR)
        {
            $this->fatal = true;
        }
        
        $array[] = $diagnoser->build_setting(
            $status, 
            '[PHP-INI]', 
            'magic_quotes_gpc', 
            'http://www.php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc', 
            $setting, 
            $req_setting, 
            'on_off', 
            Translation::get('MagicQuotesGpcInfo', null, Utilities::COMMON_LIBRARIES));
        
        $extensions = array(
            'gd' => 'http://www.php.net/gd', 
            'pcre' => 'http://www.php.net/pcre', 
            'session' => 'http://www.php.net/session', 
            'standard' => 'http://www.php.net/spl', 
            'zlib' => 'http://www.php.net/zlib', 
            'xsl' => 'http://www.php.net/xsl', 
            'mbstring' => 'http://www.php.net/mbstring', 
            'fileinfo' => 'http://www.php.net/fileinfo', 
            'curl' => 'http://www.php.net/curl', 
            'mcrypt' => 'http://www.php.net/mcrypt',
            /* 'openssl' => 'http://www.php.net/openssl' */
        );
        
        foreach ($extensions as $extension => $url)
        {
            $loaded = extension_loaded($extension);
            
            if (! $loaded)
            {
                $this->fatal = true;
            }
            
            $status = $loaded ? Diagnoser::STATUS_OK : Diagnoser::STATUS_ERROR;
            
            $array[] = $diagnoser->build_setting(
                $status, 
                '[PHP-EXTENSION]', 
                Translation::get('ExtensionLoaded', null, Utilities::COMMON_LIBRARIES) . ': ' . $extension, 
                $url, 
                $loaded, 
                1, 
                'yes_no', 
                Translation::get('ExtensionMustBeLoaded', null, Utilities::COMMON_LIBRARIES));
        }
        
        // ZipArchive
        // Fatal error: Class 'ZipArchive' not found in
        // /var/www/html/chamilo3/configuration/plugin/phpexcel/PHPExcel/Writer/Excel2007.php on line 229
        // --enable-zip
        $loaded = class_exists('ZipArchive');
        
        if (! $loaded)
        {
            $this->fatal = true;
        }
        
        $status = $loaded ? Diagnoser::STATUS_OK : Diagnoser::STATUS_ERROR;
        
        $array[] = $diagnoser->build_setting(
            $status, 
            '[PHP-EXTENSION]', 
            Translation::get('ExtensionLoaded', null, Utilities::COMMON_LIBRARIES) . ': ' . 'ZipArchive', 
            'http://php.net/manual/en/class.ziparchive.php', 
            $loaded, 
            1, 
            'yes_no', 
            Translation::get('ExtensionMustBeLoaded', null, Utilities::COMMON_LIBRARIES));
        
        return $array;
    }
}
