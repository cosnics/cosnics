<?php
namespace Chamilo\Core\Lynx\Manager\Action;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Lynx\Action;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

set_time_limit(0);

/**
 * Package installation
 * 
 * @author Hans De Bisschop - Erasmus Hogeschool Brussel
 * @author Magali Gillard - Erasmus Hogeschool Brussel
 * @author Sven Vanpoucke - Hogeschool Gent - Cleanup, code refactoring and bugfixes, comments
 */
class PackageInstaller extends Action
{

    private $additional_packages = array();

    /**
     * Runs the package installer
     * 
     * @return boolean
     */
    public function run()
    {
        if ($this->initialize() && $this->process())
        {
            $title = Translation::get(
                'Finished', 
                null, 
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
            $image = Theme::getInstance()->getImagePath(
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
                'PackageAction/Finished');
            return $this->action_successful($title, $image, Translation::get('PackageCompletelyInstalled'));
        }
        else
        {
            $title = Translation::get(
                'Failed', 
                null, 
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
            $image = Theme::getInstance()->getImagePath(
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
                'PackageAction/Failed');
            return $this->action_failed($title, $image, Translation::get('PackageInstallFailed'));
        }
    }

    /**
     * Initializes the package installer
     * 
     * @return boolean
     */
    public function initialize()
    {
        $title = Translation::get(
            'Initialization', 
            null, 
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
        $image = Theme::getInstance()->getImagePath(
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
            'PackageAction/Initialization');
        
        if (! $this->get_package() instanceof \Chamilo\Configuration\Package\Storage\DataClass\Package)
        {
            return $this->action_failed($title, $image, Translation::get('PackageAttributesNotFound'));
        }
        else
        {
            $this->add_message(Translation::get('PackageAttributesFound'));
        }
        
        // Check registration
        if ($this->is_package_registered())
        {
            return $this->action_failed($title, $image, Translation::get('PackageIsAlreadyRegistered'));
        }
        else
        {
            $this->add_message(Translation::get('PackageNotYetRegistered'));
        }
        
        return $this->action_successful($title, $image, Translation::get('PackageInstallInitialized'));
    }

    /**
     * Checks if the package is registered
     * 
     * @return boolean
     */
    public function is_package_registered()
    {
        return Configuration::is_registered($this->get_package()->get_context());
    }

    /**
     * Installs the package
     * 
     * @return boolean
     */
    public function process()
    {
        $title = Translation::get(
            'Installation', 
            array('PACKAGE' => Translation::get('TypeName', null, $this->get_package()->get_context())), 
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
        $image = Theme::getInstance()->getImagePath(
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
            'PackageAction/Installation');
        
        $installer = \Chamilo\Configuration\Package\Action\Installer::factory(
            $this->get_package()->get_context(), 
            array());
        if (! $installer->run())
        {
            $this->add_message($installer->retrieve_message());
            return $this->action_failed($title, $image, Translation::get('InitializationFailed'));
        }
        else
        {
            $this->add_message($installer->retrieve_message());
            $this->action_successful($title, $image);
        }
        
        $this->add_additional_packages($installer->get_additional_packages());
        
        $title = Translation::get(
            'AdditionalPackages', 
            null, 
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
        $image = Theme::getInstance()->getImagePath(
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
            'PackageAction/AdditionalPackages');
        
        while (($additional_package = $this->get_next_additional_package()) != null)
        {
            if (! $this->install_additional_package($additional_package))
            {
                return $this->action_failed($title, $image, Translation::get('AdditionalPackagesFailed'));
            }
        }
        
        return true;
    }

    /**
     * Installs an additional package
     * 
     */
    private function install_additional_package($context)
    {
        $title = Translation::get(
            'Installation', 
            array('PACKAGE' => Translation::get('TypeName', null, $context)), 
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
        $image = Theme::getInstance()->getImagePath($context, 'Logo/48');
        
        $installer = \Chamilo\Configuration\Package\Action\Installer::factory($context, array());
        
        if (! $installer->run())
        {
            $this->add_message($installer->retrieve_message());
            return $this->action_failed($title, $image, Translation::get('InitializationFailed'));
        }
        else
        {
            $this->add_message($installer->retrieve_message());
            $this->action_successful($title, $image);
        }
        
        $this->add_additional_packages($installer->get_additional_packages());
        
        return true;
    }

    /**
     * Returns the additional packages
     * 
     * @return string[]
     */
    public static function get_additional_packages()
    {
        return $this->additional_packages;
    }

    /**
     * Sets the additional packages
     * 
     * @param string[]
     */
    public function set_additional_packages($additional_packages)
    {
        $this->additional_packages = $additional_packages;
    }

    /**
     * Adds an additional package to the list of additional packages
     * 
     * @param string
     */
    public function add_additional_package($context)
    {
        array_push($this->additional_packages, $context);
    }

    /**
     * Adds multiple additional packages to the list of additional packages
     * 
     * @param string[]
     */
    public function add_additional_packages($additional_packages)
    {
        foreach ($additional_packages as $additional_package)
        {
            $this->add_additional_package($additional_package);
        }
    }

    /**
     * Removes and returns the first package from the list of additional packages
     * 
     * @return string
     */
    public function get_next_additional_package()
    {
        return array_shift($this->additional_packages);
    }
}
