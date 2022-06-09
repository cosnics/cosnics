<?php
namespace Chamilo\Core\Lynx\Action;

use Chamilo\Configuration\Configuration;
use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 * Package installation
 *
 * @author Hans De Bisschop - Erasmus Hogeschool Brussel
 * @author Magali Gillard - Erasmus Hogeschool Brussel
 * @author Sven Vanpoucke - Hogeschool Gent - Cleanup, code refactoring and bugfixes, comments
 */
class PackageInstaller extends AbstractAction
{

    /**
     * @var string[] $additionalPackages
     */
    private array $additionalPackages = [];

    public function run(): bool
    {
        set_time_limit(0);

        if ($this->initialize() && $this->process())
        {
            $title = Translation::get(
                'Finished', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('laugh-beam', ['fa-lg'], null, 'fas');

            return $this->wasSuccessful($title, $image, Translation::get('PackageCompletelyInstalled'));
        }
        else
        {
            $title = Translation::get(
                'Failed', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('sad-cry', ['fa-lg'], null, 'fas');

            return $this->hasFailed($title, $image, Translation::get('PackageInstallFailed'));
        }
    }

    public function addAdditionalPackage(string $context)
    {
        $this->additionalPackages[] = $context;
    }

    public function addAdditionalPackages(array $additional_packages)
    {
        foreach ($additional_packages as $additional_package)
        {
            $this->addAdditionalPackage($additional_package);
        }
    }

    /**
     * @return string[]
     */
    public static function getAdditionalPackages(): array
    {
        return [];
    }

    public function getNextAdditionalPackage(): string
    {
        return array_shift($this->additionalPackages);
    }

    /**
     * Initializes the package installer
     */
    public function initialize(): bool
    {
        $title = Translation::get(
            'Initialization', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
        );
        $image = new FontAwesomeGlyph('truck-loading', ['fa-lg'], null, 'fas');

        if (!$this->getPackage() instanceof Package)
        {
            return $this->hasFailed($title, $image, Translation::get('PackageAttributesNotFound'));
        }
        else
        {
            $this->add_message(Translation::get('PackageAttributesFound'));
        }

        // Check registration
        if ($this->isPackageRegistered())
        {
            return $this->hasFailed($title, $image, Translation::get('PackageIsAlreadyRegistered'));
        }
        else
        {
            $this->add_message(Translation::get('PackageNotYetRegistered'));
        }

        return $this->wasSuccessful($title, $image, Translation::get('PackageInstallInitialized'));
    }

    private function installAdditionalPackage(string $context): bool
    {
        $title = Translation::get(
            'Installation', ['PACKAGE' => Translation::get('TypeName', null, $context)],
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
        );
        $image = new NamespaceIdentGlyph(
            $context, true, false, false, IdentGlyph::SIZE_BIG
        );

        $installer = Installer::factory($context, []);

        if (!$installer->run())
        {
            $this->add_message($installer->retrieve_message());

            return $this->hasFailed($title, $image, Translation::get('InitializationFailed'));
        }
        else
        {
            $this->add_message($installer->retrieve_message());
            $this->wasSuccessful($title, $image);
        }

        $this->addAdditionalPackages($installer->get_additional_packages());

        return true;
    }

    public function isPackageRegistered(): bool
    {
        return Configuration::is_registered($this->getPackage()->get_context());
    }

    public function process(): bool
    {
        $title = Translation::get(
            'Installation', ['PACKAGE' => Translation::get('TypeName', null, $this->getPackage()->get_context())],
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
        );
        $image = new FontAwesomeGlyph('box', ['fa-lg'], null, 'fas');

        $installer = Installer::factory(
            $this->getPackage()->get_context(), []
        );
        if (!$installer->run())
        {
            $this->add_message($installer->retrieve_message());

            return $this->hasFailed($title, $image, Translation::get('InitializationFailed'));
        }
        else
        {
            $this->add_message($installer->retrieve_message());
            $this->wasSuccessful($title, $image);
        }

        $this->addAdditionalPackages($installer->get_additional_packages());

        $title = Translation::get(
            'AdditionalPackages', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
        );
        $image = new FontAwesomeGlyph('link', ['fa-lg'], null, 'fas');

        while (($additional_package = $this->getNextAdditionalPackage()) != null)
        {
            if (!$this->installAdditionalPackage($additional_package))
            {
                return $this->hasFailed($title, $image, Translation::get('AdditionalPackagesFailed'));
            }
        }

        return true;
    }
}
