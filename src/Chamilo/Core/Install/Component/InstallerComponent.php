<?php
namespace Chamilo\Core\Install\Component;

use Chamilo\Core\Install\Architecture\Domain\StepResult;
use Chamilo\Core\Install\Architecture\Interfaces\InstallerObserverInterface;
use Chamilo\Core\Install\Manager;
use Chamilo\Core\Install\Service\PlatformInstaller;
use Chamilo\Core\Install\Service\PlatformInstallerFactory;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\InlineGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @package Chamilo\Core\Install\Component
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class InstallerComponent extends Manager implements NoAuthenticationSupport, InstallerObserverInterface
{

    protected PlatformInstaller $installer;

    /**
     * @throws \Exception
     */
    public function run()
    {
        $this->checkInstallationAllowed();

        session_write_close();

        $response = new StreamedResponse();
        $response->setCallback(
            function () {
                echo $this->renderHeader();
                flush();

                $this->getInstaller($this)->run();
                flush();

                echo $this->renderFooter();
                flush();

                session_start();
                $this->getSession()->remove(self::PARAM_SETTINGS);
                $this->getSession()->remove(self::PARAM_LANGUAGE);
                session_write_close();
            }
        );

        $response->send();
    }

    public function afterFilesystemPrepared(StepResult $result): string
    {
        $glyph = new FontAwesomeGlyph('folder', ['fa-lg', 'fa-fw'], null, 'fas');

        return $this->renderResult(
            $this->getTranslator()->trans('Folders', [], Manager::CONTEXT), $result->isSuccessful(),
            implode('<br />' . PHP_EOL, $result->getMessages()), $glyph
        );
    }

    public function afterInstallation(): string
    {
        $translator = $this->getTranslator();

        $message = '<a href="' . $this->getWebPathBuilder()->getBasePath() . '">' .
            $translator->trans('GoToYourNewlyCreatedPortal', [], Manager::CONTEXT) . '</a>';
        $glyph = new FontAwesomeGlyph('grin-beam', ['fa-lg', 'fa-fw'], null, 'fas');

        return $this->renderResult(
            $translator->trans('InstallationFinished', [], Manager::CONTEXT), true, $message, $glyph
        );
    }

    public function afterPackageInstallation(StepResult $result): string
    {
        $title =
            $this->getTranslator()->trans('TypeName', [], $result->getContext()) . ' (' . $result->getContext() . ')';

        $glyph = new NamespaceIdentGlyph(
            $result->getContext(), true, false, false, IdentGlyph::SIZE_SMALL, ['fa-fw']
        );

        return $this->renderResult(
            $title, $result->isSuccessful(), implode('<br />' . PHP_EOL, $result->getMessages()), $glyph
        );
    }

    public function afterPackagesInstallation(): string
    {
        return '<div class="clearfix"></div>';
    }

    public function afterPreProduction(): string
    {
        return '<div class="clearfix"></div>';
    }

    public function afterPreProductionConfigurationFileWritten(StepResult $result): string
    {
        $glyph = new FontAwesomeGlyph('cog', ['fa-lg', 'fa-fw'], null, 'fas');

        return $this->renderResult(
            $this->getTranslator()->trans('Configuration', [], Manager::CONTEXT), $result->isSuccessful(),
            implode('<br />' . PHP_EOL, $result->getMessages()), $glyph
        );
    }

    public function afterPreProductionDatabaseCreated(StepResult $result): string
    {
        $glyph = new FontAwesomeGlyph('database', ['fa-lg', 'fa-fw'], null, 'fas');

        return $this->renderResult(
            $this->getTranslator()->trans('Database', [], Manager::CONTEXT), $result->isSuccessful(),
            implode('<br />' . PHP_EOL, $result->getMessages()), $glyph
        );
    }

    public function beforeFilesystemPrepared(): string
    {
        return '';
    }

    public function beforeInstallation(): string
    {
        return '';
    }

    public function beforePackageInstallation($context): string
    {
        return '';
    }

    public function beforePackagesInstallation(): string
    {
        return '<h3>' . $this->getTranslator()->trans('Packages', [], Manager::CONTEXT) . '</h3>';
    }

    public function beforePreProduction(): string
    {
        return '<h3>' . $this->getTranslator()->trans('PreProduction', [], Manager::CONTEXT) . '</h3>';
    }

    protected function getInfo(): string
    {
        return $this->getTranslator()->trans('InstallerComponentInformation', [], self::CONTEXT);
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\ConnectionException
     */
    public function getInstaller(InstallerObserverInterface $installerObserver): PlatformInstaller
    {
        if (!isset($this->installer))
        {
            $values = unserialize($this->getSession()->get(self::PARAM_SETTINGS));

            if (!is_array($values))
            {
                $values = [];
            }

            $this->installer = $this->getInstallerFactory()->getInstallerFromArray($installerObserver, $values);
            unset($values);
        }

        return $this->installer;
    }

    protected function getInstallerFactory(): PlatformInstallerFactory
    {
        return $this->getService(PlatformInstallerFactory::class);
    }

    public function renderHeader(string $pageTitle = ''): string
    {
        $html = [];

        $html[] = parent::renderHeader($pageTitle);

        $html[] = $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Install') . 'InstallProcess.js'
        );

        return implode(PHP_EOL, $html);
    }

    public function renderResult(string $title, bool $result, string $message, InlineGlyph $image): string
    {
        $html = [];

        $html[] = $this->renderResultHeader($title, $result, $image);
        $html[] = $message;
        $html[] = $this->renderResultFooter();

        return implode(PHP_EOL, $html);
    }

    public function renderResultFooter(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderResultHeader(string $title, bool $result, InlineGlyph $image): string
    {
        $result_class = ($result ? 'installation-step-successful' : 'installation-step-failed');

        $html = [];

        $html[] = '<div class="installation-step installation-step-collapsed ' . $result_class . '">';
        $html[] = '<div class="title">' . $image->render() . $title . '</div>';
        $html[] = '<div class="description">';

        return implode(PHP_EOL, $html);
    }
}
