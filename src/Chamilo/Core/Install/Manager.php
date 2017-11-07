<?php
namespace Chamilo\Core\Install;

use Chamilo\Configuration\Configuration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeader;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeaderRenderer;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Core\Install\Format\Structure\Header;
use Chamilo\Core\Install\Format\Structure\Footer;

/**
 *
 * @package Chamilo\Core\Install
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class Manager extends Application implements NoContextComponent
{
    const DEFAULT_ACTION = self::ACTION_INTRODUCTION;

    /**
     * Constant defining an action of the repository manager.
     */
    const ACTION_INSTALL_PLATFORM = 'installer';
    const ACTION_INTRODUCTION = 'introduction';
    const ACTION_REQUIREMENTS = 'requirements';
    const ACTION_LICENSE = 'license';
    const ACTION_SETTINGS = 'settings';
    const ACTION_OVERVIEW = 'overview';

    // Parameters
    const PARAM_LANGUAGE = 'install_language';
    const PARAM_SETTINGS = 'install_settings';

    /**
     *
     * @var \Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeader
     */
    private $wizardHeader;

    /**
     *
     * @param ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->initialize();
    }

    protected function initialize()
    {
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "7200");

        $this->setLanguage();
    }

    protected function setLanguage()
    {
        $language = $this->getRequest()->query->get(self::PARAM_LANGUAGE, 'en');

        if ($language)
        {
            Session::register(self::PARAM_LANGUAGE, $language);
        }

        Translation::getInstance()->setLanguageIsocode(Session::retrieve(self::PARAM_LANGUAGE));
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\Page
     */
    public function getPage()
    {
        if (! isset($this->page))
        {
            $header = new Header(
                Page::VIEW_MODE_FULL,
                'container-fluid',
                Translation::getInstance()->getLanguageIsocode(),
                'ltr');
            $footer = new Footer(Page::VIEW_MODE_FULL);

            $this->page = new Page(Page::VIEW_MODE_FULL, 'container-fluid', $header, $footer);
        }

        return $this->page;
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_header()
     */
    public function render_header()
    {
        $page = $this->getPage();

        $page->setApplication($this);
        $page->setContainerMode('container');
        $page->setTitle(Translation::get('ChamiloInstallationTitle'));

        $html = array();

        $html[] = $page->getHeader()->toHtml();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = $this->renderWizardHeader();

        $html[] = '<div class="alert alert-info">';
        $html[] = $this->getInfo();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function getInfo()
    {
        return Translation::get(ClassnameUtilities::getInstance()->getClassnameFromObject($this) . 'Information');
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_footer()
     */
    public function render_footer()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = $this->getPage()->getFooter()->toHtml();

        return implode(PHP_EOL, $html);
    }

    public function getLanguages()
    {
        $language_path = Path::getInstance()->namespaceToFullPath('Chamilo\Configuration') . 'Resources/I18n/';
        $language_files = Filesystem::get_directory_content($language_path, Filesystem::LIST_FILES, false);

        $language_list = array();
        foreach ($language_files as $language_file)
        {
            $file_info = pathinfo($language_file);
            $language_info_file = $language_path . $file_info['filename'] . '.info';

            if (file_exists($language_info_file))
            {
                $dom_document = new \DOMDocument('1.0', 'UTF-8');
                $dom_document->load($language_info_file);
                $dom_xpath = new \DOMXPath($dom_document);

                $language_node = $dom_xpath->query('/packages/package')->item(0);

                $language_list[$dom_xpath->query('extra/isocode', $language_node)->item(0)->nodeValue] = $dom_xpath->query(
                    'name',
                    $language_node)->item(0)->nodeValue;
            }
        }

        return $language_list;
    }

    /**
     *
     * @return string
     */
    protected function renderWizardHeader()
    {
        $wizardHeaderRenderer = new WizardHeaderRenderer($this->getWizardHeader());

        $html = array();

        $html[] = '<div class="container-install-wizard">';
        $html[] = $wizardHeaderRenderer->render();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeader
     */
    protected function getWizardHeader()
    {
        if (! isset($this->wizardHeader))
        {
            $currentAction = $this->get_action();
            $wizardActions = $this->getWizardHeaderActions();

            $this->wizardHeader = new WizardHeader();
            $this->wizardHeader->setStepTitles(
                array(
                    Translation::get('IntroductionComponentTitle'),
                    Translation::get('RequirementsComponentTitle'),
                    Translation::get('LicenseComponentTitle'),
                    Translation::get('SettingsComponentTitle'),
                    Translation::get('OverviewComponentTitle'),
                    Translation::get('InstallerComponentTitle')));

            $this->wizardHeader->setSelectedStepIndex(array_search($currentAction, $wizardActions));
        }

        return $this->wizardHeader;
    }

    /**
     *
     * @return string[]
     */
    protected function getWizardHeaderActions()
    {
        return array(
            self::ACTION_INTRODUCTION,
            self::ACTION_REQUIREMENTS,
            self::ACTION_LICENSE,
            self::ACTION_SETTINGS,
            self::ACTION_OVERVIEW,
            self::ACTION_INSTALL_PLATFORM);
    }

    /**
     * Checks if the installation is allowed
     */
    protected function checkInstallationAllowed()
    {
        $fileConfigurationLocator = $this->getService('chamilo.configuration.service.file_configuration_locator');

        if (! $fileConfigurationLocator->isAvailable())
        {
            return true;
        }
        else
        {
            $installationBlocked = (bool) Configuration::getInstance()->get_setting(
                array('Chamilo\Core\Admin', 'installation_blocked'));

            if ($installationBlocked)
            {
                throw new \Exception(
                    Translation::getInstance()->getTranslation(
                        'InstallationBlockedByAdministrator',
                        null,
                        self::context()));
            }
        }
    }
}
