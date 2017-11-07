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
 * @package admin.lib.package_installer
 */
class PackageRemover extends Action
{

    /**
     * Runs the package remover
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
            return $this->action_successful($title, $image, Translation::get('PackageCompletelyRemoved'));
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
            return $this->action_failed($title, $image, Translation::get('PackageRemoveFailed'));
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
        if (! $this->is_package_registered())
        {
            return $this->action_failed($title, $image, Translation::get('PackageIsNotInstalled'));
        }
        else
        {
            $this->add_message(Translation::get('PackageNotYetRemoved'));
        }
        
        return $this->action_successful($title, $image, Translation::get('PackageRemoveInitialized'));
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
        $this->process_additional_packages($this->get_package()->get_context());
        
        return true;
    }

    public function process_additional_packages($context)
    {
        $remover = \Chamilo\Configuration\Package\Action\Remover::factory($context);
        
        $additional_packages = $remover->get_additional_packages();
        
        foreach ($additional_packages as $additional_package)
        {
            $this->process_additional_packages($additional_package);
        }
        
        $title = Translation::get(
            'Removal', 
            array('PACKAGE' => Translation::get('TypeName', null, $this->get_package()->get_context())), 
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
        $image = Theme::getInstance()->getImagePath(
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
            'PackageAction/Removal');
        
        if (! $remover->run())
        {
            $this->add_message($remover->retrieve_message());
            return $this->action_failed($title, $image, Translation::get('InitializationFailed'));
        }
        else
        {
            $this->add_message($remover->retrieve_message());
            $this->action_successful($title, $image);
        }
    }
}
