<?php
namespace Chamilo\Core\Lynx\Action;

use Chamilo\Configuration\Package\Action\Remover;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 * Package installation
 *
 * @author  Hans De Bisschop - Erasmus Hogeschool Brussel
 * @author  Magali Gillard - Erasmus Hogeschool Brussel
 * @author  Sven Vanpoucke - Hogeschool Gent - Cleanup, code refactoring and bugfixes, comments
 * @package admin.lib.package_installer
 */
class PackageRemover extends AbstractAction
{

    public function run(): bool
    {
        set_time_limit(0);

        if ($this->initialize() && $this->process())
        {
            $title = Translation::get(
                'Finished', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('laugh-beam', ['fa-lg'], null, 'fas');

            return $this->wasSuccessful($title, $image, Translation::get('PackageCompletelyRemoved'));
        }
        else
        {
            $title = Translation::get(
                'Failed', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
            );
            $image = new FontAwesomeGlyph('sad-cry', ['fa-lg'], null, 'fas');

            return $this->hasFailed($title, $image, Translation::get('PackageRemoveFailed'));
        }
    }

    public function initialize(): bool
    {
        $title = Translation::get(
            'Initialization', null, ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
        );
        $image = new FontAwesomeGlyph('compass', ['fa-lg'], null, 'fas');

        if (!$this->getPackage() instanceof Package)
        {
            return $this->hasFailed($title, $image, Translation::get('PackageAttributesNotFound'));
        }
        else
        {
            $this->add_message(Translation::get('PackageAttributesFound'));
        }

        // Check registration
        if (!$this->isPackageRegistered())
        {
            return $this->hasFailed($title, $image, Translation::get('PackageIsNotInstalled'));
        }
        else
        {
            $this->add_message(Translation::get('PackageNotYetRemoved'));
        }

        return $this->wasSuccessful($title, $image, Translation::get('PackageRemoveInitialized'));
    }

    public function isPackageRegistered(): bool
    {
        return $this->getRegistrationConsulter()->isContextRegistered($this->getPackage()->get_context());
    }

    public function process(): bool
    {
        $this->processAdditionalPackages($this->getPackage()->get_context());

        return true;
    }

    public function processAdditionalPackages($context)
    {
        $remover = Remover::factory($context);

        $additionalPackages = $remover->get_additional_packages();

        foreach ($additionalPackages as $additionalPackage)
        {
            $this->processAdditionalPackages($additionalPackage);
        }

        $title = Translation::get(
            'Removal', ['PACKAGE' => Translation::get('TypeName', null, $this->getPackage()->get_context())],
            ClassnameUtilities::getInstance()->getNamespaceParent(__NAMESPACE__, 2)
        );
        $image = new FontAwesomeGlyph('trash-alt', ['fa-lg'], null, 'fas');

        if (!$remover->run())
        {
            $this->add_message($remover->retrieve_message());

            return $this->hasFailed($title, $image, Translation::get('InitializationFailed'));
        }
        else
        {
            $this->add_message($remover->retrieve_message());
            $this->wasSuccessful($title, $image);
        }
    }
}
