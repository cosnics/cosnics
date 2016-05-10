<?php
namespace Chamilo\Core\Install;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeader;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeaderRenderer;
use Chamilo\Libraries\Platform\Session\Session;

/**
 * $Id: install_manager.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager
 */
/**
 * An install manager provides some functionalities to the end user to install his Chamilo platform
 *
 * @author Hans De Bisschop
 */
abstract class Manager extends Application implements NoContextComponent
{
    const DEFAULT_ACTION = self :: ACTION_INTRODUCTION;

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

    /**
     * Property of this repository manager.
     */
    private $breadcrumbs;

    /**
     * Constructor
     *
     * @param $user_id int The user id of current user
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent :: __construct($applicationConfiguration);
        $this->initialize();
    }

    protected function initialize()
    {
        // PHP settings
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "7200");

        // Display language
        $language = $this->getRequest()->query->get(self :: PARAM_LANGUAGE);

        if ($language)
        {
            Session :: register(self :: PARAM_LANGUAGE, $language);
        }

        Translation :: getInstance()->setLanguageIsocode(Session :: retrieve(self :: PARAM_LANGUAGE));
    }

    /**
     *
     * @see \Chamilo\Libraries\Architecture\Application\Application::render_header()
     */
    public function render_header()
    {
        $page = Page :: getInstance();
        $page->setApplication($this);
        $page->setContainerMode('container');

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
        return Translation :: get(ClassnameUtilities :: getInstance()->getClassnameFromObject($this) . 'Information');
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
        $html[] = Page :: getInstance()->getFooter()->toHtml();

        return implode(PHP_EOL, $html);
    }

    public function getLanguages()
    {
        $language_path = Path :: getInstance()->namespaceToFullPath('Chamilo\Configuration') . 'Resources/I18n/';
        $language_files = Filesystem :: get_directory_content($language_path, Filesystem :: LIST_FILES, false);

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

    protected function renderWizardHeader()
    {
        $currentAction = $this->get_action();
        $wizardActions = array(
            self :: ACTION_INTRODUCTION,
            self :: ACTION_REQUIREMENTS,
            self :: ACTION_LICENSE,
            self :: ACTION_SETTINGS,
            self :: ACTION_OVERVIEW);

        $html = array();

        $html[] = '<div class="container-install-wizard">';

        if (in_array($currentAction, $wizardActions))
        {
            $wizardHeader = new WizardHeader();
            $wizardHeader->setStepTitles(
                array(
                    Translation :: get('IntroductionComponentTitle'),
                    Translation :: get('RequirementsComponentTitle'),
                    Translation :: get('LicenseComponentTitle'),
                    Translation :: get('SettingsComponentTitle'),
                    Translation :: get('OverviewComponentTitle')));

            $wizardHeader->setSelectedStepIndex(array_search($currentAction, $wizardActions));

            $wizardHeaderRenderer = new WizardHeaderRenderer($wizardHeader);

            $html[] = $wizardHeaderRenderer->render();
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
