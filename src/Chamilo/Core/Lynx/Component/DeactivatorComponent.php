<?php
namespace Chamilo\Core\Lynx\Component;

use Chamilo\Configuration\Package\Action;

class DeactivatorComponent extends ActionComponent
{
    protected function getBreadcrumbNameVariable(): string
    {
        return 'DeactivatingPackage';
    }

    protected function getCurrentPackageAction(): Action
    {
        return $this->getPackageActionFactory()->getPackageDeactivator($this->getCurrentContext());
    }
}
