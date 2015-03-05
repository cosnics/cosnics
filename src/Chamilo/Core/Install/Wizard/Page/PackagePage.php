<?php
namespace Chamilo\Core\Install\Wizard\Page;

use Chamilo\Core\Install\Wizard\InstallWizardPage;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Configuration\Package\PackageList;
use Chamilo\Configuration\Package\PlatformPackageBundles;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Configuration\Package\Storage\DataClass\Package;

/**
 *
 * @package Chamilo\Core\Install\Wizard\Page
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PackagePage extends InstallWizardPage
{

    public function buildForm()
    {
        $this->set_lang($this->controller->exportValue('page_language', 'install_language'));
        $this->_formBuilt = true;

        $this->addElement('html', '<div class="package-selection">');

        $html = array();
        $html[] = '<h3>';
        $html[] = Translation :: get('AllPackages');
        $html[] = '<img src = "' . Theme :: getInstance()->getImagePath('Chamilo\Configuration', 'Form/CheckChecked') .
             '" class="package-list-select-all" /><img src = "' . Theme :: getInstance()->getImagePath(
                'Chamilo\Configuration',
                'Form/CheckUnchecked') . '" class="package-list-select-none" />';
        $html[] = '</h3>';
        $this->addElement('html', implode(PHP_EOL, $html));

        $packageList = PlatformPackageBundles :: getInstance()->get_package_list();
        $this->renderPackages($packageList);

        $html = array();
        $html[] = '<script type="text/javascript" src="' .
             Path :: getInstance()->getJavascriptPath('Chamilo\Core\Install', true) . 'Install.js"></script>';
        $html[] = '</div>';
        $this->addElement('html', implode(PHP_EOL, $html));

        $buttons = array();
        $buttons[] = $this->createElement(
            'style_submit_button',
            $this->getButtonName('back'),
            Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal previous'));
        $buttons[] = $this->createElement(
            'style_submit_button',
            $this->getButtonName('next'),
            Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES),
            array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
    }

    /**
     *
     * @param \Chamilo\Configuration\Package\PackageList $packageList
     */
    public function renderPackages(PackageList $packageList)
    {
        $html = array();

        $html[] = '<div class="clear"></div>';

        $renderer = $this->defaultRenderer();
        $packages = $this->determinePackages($packageList);

        if (count($packages) > 0)
        {
            $firstPackage = current($packages);
            $packageType = Translation :: get('TypeCategory', null, $firstPackage->get_context());

            $html = array();
            $html[] = '<div class="package-list">';
            $html[] = '<h3>';
            $html[] = $packageType;
            $html[] = '<img src = "' . Theme :: getInstance()->getImagePath(
                'Chamilo\Configuration',
                'Form/CheckChecked') . '" class="package-list-select-all" /><img src = "' . Theme :: getInstance()->getImagePath(
                'Chamilo\Configuration',
                'Form/CheckUnchecked') . '" class="package-list-select-none" />';
            $html[] = '</h3>';
            $html[] = '<div class="package-list-items">';
            $this->addElement('html', implode(PHP_EOL, $html));

            foreach ($packages as $title => $package)
            {
                $html = array();
                $html[] = '<div class="' . $this->getPackageClasses($package) . '" style="background-image: url(' .
                     Theme :: getInstance()->getImagePath($package->get_context(), 'Logo/22') . ')">';
                $this->addElement('html', implode(PHP_EOL, $html));

                $checkbox_name = 'install_' .
                     ClassnameUtilities :: getInstance()->getNamespaceId($package->get_context());
                $this->addElement(
                    'checkbox',
                    'install[' . $package->get_context() . ']',
                    '',
                    '',
                    array('style' => 'display: none'));
                $renderer->setElementTemplate('{element}', 'install[' . $package->get_context() . ']');

                $html = array();
                $html[] = $title;
                $html[] = '</div>';
                $this->addElement('html', implode(PHP_EOL, $html));

                $extra = $package->get_extra();

                if ($extra['core-install'] || $extra['default-install'])
                {
                    $defaults['install'][$package->get_context()] = 1;
                }
            }

            $this->setDefaults($defaults);

            $html = array();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = '</div>';
            $this->addElement('html', implode(PHP_EOL, $html));
        }

        foreach ($packageList->get_children() as $child)
        {
            $this->renderPackages($child);
        }
    }

    /**
     *
     * @param \Chamilo\Configuration\Package\PackageList $packageList
     * @return \Chamilo\Configuration\Package\Storage\DataClass\Package[]
     */
    public function determinePackages(PackageList $packageList)
    {
        $packages = array();

        foreach ($packageList->get_packages() as $namespace => $package)
        {
            if (strpos($namespace, '\Integration\Chamilo\\') === false)
            {
                $packages[Translation :: get('TypeName', null, $package->get_context())] = $package;
            }
        }

        ksort($packages);

        return $packages;
    }

    /**
     *
     * @param \Chamilo\Configuration\Package\Storage\DataClass\Package $package
     * @return string
     */
    private function getPackageClasses(Package $package)
    {
        $extra = $package->get_extra();

        if ($extra['core-install'])
        {
            return 'package-list-item-core';
        }

        if ($extra['default-install'])
        {
            return 'package-list-item package-list-item-selected';
        }

        return 'package-list-item';
    }

    /**
     *
     * @see \Chamilo\Core\Install\Wizard\InstallWizardPage::get_info()
     */
    public function get_info()
    {
        $html = array();

        $html[] = Translation :: get(ClassnameUtilities :: getInstance()->getClassnameFromObject($this) . 'Information');
        $html[] = '<br /><br />';

        $html[] = '<div style="background-image: url(' . Theme :: getInstance()->getImagePath(
            'Chamilo\Configuration',
            'Logo/22') . ')" class="package-list-item-core">';
        $html[] = Translation :: get('CorePackage');
        $html[] = '</div>';

        $html[] = '<div style="background-image: url(' . Theme :: getInstance()->getImagePath(
            'Chamilo\Configuration',
            'Logo/22') . ')" class="package-list-item">';
        $html[] = Translation :: get('AvailablePackage');
        $html[] = '</div>';

        $html[] = '<div style="background-image: url(' . Theme :: getInstance()->getImagePath(
            'Chamilo\Configuration',
            'Logo/22') . ')" class="package-list-item package-list-item-selected">';
        $html[] = Translation :: get('SelectedPackage');
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
