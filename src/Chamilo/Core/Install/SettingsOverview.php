<?php
namespace Chamilo\Core\Install;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Theme;

/**
 *
 * @package Chamilo\Core\Install
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class SettingsOverview
{

    /**
     *
     * @var string[]
     */
    private $settingsValues;

    /**
     *
     * @param string[] $settingsValues
     */
    public function __construct($settingsValues)
    {
        $this->settingsValues = $settingsValues;
    }

    /**
     *
     * @return string[]
     */
    public function getSettingsValues()
    {
        return $this->settingsValues;
    }

    /**
     *
     * @param string[] $settingsValues
     */
    public function setSettingsValues($settingsValues)
    {
        $this->settingsValues = $settingsValues;
    }

    public function getSettingValue($setting)
    {
        return $this->settingsValues[$setting];
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = '<div class="form-horizontal form-install-settings">';

        $html[] = $this->renderSection(Translation::get('Database'), $this->getDatabaseContent());
        $html[] = $this->renderSection(Translation::get('SelectedPackages'), $this->getSelectedPackages());
        $html[] = $this->renderSection(Translation::get('GeneralProperties'), $this->getGeneralPropertiesContent());
        $html[] = $this->renderSection(Translation::get('Administrator'), $this->getAdministratorContent());
        $html[] = $this->renderSection(Translation::get('Platform'), $this->getPlatformContent());
        $html[] = $this->renderSection(Translation::get('Storage'), $this->getStorageContent());

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function getDatabaseContent()
    {
        $html = array();

        $html[] = $this->renderSetting(Translation::get('DatabaseDriver'), $this->getSettingValue('database_driver'));
        $html[] = $this->renderSetting(Translation::get('DatabaseHost'), $this->getSettingValue('database_host'));
        $html[] = $this->renderSetting(Translation::get('DatabaseName'), $this->getSettingValue('database_name'));
        $html[] = $this->renderSetting(Translation::get('DatabaseLogin'), $this->getSettingValue('database_username'));

        $html[] = $this->renderSetting(
            Translation::get('DatabasePassword'),
            $this->getSettingValue('database_password'));

        $html[] = $this->renderSetting(
            Translation::get('DatabaseExists'),
            $this->getSettingValue('database_exists') ? Translation::get(
                'ConfirmYes',
                null,
                Utilities::COMMON_LIBRARIES) : Translation::get('ConfirmNo', null, Utilities::COMMON_LIBRARIES));

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function getGeneralPropertiesContent()
    {
        $html = array();

        $html[] = $this->renderSetting(Translation::get('MainLang'), $this->getSettingValue('platform_language'));

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function getAdministratorContent()
    {
        $html = array();

        $html[] = $this->renderSetting(Translation::get('AdminEmail'), $this->getSettingValue('admin_email'));
        $html[] = $this->renderSetting(Translation::get('AdminLastName'), $this->getSettingValue('admin_surname'));
        $html[] = $this->renderSetting(Translation::get('AdminFirstName'), $this->getSettingValue('admin_firstname'));
        $html[] = $this->renderSetting(Translation::get('AdminPhone'), $this->getSettingValue('admin_phone'));
        $html[] = $this->renderSetting(Translation::get('AdminLogin'), $this->getSettingValue('admin_username'));
        $html[] = $this->renderSetting(Translation::get('AdminPass'), $this->getSettingValue('admin_password'));

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function getPlatformContent()
    {
        $html = array();

        $html[] = $this->renderSetting(Translation::get('CampusName'), $this->getSettingValue('site_name'));

        $html[] = $this->renderSetting(
            Translation::get('InstituteShortName'),
            $this->getSettingValue('organization_name'));

        $html[] = $this->renderSetting(Translation::get('InstituteURL'), $this->getSettingValue('organization_url'));

        $html[] = $this->renderSetting(
            Translation::get('AllowSelfReg'),
            Translation::get(($this->getSettingValue('self_reg') == 1 ? 'Yes' : 'No')));

        $html[] = $this->renderSetting(
            Translation::get('HashingAlgorithm'),
            $this->getSettingValue('hashing_algorithm'));

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function getStorageContent()
    {
        $html = array();

        $html[] = $this->renderSetting(Translation::get('ArchivePath'), $this->getSettingValue('archive_path'));
        $html[] = $this->renderSetting(Translation::get('CachePath'), $this->getSettingValue('cache_path'));
        $html[] = $this->renderSetting(Translation::get('GarbagePath'), $this->getSettingValue('garbage_path'));
        $html[] = $this->renderSetting(Translation::get('HotpotatoesPath'), $this->getSettingValue('hotpotatoes_path'));
        $html[] = $this->renderSetting(Translation::get('LogsPath'), $this->getSettingValue('logs_path'));
        $html[] = $this->renderSetting(Translation::get('RepositoryPath'), $this->getSettingValue('repository_path'));
        $html[] = $this->renderSetting(Translation::get('ScormPath'), $this->getSettingValue('scorm_path'));
        $html[] = $this->renderSetting(Translation::get('TempPath'), $this->getSettingValue('temp_path'));
        $html[] = $this->renderSetting(
            Translation::get('UserpicturesPath'),
            $this->getSettingValue('userpictures_path'));

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    protected function getSelectedPackages()
    {
        $selectedPackages = array();
        $wizardPackages = (array) $this->getSettingValue('install');

        foreach ($wizardPackages as $context => $value)
        {
            if (isset($value) && $value == '1')
            {
                $selectedPackages[] = $context;
            }
        }

        $html = array();

        if (count($selectedPackages) > 0)
        {
            $html = array();

            $html[] = '<div class="package-list">';
            $html[] = '<div class="package-list-items row">';

            foreach ($selectedPackages as $package)
            {
                $iconSource = Theme::getInstance()->getImagePath($package, 'Logo/22');

                $html[] = '<div class="col col-sm-6 col-md-4 col-lg-3">';
                $html[] = '<a class="btn btn-default"><img src="' . $iconSource . '"> ';

                $html[] = Translation::get('TypeName', null, $package);
                $html[] = '</a>';
                $html[] = '</div>';
            }

            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param string $label
     * @param string $content
     * @return string
     */
    protected function renderSection($label, $content)
    {
        $html = array();

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

    /**
     *
     * @param string $label
     * @param string $value
     * @return string
     */
    protected function renderSetting($label, $value)
    {
        $html = array();

        $html[] = '<div class="form-group">';
        $html[] = '<label class="col col-sm-4 control-label">' . $label . '</label>';
        $html[] = '<div class="col col-sm-8">';
        $html[] = '<p class="form-control-static">' . $value . '</p>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}