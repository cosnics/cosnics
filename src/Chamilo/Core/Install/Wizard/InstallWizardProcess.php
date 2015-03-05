<?php
namespace Chamilo\Core\Install\Wizard;

use Chamilo\Core\Install\Exception\InstallFailedException;
use Chamilo\Core\Install\Factory;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\StepResult;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Exception;
use HTML_QuickForm_Action;

/**
 * $Id: install_wizard_process.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 *
 * @package install.lib.installmanager.component.inc.wizard
 */

/**
 * This class implements the action to take after the user has completed a course maintenance wizard
 */
class InstallWizardProcess extends HTML_QuickForm_Action implements InstallerObserver
{

    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    private $current_step;

    private $counter;

    private $optional_application_counter;

    private $optional;

    /**
     * Constructor
     *
     * @param $parent Tool The repository tool in which the wizard runs.
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        $this->current_step = "";
        $this->counter = 0;
        $this->optional_application_counter = 0;
        $this->optional = false;
    }

    public function perform($page, $actionName)
    {
        $this->installer = $this->build_installer($page);
        $this->installer->add_observer($this);

        try
        {
            $html = array();

            $html[] = $this->render_header($page);
            $html[] = $this->installer->perform_install();
            // $page->controller->container(true);

            $html[] = $this->parent->render_footer();

            return implode(PHP_EOL, $html);
        }
        catch (InstallFailedException $exception)
        {
            return $this->process_result(
                Translation :: get('PlatformInstallFailed') . ' - ' . $exception->get_package(),
                false,
                $exception->getMessage(),
                Theme :: getInstance()->getImagePath('Chamilo\Core\Install', 'Place/Failed'));
        }
    }

    private function build_installer($page)
    {
        if (array_key_exists('config_file', $_FILES))
        {
            $values = array();

            // TODO check why this gives install errors for missing path ??
            if ($_FILES['config_file']['tmp_name'])
            {
                require_once ($_FILES['config_file']['tmp_name']);
            }
        }
        else
        {
            $values = $page->controller->exportValues();
        }
        $url_append = str_replace('/install/index.php', '', $_SERVER['REQUEST_URI']);
        $values['url_append'] = $url_append;

        $factory = new Factory();
        $installer = $factory->build_installer_from_array($values);
        unset($values);
        return $installer;
    }

    public function render_header($page)
    {
        $values = $this->values;
        $all_pages = $page->controller->_pages;

        $total_number_of_pages = count($all_pages) + 1;

        $page_number = 1;

        foreach ($all_pages as $page)
        {
            $name = $page_number . '.&nbsp;&nbsp;' . $page->get_breadcrumb();
            BreadcrumbTrail :: get_instance()->add(new Breadcrumb(null, $name));
            $page_number ++;
        }

        $name = $total_number_of_pages . '.&nbsp;&nbsp;' . Translation :: get('Installation');
        BreadcrumbTrail :: get_instance()->add(new Breadcrumb(null, $name));

        $html = array();

        $html[] = $this->parent->render_header();

        $html[] = '<div id="theForm" style="margin: 10px;">';
        $html[] = '<div id="select" class="row"><div class="formc formc_no_margin">';
        $html[] = '<b>' . Translation :: get('Step') . ' ' . $total_number_of_pages . ' ' . Translation :: get('of') .
             ' ' . $total_number_of_pages . ' &ndash; ' . Translation :: get('Installation') . '</b><br />';

        $html[] = Translation :: get('InstallationDescription');
        $html[] = '</div>';
        $html[] = '</div></div>';
        $html[] = '<div class="clear"></div>';

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getConfigurationPath(true) . 'Resources/Javascript/InstallProcess.js');

        return implode(PHP_EOL, $html);
    }

    public function process_result($title, $result, $message, $image)
    {
        $html = array();

        $html[] = $this->display_install_block_header($title, $result, $image);
        $html[] = $message;
        $html[] = $this->display_install_block_footer();

        return implode(PHP_EOL, $html);
    }

    public function display_install_block_header($title, $result, $image)
    {
        $counter = $this->counter;

        $result_class = ($result ? 'installation-step-successful' : 'installation-step-failed');

        $html = array();
        $html[] = '<div class="installation-step installation-step-collapsed ' . $result_class .
             '" style="background-image: url(' . $image . ');">';
        $html[] = '<div class="title">' . $title . '</div>';
        $html[] = '<div class="description">';

        return implode(PHP_EOL, $html);
    }

    public function display_install_block_footer()
    {
        $html = array();
        $html[] = '</div>';
        $html[] = '</div>';
        return implode(PHP_EOL, $html);
    }

    public function before_filesystem_prepared()
    {
    }

    public function after_filesystem_prepared(StepResult $result)
    {
        $image = Theme :: getInstance()->getImagePath('Chamilo\Core\Install', 'PlaceFolder');
        return $this->process_result(
            Translation :: get('Folders'),
            $result->get_success(),
            implode('<br />' . "\n", $result->get_messages()),
            $image);
    }

    public function after_preprod()
    {
        return '<div class="clear"></div>';
    }

    public function before_install()
    {
    }

    public function after_install()
    {
        $message = '<a href="' . Path :: getInstance()->getBasePath(true) . '">' .
             Translation :: get('GoToYourNewlyCreatedPortal') . '</a>';
        $image = Theme :: getInstance()->getImagePath('Chamilo\Core\Install', 'Place/Finished');
        return $this->process_result(Translation :: get('InstallationFinished'), true, $message, $image);
    }

    public function before_preprod()
    {
        return '<h3>' . Translation :: get('PreProduction') . '</h3>';
    }

    public function before_packages_install()
    {
        return '<h3>' . Translation :: get('Packages') . '</h3>';
    }

    public function before_package_install($context)
    {
    }

    public function after_package_install(StepResult $result)
    {
        $image = Theme :: getInstance()->getImagePath($result->get_context(), 'Logo/22');
        $title = Translation :: get('TypeName', null, $result->get_context()) . ' (' . $result->get_context() . ')';

        return $this->process_result(
            $title,
            $result->get_success(),
            implode('<br />' . "\n", $result->get_messages()),
            $image);
    }

    public function after_packages_install()
    {
        return '<div class="clear"></div>';
    }

    public function preprod_config_file_written(StepResult $result)
    {
        $image = Theme :: getInstance()->getImagePath('Chamilo\Core\Install', 'Place/Config');
        return $this->process_result(
            Translation :: get('Configuration'),
            $result->get_success(),
            implode('<br />' . "\n", $result->get_messages()),
            $image);
    }

    public function preprod_db_created(StepResult $result)
    {
        $image = Theme :: getInstance()->getImagePath('Chamilo\Core\Install', 'Place/Database');
        return $this->process_result(
            Translation :: get('Database'),
            $result->get_success(),
            implode('<br />' . "\n", $result->get_messages()),
            $image);
    }
}
