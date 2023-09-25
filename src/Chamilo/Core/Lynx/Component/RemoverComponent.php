<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Package\Action;
use Chamilo\Core\Lynx\Manager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use OutOfBoundsException;

class RemoverComponent extends AdditionalActionComponent
{
    protected function getBreadcrumbNameVariable(): string
    {
        return 'RemovingPackage';
    }

    protected function getCurrentPackageAction(): Action
    {
        return $this->getPackageActionFactory()->getPackageRemover($this->getCurrentContext());
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    protected function runPackageAction(Action $packageAction): string
    {
        $translator = $this->getTranslator();

        $initializationGlyph = new FontAwesomeGlyph('truck-loading', ['fa-lg'], null, 'fas');
        $initilizationTitle = $translator->trans('Failed', [], Manager::CONTEXT);

        $html = [];

        try
        {
            $package = $this->getCurrentPackage();

            if (!$this->getRegistrationConsulter()->isContextRegistered($package->get_context()))
            {
                $message = $translator->trans('PackageIsNotInstalled', [], Manager::CONTEXT);

                return $this->renderPanel('panel-danger', $initializationGlyph, $initilizationTitle, $message);
            }
            else
            {
                $this->addAdditionalPackageContexts($package->getAdditional());

                while (($additionalPackageContext = $this->getNextAdditionalPackageContext()) != null)
                {
                    try
                    {
                        $additionalPackage = $this->getPackageFactory()->getPackage($additionalPackageContext);
                        $additionalPackageAction =
                            $this->getPackageActionFactory()->getPackageInstaller($additionalPackageContext);

                        $html[] = parent::runPackageAction($additionalPackageAction);

                        $this->addAdditionalPackageContexts($additionalPackage->getAdditional());
                    }
                    catch (OutOfBoundsException)
                    {
                        $contextTitle = $translator->trans(
                            'Removal', ['PACKAGE' => $translator->trans('TypeName', [], $additionalPackageContext)],
                            Manager::CONTEXT
                        );

                        $contextGlyph = new NamespaceIdentGlyph(
                            $additionalPackageContext, true, false, false, IdentGlyph::SIZE_BIG
                        );

                        $html[] = $this->renderPanel('panel-danger', $contextGlyph, $initilizationTitle, $contextTitle);

                        return implode(PHP_EOL, $html);
                    }
                }

                $html[] = parent::runPackageAction($packageAction);
            }
        }
        catch (OutOfBoundsException)
        {
            $message = $translator->trans('PackageAttributesNotFound', [], Manager::CONTEXT);

            return $this->renderPanel('panel-danger', $initializationGlyph, $initilizationTitle, $message);
        }

        return implode(PHP_EOL, $html);
    }
}
