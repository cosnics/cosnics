<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Application\Calendar\Architecture\Enum\ActionEnum;
use Chamilo\Application\Calendar\Storage\Repository\VisibilityRepository;
use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Application\Calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const string CONTEXT = __NAMESPACE__;

    public function getApplicationAction(): string
    {
        return ActionEnum::getActionValue(static::class);
    }

    public function getApplicationContext(): string
    {
        return self::CONTEXT;
    }

    public function getDefaultApplicationAction(): string
    {
        return ActionEnum::BROWSE->value;
    }

    public function getVisibilityRepository(): VisibilityRepository
    {
        return $this->getService(VisibilityRepository::class);
    }
}
