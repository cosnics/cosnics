<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Factory;
use Chamilo\Core\Install\Manager;
use Chamilo\Core\Install\Observer\InstallerObserver;
use Chamilo\Core\Install\StepResult;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 *
 * @package Chamilo\Core\Install\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class InstallerComponent extends Manager implements NoAuthenticationSupport, InstallerObserver
{

    private $installer;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkInstallationAllowed();

        $this->installer = $this->getInstaller($this);

        $wizardProcess = $this;

        session_write_close();

        $response = new StreamedResponse();
        $response->setCallback(
            function () use ($wizardProcess) {
                echo $wizardProcess->render_header();
                flush();

                $wizardProcess->getInstaller($this)->run();
                flush();

                echo $wizardProcess->render_footer();
                flush();

                session_start();
                Session::unregister(self::PARAM_SETTINGS);
                Session::unregister(self::PARAM_LANGUAGE);
                session_write_close();
            }
        );

        $response->send();
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::afterFilesystemPrepared()
     */
    public function afterFilesystemPrepared(StepResult $result)
    {
        $glyph = new FontAwesomeGlyph('folder', array('fa-lg', 'fa-fw'), null, 'fas');

        return $this->renderResult(
            Translation::get('Folders'), $result->get_success(), implode('<br />' . PHP_EOL, $result->get_messages()),
            $glyph
        );
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::afterInstallation()
     */
    public function afterInstallation()
    {
        $message = '<a href="' . Path::getInstance()->getBasePath(true) . '">' .
            Translation::get('GoToYourNewlyCreatedPortal') . '</a>';
        $glyph = new FontAwesomeGlyph('grin-beam', array('fa-lg', 'fa-fw'), null, 'fas');

        return $this->renderResult(Translation::get('InstallationFinished'), true, $message, $glyph);
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::afterPackageInstallation()
     */
    public function afterPackageInstallation(StepResult $result)
    {
        $title = Translation::get('TypeName', null, $result->get_context()) . ' (' . $result->get_context() . ')';

        $glyph = new NamespaceIdentGlyph($result->get_context(), true, false, false, Theme::ICON_SMALL, array('fa-fw'));

        return $this->renderResult(
            $title, $result->get_success(), implode('<br />' . PHP_EOL, $result->get_messages()), $glyph
        );
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::afterPackagesInstallation()
     */
    public function afterPackagesInstallation()
    {
        return '<div class="clear"></div>';
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::afterPreProduction()
     */
    public function afterPreProduction()
    {
        return '<div class="clear"></div>';
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::afterPreProductionConfigurationFileWritten()
     */
    public function afterPreProductionConfigurationFileWritten(StepResult $result)
    {
        $glyph = new FontAwesomeGlyph('cog', array('fa-lg', 'fa-fw'), null, 'fas');

        return $this->renderResult(
            Translation::get('Configuration'), $result->get_success(),
            implode('<br />' . PHP_EOL, $result->get_messages()), $glyph
        );
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::afterPreProductionDatabaseCreated()
     */
    public function afterPreProductionDatabaseCreated(StepResult $result)
    {
        $glyph = new FontAwesomeGlyph('database', array('fa-lg', 'fa-fw'), null, 'fas');

        return $this->renderResult(
            Translation::get('Database'), $result->get_success(), implode('<br />' . PHP_EOL, $result->get_messages()),
            $glyph
        );
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::beforeFilesystemPrepared()
     */
    public function beforeFilesystemPrepared()
    {
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::beforeInstallation()
     */
    public function beforeInstallation()
    {
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::beforePackageInstallation()
     */
    public function beforePackageInstallation($context)
    {
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::beforePackagesInstallation()
     */
    public function beforePackagesInstallation()
    {
        return '<h3>' . Translation::get('Packages') . '</h3>';
    }

    /**
     *
     * @see \Chamilo\Core\Install\Observer\InstallerObserver::beforePreProduction()
     */
    public function beforePreProduction()
    {
        return '<h3>' . Translation::get('PreProduction') . '</h3>';
    }

    /**
     *
     * @return \Chamilo\Core\Install\PlatformInstaller
     */
    public function getInstaller(InstallerObserver $installerObserver)
    {
        if (!isset($this->installer))
        {
            $values = unserialize(Session::retrieve(self::PARAM_SETTINGS));
            if (!is_array($values))
            {
                $values = [];
            }

            $factory = new Factory();
            $this->installer = $factory->getInstallerFromArray($installerObserver, $values);
            unset($values);
        }

        return $this->installer;
    }

    /**
     *
     * @param string $title
     * @param string $result
     * @param string $message
     * @param string $image
     *
     * @return string
     */
    public function renderResult($title, $result, $message, $image)
    {
        $html = array();

        $html[] = $this->renderResultHeader($title, $result, $image);
        $html[] = $message;
        $html[] = $this->renderResultFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function renderResultFooter()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $title
     * @param string $result
     * @param string $image
     *
     * @return string
     */
    public function renderResultHeader($title, $result, $image)
    {
        $result_class = ($result ? 'installation-step-successful' : 'installation-step-failed');

        $html = array();

        if ($image instanceof InlineGlyph)
        {
            $html[] = '<div class="installation-step installation-step-collapsed ' . $result_class . '">';
            $html[] = '<div class="title">' . $image->render() . $title . '</div>';
        }
        else
        {
            $html[] = '<div class="installation-step installation-step-collapsed ' . $result_class .
                '" style="background-image: url(' . $image . ');">';
            $html[] = '<div class="title">' . $title . '</div>';
        }

        $html[] = '<div class="description">';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @see \Chamilo\Core\Install\Manager::render_header()
     */
    public function render_header()
    {
        $html = array();

        $html[] = parent::render_header();

        $html[] = ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath('Chamilo\Core\Install', true) . 'InstallProcess.js'
        );

        return implode(PHP_EOL, $html);
    }
}
