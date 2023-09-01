<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Manager;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\ActionBar\AbstractButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\SimpleTableRenderer;
use Chamilo\Libraries\Support\Diagnoser;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Install\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class RequirementsComponent extends Manager implements NoAuthenticationSupport
{
    private bool $fatal = false;

    /**
     * Runs this component and displays its output.
     *
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \QuickformException
     * @throws \Exception
     */
    public function run()
    {
        $this->checkInstallationAllowed();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getDiagnoserTableRenderer()->render($this->get_data());
        $html[] = $this->getButtons();
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \QuickformException
     */
    public function getButtons(): string
    {
        $translator = $this->getTranslator();
        $buttonToolBar = new ButtonToolBar();

        $buttonToolBar->addItem(
            new Button(
                $translator->trans('Previous', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('chevron-left'),
                $this->get_url([self::PARAM_ACTION => self::ACTION_INTRODUCTION])
            )
        );

        $buttonToolBar->addItem(
            new Button(
                $translator->trans('Refresh', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sync'),
                $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_REQUIREMENTS,
                        self::PARAM_LANGUAGE => $this->getSession()->get(self::PARAM_LANGUAGE)
                    ]
                )
            )
        );

        if (!$this->fatal)
        {
            $buttonToolBar->addItem(
                new Button(
                    $translator->trans('Next', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('chevron-right'),
                    $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_LICENSE,
                            self::PARAM_LANGUAGE => $this->getSession()->get(self::PARAM_LANGUAGE)
                        ]
                    ), AbstractButton::DISPLAY_ICON_AND_LABEL, null, ['btn-primary']
                )
            );
        }

        $buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        return $buttonToolbarRenderer->render();
    }

    protected function getDiagnoser(): Diagnoser
    {
        return $this->getService(Diagnoser::class);
    }

    protected function getDiagnoserTableRenderer(): SimpleTableRenderer
    {
        return $this->getService('Chamilo\Libraries\Support\DiagnoserTableRenderer');
    }

    protected function getInfo(): string
    {
        return $this->getTranslator()->trans('RequirementsComponentInformation', [], self::CONTEXT);
    }

    /**
     * @return string[]
     */
    public function get_data(): array
    {
        $diagnoser = $this->getDiagnoser();
        $translator = $this->getTranslator();

        $filesFolder = $this->getSystemPathBuilder()->getStoragePath();

        $array = [];

        if (!file_exists($filesFolder))
        {
            mkdir($filesFolder);
        }

        $exists = file_exists($filesFolder);
        $writable = is_writable($filesFolder);

        if (!$exists || !$writable)
        {
            $this->fatal = true;
        }

        $status = $exists && $writable ? Diagnoser::STATUS_OK : Diagnoser::STATUS_ERROR;

        $array[] = $diagnoser->build_setting(
            $status, '[FILES]',
            $translator->trans($exists ? 'IsWritable' : 'DirectoryExists', [], StringUtilities::LIBRARIES) . ': ' .
            $filesFolder, $exists ? 'http://php.net/manual/en/function.is-writable.php' :
            'http://php.net/manual/en/function.file-exists.php', $writable, 1, 'yes_no', $translator->trans(
            $exists ? 'DirectoryMustBeWritable' : 'DirectoryMustExist', [], StringUtilities::LIBRARIES
        )
        );

        $version = phpversion();
        $status = $version > '5.3' ? Diagnoser::STATUS_OK : Diagnoser::STATUS_ERROR;

        if ($status == Diagnoser::STATUS_ERROR)
        {
            $this->fatal = true;
        }

        $array[] = $diagnoser->build_setting(
            $status, '[PHP]', 'phpversion()', 'http://www.php.net/manual/en/function.phpversion.php', phpversion(),
            '>= 5.4', null, $translator->trans('PHPVersionInfo', [], StringUtilities::LIBRARIES)
        );

        $setting = ini_get('magic_quotes_gpc');
        $req_setting = 0;
        $status = $setting == $req_setting ? Diagnoser::STATUS_OK : Diagnoser::STATUS_ERROR;

        if ($status == Diagnoser::STATUS_ERROR)
        {
            $this->fatal = true;
        }

        $array[] = $diagnoser->build_setting(
            $status, '[PHP-INI]', 'magic_quotes_gpc',
            'http://www.php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc', $setting, $req_setting,
            'on_off', $translator->trans('MagicQuotesGpcInfo', [], StringUtilities::LIBRARIES)
        );

        $extensions = [
            'dom' => 'http://www.php.net/dom',
            'json' => 'http://www.php.net/json',
            'fileinfo' => 'http://www.php.net/fileinfo',
            'curl' => 'http://www.php.net/curl',
            'gd' => 'http://www.php.net/gd',
            'pdo' => 'http://www.php.net/pdo',
            'exif' => 'http://www.php.net/exif',
            'zip' => 'http://www.php.net/zip',
            'intl' => 'http://www.php.net/intl',
            'mbstring' => 'http://www.php.net/mbstring',
            'openssl' => 'http://www.php.net/openssl'
        ];

        foreach ($extensions as $extension => $url)
        {
            $loaded = extension_loaded($extension);

            if (!$loaded)
            {
                $this->fatal = true;
            }

            $status = $loaded ? Diagnoser::STATUS_OK : Diagnoser::STATUS_ERROR;

            $array[] = $diagnoser->build_setting(
                $status, '[PHP-EXTENSION]',
                $translator->trans('ExtensionLoaded', [], StringUtilities::LIBRARIES) . ': ' . $extension, $url,
                $loaded, 1, 'yes_no', $translator->trans('ExtensionMustBeLoaded', [], StringUtilities::LIBRARIES)
            );
        }

        // ZipArchive
        // Fatal error: Class 'ZipArchive' not found in
        // /var/www/html/chamilo3/configuration/plugin/phpexcel/PHPExcel/Writer/Excel2007.php on line 229
        // --enable-zip
        $loaded = class_exists('ZipArchive');

        if (!$loaded)
        {
            $this->fatal = true;
        }

        $status = $loaded ? Diagnoser::STATUS_OK : Diagnoser::STATUS_ERROR;

        $array[] = $diagnoser->build_setting(
            $status, '[PHP-EXTENSION]',
            $translator->trans('ExtensionLoaded', [], StringUtilities::LIBRARIES) . ': ' . 'ZipArchive',
            'http://php.net/manual/en/class.ziparchive.php', $loaded, 1, 'yes_no',
            $translator->trans('ExtensionMustBeLoaded', [], StringUtilities::LIBRARIES)
        );

        return $array;
    }
}
