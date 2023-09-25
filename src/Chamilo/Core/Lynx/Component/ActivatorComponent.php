<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Package\Action;

class ActivatorComponent extends ActionComponent
{
    protected function getBreadcrumbNameVariable(): string
    {
        return 'ActivatingPackage';
    }

    protected function getCurrentPackageAction(): Action
    {
        return $this->getPackageActionFactory()->getPackageActivator($this->getCurrentContext());
    }
}
