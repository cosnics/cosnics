<?php
namespace Chamilo\Core\Install;

use Chamilo\Configuration\Service\FileConfigurationLocator;
use Chamilo\Core\Install\Format\Structure\FooterRenderer;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Architecture\Interfaces\NoContextComponent;
use Chamilo\Libraries\Format\Structure\FooterRendererInterface;
use Chamilo\Libraries\Format\Structure\HeaderRendererInterface;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeader;
use Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeaderRenderer;
use DOMDocument;
use DOMXPath;
use Exception;
use Symfony\Component\Finder\Iterator\FileTypeFilterIterator;

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
     * @throws \Exception
     */
    protected function checkInstallationAllowed(): bool
    {
        if (!$this->getFileConfigurationLocator()->isAvailable())
        {
            return true;
        }
        else
        {
            $installationBlocked = (bool) $this->getConfigurationConsulter()->getSetting(
                ['Chamilo\Core\Admin', 'installation_blocked']
            );

            if ($installationBlocked)
            {
                throw new Exception(
                    $this->getTranslator()->trans(
                        'InstallationBlockedByAdministrator', [], Manager::CONTEXT
                    )
                );
            }

            return false;
        }
    }

    public function getFileConfigurationLocator(): FileConfigurationLocator
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

    abstract protected function getInfo(): string;

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        $languagePath = $this->getSystemPathBuilder()->namespaceToFullPath('Chamilo\Configuration') . 'Resources/I18n/';
        $languageFiles =
            $this->getFilesystemTools()->getDirectoryContent($languagePath, FileTypeFilterIterator::ONLY_FILES, false);

        $languageList = [];

        foreach ($languageFiles as $language_file)
        {
            $fileInfo = pathinfo($language_file);
            $languageInfoFile = $languagePath . $fileInfo['filename'] . '.info';

            if (file_exists($languageInfoFile))
            {
                $domDocument = new DOMDocument('1.0', 'UTF-8');
                $domDocument->load($languageInfoFile);
                $domXpath = new DOMXPath($domDocument);

                $language_node = $domXpath->query('/packages/package')->item(0);

                $languageList[$domXpath->query('extra/isocode', $language_node)->item(0)->nodeValue] = $domXpath->query(
                    'name', $language_node
                )->item(0)->nodeValue;
            }
        }

        return $languageList;
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\WizardHeader\WizardHeader
     */
    protected function getWizardHeader(): WizardHeader
    {
        if (!isset($this->wizardHeader))
        {
            $translator = $this->getTranslator();

            $currentAction = $this->get_action();
            $wizardActions = $this->getWizardHeaderActions();

            $this->wizardHeader = new WizardHeader();
            $this->wizardHeader->setStepTitles(
                [
                    $translator->trans('IntroductionComponentTitle', [], self::CONTEXT),
                    $translator->trans('RequirementsComponentTitle', [], self::CONTEXT),
                    $translator->trans('LicenseComponentTitle', [], self::CONTEXT),
                    $translator->trans('SettingsComponentTitle', [], self::CONTEXT),
                    $translator->trans('OverviewComponentTitle', [], self::CONTEXT),
                    $translator->trans('InstallerComponentTitle', [], self::CONTEXT)
                ]
            );

            $this->wizardHeader->setSelectedStepIndex(array_search($currentAction, $wizardActions));
        }

        return $this->wizardHeader;
    }

    /**
     * @return string[]
     */
    protected function getWizardHeaderActions(): array
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

    protected function initialize(): void
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '7200');

        $this->setLanguage();
    }

    public function renderFooter(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = $this->getFooterRenderer()->render();

        return implode(PHP_EOL, $html);
    }

    public function renderHeader(string $pageTitle = ''): string
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

    protected function renderWizardHeader(): string
    {
        $wizardHeaderRenderer = new WizardHeaderRenderer($this->getWizardHeader());

        $html = [];

        $html[] = '<div class="container-install-wizard">';
        $html[] = $wizardHeaderRenderer->render();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    protected function setLanguage(): void
    {
        $language = $this->getRequest()->query->get(self::PARAM_LANGUAGE, 'en');

        if ($language)
        {
            $this->getSession()->set(self::PARAM_LANGUAGE, $language);
        }

        $this->getTranslator()->setLocale($this->getSession()->get(self::PARAM_LANGUAGE));
    }
}
