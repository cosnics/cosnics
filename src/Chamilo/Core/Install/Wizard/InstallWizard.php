<?php
namespace Chamilo\Core\Install\Wizard;

use Chamilo\Core\Install\Wizard\Page\DatabasePage;
use Chamilo\Core\Install\Wizard\Page\IntroductionPage;
use Chamilo\Core\Install\Wizard\Page\LanguagePage;
use Chamilo\Core\Install\Wizard\Page\LicensePage;
use Chamilo\Core\Install\Wizard\Page\OverviewPage;
use Chamilo\Core\Install\Wizard\Page\PackagePage;
use Chamilo\Core\Install\Wizard\Page\PreconfiguredPage;
use Chamilo\Core\Install\Wizard\Page\RequirementsPage;
use Chamilo\Core\Install\Wizard\Page\SettingsPage;
use HTML_QuickForm_Controller;

/**
 * $Id: install_wizard.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.install_manager.component.inc
 */
/**
 * A wizard which guides the user to several steps to complete a maintenance action on a course.
 */
class InstallWizard extends HTML_QuickForm_Controller
{

    /**
     * The repository tool in which this wizard runs.
     */
    private $parent;

    /**
     * Creates a new MaintenanceWizard
     *
     * @param Tool $parent The repository tool in which this wizard runs.
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        parent :: __construct('InstallWizard', true);
        $this->addPage(new IntroductionPage('page_introduction', $this->parent));
        $this->addPage(new LanguagePage('page_language', $this->parent));
        $this->addPage(new RequirementsPage('page_requirements', $this->parent));
        $this->addPage(new LicensePage('page_license', $this->parent));
        $this->addPage(new DatabasePage('page_database', $this->parent));
        $this->addPage(new PackagePage('page_package', $this->parent));
        $this->addPage(new SettingsPage('page_settings', $this->parent));
        $this->addPage(new OverviewPage('page_overview', $this->parent));

        list($page, $action) = $this->getActionName();

        if ($page == 'page_language' || $page == 'page_preconfigured')
        {
            $this->addPage(new PreconfiguredPage('page_preconfigured', $this->parent));
        }

        $this->addAction('process', new InstallWizardProcess($this->parent));
        $this->addAction('display', new InstallWizardDisplay($this->parent));
    }

    public function run()
    {
        $this->_actionName = $this->getActionName();
        list($page, $action) = $this->_actionName;
        return $this->_pages[$page]->handle($action);
    }
}
