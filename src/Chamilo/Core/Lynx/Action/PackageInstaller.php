<?php
namespace Chamilo\Core\Lynx\Action;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Core\Lynx\Action;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
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

    private $additional_packages = [];

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
                'Finished', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('laugh-beam', array('fa-lg'), null, 'fas');

            return $this->action_successful($title, $image, Translation::get('PackageCompletelyInstalled'));
        }
        else
        {
            $title = Translation::get(
                'Failed', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('sad-cry', array('fa-lg'), null, 'fas');

            return $this->action_failed($title, $image, Translation::get('PackageInstallFailed'));
        }
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
     * Returns the additional packages
     *
     * @return string[]
     */
    public static function get_additional_packages()
    {
        return $this->additional_packages;
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

    /**
     * Initializes the package installer
     *
     * @return boolean
     */
    public function initialize()
    {
        $title = Translation::get(
            'Initialization', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
        );
        $image = new FontAwesomeGlyph('truck-loading', array('fa-lg'), null, 'fas');

        if (!$this->get_package() instanceof Package)
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
     * Installs an additional package
     *
     */
    private function install_additional_package($context)
    {
        $title = Translation::get(
            'Installation', array('PACKAGE' => Translation::get('TypeName', null, $context)),
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
        );
        $image = new NamespaceIdentGlyph(
            $context, true, false, false, IdentGlyph::SIZE_BIG
        );

        $installer = Installer::factory($context, []);

        if (!$installer->run())
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
            'Installation', array('PACKAGE' => Translation::get('TypeName', null, $this->get_package()->get_context())),
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
        );
        $image = new FontAwesomeGlyph('box', array('fa-lg'), null, 'fas');

        $installer = Installer::factory(
            $this->get_package()->get_context(), []
        );
        if (!$installer->run())
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
            'AdditionalPackages', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
        );
        $image = new FontAwesomeGlyph('link', array('fa-lg'), null, 'fas');

        while (($additional_package = $this->get_next_additional_package()) != null)
        {
            if (!$this->install_additional_package($additional_package))
            {
                return $this->action_failed($title, $image, Translation::get('AdditionalPackagesFailed'));
            }
        }

        return true;
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
}
