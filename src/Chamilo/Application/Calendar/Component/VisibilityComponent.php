<?php
namespace Chamilo\Application\Calendar\Component;

use Chamilo\Application\Calendar\Manager;
use Chamilo\Application\Calendar\Service\VisibilityService;
use Chamilo\Libraries\Calendar\Architecture\Interfaces\VisibilityServiceInterface;
use Chamilo\Libraries\Calendar\Architecture\Traits\VisibilityComponentTrait;

class VisibilityComponent extends Manager
{
    use VisibilityComponentTrait;

    public const CONTEXT = Manager::CONTEXT;

    public function getVisibilityService(): VisibilityServiceInterface
    {
        return $this->getService(VisibilityService::class);
    }
}
