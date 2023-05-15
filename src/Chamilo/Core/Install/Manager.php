<?php
namespace Chamilo\Core\Install;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Core\Install\Format\Structure\FooterRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\FooterRendererInterface;
use Chamilo\Libraries\Format\Structure\HeaderRendererInterface;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeader;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeaderRenderer;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use DOMDocument;
use DOMXPath;
use Exception;

/**
 * @package Chamilo\Core\Install
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
abstract class Manager extends Application implements NoContextComponent
{
    public const ACTION_INSTALL_PLATFORM = 'installer';
    public const ACTION_INTRODUCTION = 'introduction';
    public const ACTION_LICENSE = 'license';
    public const ACTION_OVERVIEW = 'overview';
    public const ACTION_REQUIREMENTS = 'requirements';
    public const ACTION_SETTINGS = 'settings';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_INTRODUCTION;

    public const PARAM_LANGUAGE = 'install_language';
    public const PARAM_SETTINGS = 'install_settings';

    private WizardHeader $wizardHeader;

    /**
     * @param ApplicationConfigurationInterface $applicationConfiguration
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);
        $this->initialize();
    }

    /**
     * Checks if the installation is allowed
     *
     * @return bool
     * @throws \ReflectionException
     */
    protected function checkInstallationAllowed()
    {
        if (!$this->getFileConfigurationLocator()->isAvailable())
        {
            return true;
        }
        else
        {
            $installationBlocked = (bool) Configuration::getInstance()->get_setting(
                ['Chamilo\Core\Admin', 'installation_blocked']
            );

            if ($installationBlocked)
            {
                throw new Exception(
                    Translation::getInstance()->getTranslation(
                        'InstallationBlockedByAdministrator', null, Manager::CONTEXT
                    )
                );
            }
        }
    }

    /**
     * @return \Chamilo\Configuration\Service\FileConfigurationLocator
     */
    public function getFileConfigurationLocator()
    {
        return $this->getService(FileConfigurationLocator::class);
    }

    public function getFooterRenderer(): FooterRendererInterface
    {
        return $this->getService(FooterRenderer::class);
    }

    public function getHeaderRenderer(): HeaderRendererInterface
    {
        return $this->getService('Chamilo\Core\Install\Format\Structure\HeaderRenderer');
    }

    /**
     * @return string
     */
    protected function getInfo()
    {
        return Translation::get(ClassnameUtilities::getInstance()->getClassnameFromObject($this) . 'Information');
    }

    public function getLanguages()
    {
        $language_path = Path::getInstance()->namespaceToFullPath('Chamilo\Configuration') . 'Resources/I18n/';
        $language_files = Filesystem::get_directory_content($language_path, Filesystem::LIST_FILES, false);

        $language_list = [];
        foreach ($language_files as $language_file)
        {
            $file_info = pathinfo($language_file);
            $language_info_file = $language_path . $file_info['filename'] . '.info';

            if (file_exists($language_info_file))
            {
                $dom_document = new DOMDocument('1.0', 'UTF-8');
                $dom_document->load($language_info_file);
                $dom_xpath = new DOMXPath($dom_document);

                $language_node = $dom_xpath->query('/packages/package')->item(0);

                $language_list[$dom_xpath->query('extra/isocode', $language_node)->item(0)->nodeValue] =
                    $dom_xpath->query(
                        'name', $language_node
                    )->item(0)->nodeValue;
            }
        }

        return $language_list;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeader
     */
    protected function getWizardHeader()
    {
        if (!isset($this->wizardHeader))
        {
            $currentAction = $this->get_action();
            $wizardActions = $this->getWizardHeaderActions();

            $this->wizardHeader = new WizardHeader();
            $this->wizardHeader->setStepTitles(
                [
                    Translation::get('IntroductionComponentTitle'),
                    Translation::get('RequirementsComponentTitle'),
                    Translation::get('LicenseComponentTitle'),
                    Translation::get('SettingsComponentTitle'),
                    Translation::get('OverviewComponentTitle'),
                    Translation::get('InstallerComponentTitle')
                ]
            );

            $this->wizardHeader->setSelectedStepIndex(array_search($currentAction, $wizardActions));
        }

        return $this->wizardHeader;
    }

    /**
     * @return string[]
     */
    protected function getWizardHeaderActions()
    {
        return [
            self::ACTION_INTRODUCTION,
            self::ACTION_REQUIREMENTS,
            self::ACTION_LICENSE,
            self::ACTION_SETTINGS,
            self::ACTION_OVERVIEW,
            self::ACTION_INSTALL_PLATFORM
        ];
    }

    protected function initialize()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '7200');

        $this->setLanguage();
    }

    /**
     * @return string
     */
    protected function renderWizardHeader()
    {
        $wizardHeaderRenderer = new WizardHeaderRenderer($this->getWizardHeader());

        $html = [];

        $html[] = '<div class="container-install-wizard">';
        $html[] = $wizardHeaderRenderer->render();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_footer()
     */
    public function render_footer(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = $this->getFooterRenderer()->render();

        return implode(PHP_EOL, $html);
    }

    /**
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_header()
     */
    public function render_header(string $pageTitle = ''): string
    {
        $page = $this->getPageConfiguration();

        $page->setApplication($this);
        $page->setContainerMode('container');
        $page->setLanguageCode($this->getTranslator()->getLocale());
        $page->setTitle($this->getTranslator()->trans('ChamiloInstallationTitle', [], Manager::CONTEXT));

        $html = [];

        $html[] = $this->getHeaderRenderer()->render();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = $this->renderWizardHeader();

        $html[] = '<div class="alert alert-info">';
        $html[] = $this->getInfo();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
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
}
