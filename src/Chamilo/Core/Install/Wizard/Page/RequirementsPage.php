<?php
namespace Chamilo\Core\Install\Wizard\Page;

use Chamilo\Core\Install\Wizard\InstallWizardPage;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Table\SimpleTable;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Support\Diagnoser;
use Chamilo\Libraries\Support\DiagnoserCellRenderer;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: requirements_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * Class for requirements page This checks and informs about some requirements for installing Chamilo: - necessary and
 * optional extensions - folders which have to be writable
 */
class RequirementsPage extends InstallWizardPage
{

    private $fatal = false;

    public function get_data()
    {
        $array = array();
        $diagnoser = new Diagnoser();
        $files_folder = Path :: getInstance()->getStoragePath();

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

        $status = $exists && $writable ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;

        $array[] = $diagnoser->build_setting(
            $status,
            '[FILES]',
            Translation :: get($exists ? 'IsWritable' : 'DirectoryExists', null, Utilities :: COMMON_LIBRARIES) . ': ' .
                 $files_folder,
                $exists ? 'http://php.net/manual/en/function.is-writable.php' : 'http://php.net/manual/en/function.file-exists.php',
                $writable,
                1,
                'yes_no',
                Translation :: get(
                    $exists ? 'DirectoryMustBeWritable' : 'DirectoryMustExist',
                    null,
                    Utilities :: COMMON_LIBRARIES));

        $version = phpversion();
        $status = $version > '5.3' ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;

        if ($status == Diagnoser :: STATUS_ERROR)
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
            Translation :: get('PHPVersionInfo', null, Utilities :: COMMON_LIBRARIES));

        $setting = ini_get('magic_quotes_gpc');
        $req_setting = 0;
        $status = $setting == $req_setting ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;

        if ($status == Diagnoser :: STATUS_ERROR)
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
            Translation :: get('MagicQuotesGpcInfo', null, Utilities :: COMMON_LIBRARIES));

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

            $status = $loaded ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;

            $array[] = $diagnoser->build_setting(
                $status,
                '[PHP-EXTENSION]',
                Translation :: get('ExtensionLoaded', null, Utilities :: COMMON_LIBRARIES) . ': ' . $extension,
                $url,
                $loaded,
                1,
                'yes_no',
                Translation :: get('ExtensionMustBeLoaded', null, Utilities :: COMMON_LIBRARIES));
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

        $status = $loaded ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;

        $array[] = $diagnoser->build_setting(
            $status,
            '[PHP-EXTENSION]',
            Translation :: get('ExtensionLoaded', null, Utilities :: COMMON_LIBRARIES) . ': ' . 'ZipArchive',
            'http://php.net/manual/en/class.ziparchive.php',
            $loaded,
            1,
            'yes_no',
            Translation :: get('ExtensionMustBeLoaded', null, Utilities :: COMMON_LIBRARIES));

        // FreeType
        // Fatal error: Call to undefined function imageftbbox() in
        // /var/www/html/chamilo3/configuration/plugin/pChart/pChart/pChart.class on line 2428
        // --with-freetype-dir=/usr/lib64
        $loaded = function_exists('imageftbbox');

        if (! $loaded)
        {
            $this->fatal = true;
        }

        $status = $loaded ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;

        $array[] = $diagnoser->build_setting(
            $status,
            '[PHP-EXTENSION]',
            Translation :: get('ExtensionLoaded', null, Utilities :: COMMON_LIBRARIES) . ': ' . 'FreeType',
            null,
            $loaded,
            1,
            'yes_no',
            Translation :: get('ExtensionMustBeLoaded', null, Utilities :: COMMON_LIBRARIES));

        return $array;
    }

    public function buildForm()
    {
        $this->set_lang($this->controller->exportValue('page_language', 'install_language'));
        $this->_formBuilt = true;

        $buttons = array();
        $buttons[] = $this->createElement(
            'style_submit_button',
            $this->getButtonName('back'),
            Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal previous'));
        $buttons[] = $this->createElement(
            'style_submit_button',
            $this->getButtonName('refresh'),
            Translation :: get('Refresh'),
            array('class' => 'normal refresh', 'id' => 'refresh_button'));
        $buttons[] = $this->createElement(
            'style_submit_button',
            $this->getButtonName('next'),
            Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal next'));

        $table = new SimpleTable($this->get_data(), new DiagnoserCellRenderer(), null, 'diagnoser');
        $this->addElement('html', $table->toHTML());

        $script = array();
        $script[] = '<script type="text/javascript">';
        $script[] = '//Tim brouckaert 2010 03 11: added for refresh button';
        $script[] = '$(document).ready(function ()';
        $script[] = '{';
        $script[] = '	$(\'#refresh_button\').click(function(){';
        $script[] = '		location.reload();';
        $script[] = '		return false;';
        $script[] = '	});';
        $script[] = '});';
        $script[] = '</script>';

        $this->addElement('html', implode(PHP_EOL, $script));
        $this->get_data();

        if ($this->fatal)
        {
            $el = $buttons[2];
            $el->updateAttributes('disabled="disabled"');
        }

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
        $this->set_form_defaults();
    }

    public function set_form_defaults()
    {
        $defaults = array();
        $defaults['installation_type'] = 'new';
        $this->setDefaults($defaults);
    }
}
