<?php
namespace Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait;

use Chamilo\Libraries\Storage\Architecture\Domain\Query\Condition\AndCondition;
use Chamilo\Libraries\UserInterface\ButtonToolBar\Service\ButtonToolBarRenderer;

/**
 * @package Chamilo\Libraries\UserInterface\ButtonToolBar\Architecture\Trait
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait ButtonToolBarSearchFormTrait
{
    protected readonly ButtonToolBarRenderer $buttonToolBarRenderer;

    public function getButtonToolBarSearchCondition(?string $type = null): ?AndCondition
    {
        return $this->buttonToolBarRenderer->getConditions($this->getButtonToolBarSearchProperties($type));
    }

    /**
     * @return \Chamilo\Libraries\Storage\Architecture\Domain\Query\ConditionVariable\PropertyConditionVariable[]
     */
    abstract public function getButtonToolBarSearchProperties(?string $type = null): array;
}