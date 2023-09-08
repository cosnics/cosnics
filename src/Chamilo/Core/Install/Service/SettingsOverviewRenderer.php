<?php
namespace Chamilo\Core\Install\Service;

use Chamilo\Core\Install\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Install
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class SettingsOverviewRenderer
{

    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string[]|string[][] $settingsValues
     */
    public function render(array $settingsValues): string
    {
        $translator = $this->getTranslator();
        $html = [];

        $html[] = '<div class="form-horizontal form-install-settings">';

        $html[] = $this->renderSection(
            $translator->trans('Database', [], Manager::CONTEXT), $this->getDatabaseContent($settingsValues)
        );
        $html[] = $this->renderSection(
            $translator->trans('SelectedPackages', [], Manager::CONTEXT), $this->getSelectedPackages($settingsValues)
        );
        $html[] = $this->renderSection(
            $translator->trans('GeneralProperties', [], Manager::CONTEXT),
            $this->getGeneralPropertiesContent($settingsValues)
        );
        $html[] = $this->renderSection(
            $translator->trans('Administrator', [], Manager::CONTEXT), $this->getAdministratorContent($settingsValues)
        );
        $html[] = $this->renderSection(
            $translator->trans('Platform', [], Manager::CONTEXT), $this->getPlatformContent($settingsValues)
        );
        $html[] = $this->renderSection(
            $translator->trans('Storage', [], Manager::CONTEXT), $this->getStorageContent($settingsValues)
        );

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string[]|string[][] $settingsValues
     */
    protected function getAdministratorContent(array $settingsValues): string
    {
        $translator = $this->getTranslator();
        $html = [];

        $html[] = $this->renderSetting(
            $translator->trans('AdminEmail', [], Manager::CONTEXT), $settingsValues['admin_email']
        );
        $html[] = $this->renderSetting(
            $translator->trans('AdminLastName', [], Manager::CONTEXT), $settingsValues['admin_surname']
        );
        $html[] = $this->renderSetting(
            $translator->trans('AdminFirstName', [], Manager::CONTEXT), $settingsValues['admin_firstname']
        );
        $html[] = $this->renderSetting(
            $translator->trans('AdminPhone', [], Manager::CONTEXT), $settingsValues['admin_phone']
        );
        $html[] = $this->renderSetting(
            $translator->trans('AdminLogin', [], Manager::CONTEXT), $settingsValues['admin_username']
        );
        $html[] = $this->renderSetting(
            $translator->trans('AdminPass', [], Manager::CONTEXT), $settingsValues['admin_password']
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string[]|string[][] $settingsValues
     */
    protected function getDatabaseContent(array $settingsValues): string
    {
        $translator = $this->getTranslator();
        $html = [];

        $html[] = $this->renderSetting(
            $translator->trans('DatabaseDriver', [], Manager::CONTEXT), $settingsValues['database']['driver']
        );
        $html[] = $this->renderSetting(
            $translator->trans('DatabaseHost', [], Manager::CONTEXT), $settingsValues['database']['host']
        );
        $html[] = $this->renderSetting(
            $translator->trans('DatabaseName', [], Manager::CONTEXT), $settingsValues['database']['name']
        );
        $html[] = $this->renderSetting(
            $translator->trans('DatabaseLogin', [], Manager::CONTEXT), $settingsValues['database']['username']
        );

        $html[] = $this->renderSetting(
            $translator->trans('DatabasePassword', [], Manager::CONTEXT), $settingsValues['database']['password']
        );

        $html[] = $this->renderSetting(
            $translator->trans('DatabaseExists', [], Manager::CONTEXT), $translator->trans(
            $settingsValues['database']['exists'] ? 'ConfirmYes' : 'ConfirmNo', [], StringUtilities::LIBRARIES
        )
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string[]|string[][] $settingsValues
     */
    protected function getGeneralPropertiesContent(array $settingsValues): string
    {
        return $this->renderSetting(
            $this->getTranslator()->trans('MainLang', [], Manager::CONTEXT), $settingsValues['platform_language']
        );
    }

    /**
     * @param string[]|string[][] $settingsValues
     */
    protected function getPlatformContent(array $settingsValues): string
    {
        $translator = $this->getTranslator();
        $html = [];

        $html[] =
            $this->renderSetting($translator->trans('CampusName', [], Manager::CONTEXT), $settingsValues['site_name']);

        $html[] = $this->renderSetting(
            $translator->trans('InstituteShortName', [], Manager::CONTEXT), $settingsValues['organization_name']
        );

        $html[] = $this->renderSetting(
            $translator->trans('InstituteURL', [], Manager::CONTEXT), $settingsValues['organization_url']
        );

        $html[] = $this->renderSetting(
            $translator->trans('AllowSelfReg', [], Manager::CONTEXT),
            $translator->trans(($settingsValues['self_reg'] == 1 ? 'Yes' : 'No'), [], StringUtilities::LIBRARIES)
        );

        $html[] = $this->renderSetting(
            $translator->trans('HashingAlgorithm', [], Manager::CONTEXT), $settingsValues['hashing_algorithm']
        );

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string[]|string[][] $settingsValues
     */
    protected function getSelectedPackages(array $settingsValues): string
    {
        $selectedPackages = [];
        $wizardPackages = (array) $settingsValues['install'];

        foreach ($wizardPackages as $context => $value)
        {
            if (isset($value) && $value == '1')
            {
                $selectedPackages[] = $context;
            }
        }

        $html = [];

        if (count($selectedPackages) > 0)
        {
            $html[] = '<div class="package-list">';
            $html[] = '<div class="package-list-items row">';

            foreach ($selectedPackages as $package)
            {
                $glyph = new NamespaceIdentGlyph($package, true);

                $html[] = '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">';
                $html[] = '<a class="btn btn-default">' . $glyph->render() . ' ';

                $html[] = $this->getTranslator()->trans('TypeName', [], $package);
                $html[] = '</a>';
                $html[] = '</div>';
            }

            $html[] = '<div class="clearfix"></div>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * @param string[]|string[][] $settingsValues
     */
    protected function getStorageContent(array $settingsValues): string
    {
        $translator = $this->getTranslator();
        $html = [];

        $html[] = $this->renderSetting(
            $translator->trans('ArchivePath', [], Manager::CONTEXT), $settingsValues['path']['archive_path']
        );
        $html[] = $this->renderSetting(
            $translator->trans('CachePath', [], Manager::CONTEXT), $settingsValues['path']['cache_path']
        );
        $html[] = $this->renderSetting(
            $translator->trans('GarbagePath', [], Manager::CONTEXT), $settingsValues['path']['garbage_path']
        );
        $html[] = $this->renderSetting(
            $translator->trans('HotpotatoesPath', [], Manager::CONTEXT), $settingsValues['path']['hotpotatoes_path']
        );
        $html[] = $this->renderSetting(
            $translator->trans('LogsPath', [], Manager::CONTEXT), $settingsValues['path']['logs_path']
        );
        $html[] = $this->renderSetting(
            $translator->trans('RepositoryPath', [], Manager::CONTEXT), $settingsValues['path']['repository_path']
        );
        $html[] = $this->renderSetting(
            $translator->trans('ScormPath', [], Manager::CONTEXT), $settingsValues['path']['scorm_path']
        );
        $html[] = $this->renderSetting(
            $translator->trans('TempPath', [], Manager::CONTEXT), $settingsValues['path']['temp_path']
        );
        $html[] = $this->renderSetting(
            $translator->trans('UserpicturesPath'), $settingsValues['path']['userpictures_path']
        );

        return implode(PHP_EOL, $html);
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    protected function renderSection(string $label, string $content): string
    {
        $html = [];

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . $label . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';
        $html[] = $content;
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    protected function renderSetting(string $label, string $value): string
    {
        $html = [];

        $html[] = '<div class="form-group">';
        $html[] = '<label class="col-xs-12 col-sm-4 control-label">' . $label . '</label>';
        $html[] = '<div class="col-xs-12 col-sm-8">';
        $html[] = '<p class="form-control-static">' . $value . '</p>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}