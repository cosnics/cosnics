<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Factory;
use Chamilo\Core\Install\Manager;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\StepResult;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 *
 * @package Chamilo\Core\Install\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class InstallerComponent extends Manager implements NoAuthenticationSupport, InstallerObserver
{

    private $current_step;

    private $counter;

    private $optional_application_counter;

    private $optional;

    private $installer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkInstallationAllowed();
        
        $this->current_step = "";
        $this->counter = 0;
        $this->optional_application_counter = 0;
        $this->optional = false;

        $this->installer = $this->build_installer();
        $this->installer->add_observer($this);

        $wizardProcess = $this;

        session_write_close();

        $response = new StreamedResponse();
        $response->setCallback(
            function () use($wizardProcess) {
                echo $wizardProcess->render_header();
                flush();

                $wizardProcess->getInstaller()->perform_install();
                flush();

                echo $wizardProcess->render_footer();
                flush();

                session_start();
                Session :: unregister(self :: PARAM_SETTINGS);
                Session :: unregister(self :: PARAM_LANGUAGE);
                session_write_close();
            });

        $response->send();
    }

    public function getInstaller()
    {
        return $this->installer;
    }

    public function getParent()
    {
        return $this->parent;
    }

    private function build_installer($page)
    {
        $values = unserialize(Session :: retrieve(self :: PARAM_SETTINGS));
        $url_append = str_replace('/install/index.php', '', $_SERVER['REQUEST_URI']);
        $values['url_append'] = $url_append;

        $factory = new Factory();
        $installer = $factory->build_installer_from_array($values);
        unset($values);
        return $installer;
    }

    public function render_header()
    {
        $html = array();

        $html[] = parent :: render_header();

        $html[] = ResourceManager :: get_instance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath('Chamilo\Core\Install', true) . 'InstallProcess.js');

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
        $image = Theme :: getInstance()->getImagePath('Chamilo\Core\Install', 'Place/Folder');
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
