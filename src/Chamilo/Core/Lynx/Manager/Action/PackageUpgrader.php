<?php
namespace Chamilo\Core\Lynx\Manager\Action;

use Chamilo\Core\Lynx\Action;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;

set_time_limit(0);

/**
 * Package installation
 * 
 * @author Hans De Bisschop - Erasmus Hogeschool Brussel
 * @author Magali Gillard - Erasmus Hogeschool Brussel
 */
class PackageUpgrader extends Action
{

    /**
     *
     * @var multitype:string $additional_packages
     */
    private $additional_packages = array();

    /**
     *
     * @var boolean
     */
    private $upgrade_additional_packages;

    /**
     *
     * @var \configuration\package\Upgrader
     */
    private $upgrader;

    public function __construct($context, $upgrade_additional_packages = true)
    {
        parent::__construct($context);
        $this->upgrade_additional_packages = $upgrade_additional_packages;
    }

    /**
     * Runs the package remover
     * 
     * @return boolean
     */
    public function run()
    {
        if ($this->process())
        {
            $title = Translation::get(
                'Finished', 
                null, 
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
            $image = Theme::getInstance()->getImagePath(
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
                'PackageAction/Finished');
            return $this->action_successful($title, $image, Translation::get('PackageCompletelyUpgraded'));
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
            return $this->action_failed($title, $image, Translation::get('PackageUpgradeFailed'));
        }
    }

    /**
     * Installs the package
     * 
     * @return boolean
     */
    public function process()
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
        
        $title = Translation::get(
            'Upgrade', 
            array('PACKAGE' => Translation::get('TypeName', null, $this->get_package()->get_context())), 
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
        $image = Theme::getInstance()->getImagePath(
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
            'PackageAction/Upgrade');
        
        try
        {
            $this->upgrader = \Chamilo\Configuration\Package\Action\Upgrader::factory(
                $this->get_package()->get_context());
        }
        catch (\Exception $ex)
        {
            $this->add_message(
                Translation::get(
                    'UpgraderNotFoundIgnoringPackage', 
                    array('CONTEXT' => $this->get_package()->get_context())));
            
            return $this->action_successful($title, $image);
        }
        
        if (! $this->upgrader->run())
        {
            $this->add_message($this->upgrader->retrieve_message());
            return $this->action_failed($title, $image, Translation::get('UpgradeFailed'));
        }
        else
        {
            $this->add_message($this->upgrader->retrieve_message());
            $this->action_successful($title, $image);
        }
        
        if ($this->upgrade_additional_packages)
        {
            $this->add_additional_packages($this->upgrader->get_additional_packages());
            
            $title = Translation::get(
                'AdditionalPackages', 
                null, 
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
            $image = Theme::getInstance()->getImagePath(
                ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2), 
                'PackageAction/AdditionalPackages');
            
            while (($additional_package = $this->get_next_additional_package()) != null)
            {
                if (! $this->upgrade_additional_package($additional_package))
                {
                    return $this->action_failed($title, $image, Translation::get('AdditionalPackagesFailed'));
                }
            }
        }
        
        return true;
    }

    /**
     * Upgrades an additional package
     * 
     * @param string $context
     */
    private function upgrade_additional_package($context)
    {
        $title = Translation::get(
            'Upgrade', 
            array('PACKAGE' => Translation::get('TypeName', null, $context)), 
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2));
        $image = Theme::getInstance()->getImagePath($context, 'Logo/48');
        
        $upgrader = \Chamilo\Configuration\Package\Action\Upgrader::factory($context);
        
        if (! $upgrader->run())
        {
            $this->add_message($upgrader->retrieve_message());
            return $this->action_failed($title, $image, Translation::get('UpgradeFailed'));
        }
        else
        {
            $this->add_message($upgrader->retrieve_message());
            $this->add_message(Translation::get('PackageUpgraded'), self::TYPE_CONFIRM);
            $this->add_additional_packages($upgrader->get_additional_packages());
            return $this->action_successful($title, $image);
        }
    }

    /**
     * Returns the additional packages
     * 
     * @return multitype:string
     */
    public function get_additional_packages()
    {
        return $this->additional_packages;
    }

    /**
     * Sets the additional packages
     * 
     * @param multitype:string
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
     * @param multitype:string
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

    public function get_upgrader()
    {
        return $this->upgrader;
    }
}
