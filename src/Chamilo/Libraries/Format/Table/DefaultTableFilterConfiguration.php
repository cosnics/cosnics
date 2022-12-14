<?php

namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Format\Table\Interfaces\TableFilterConfigurationInterface;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

class DefaultTableFilterConfiguration implements TableFilterConfigurationInterface
{
    private ?Condition $condition;

    public function __construct(?Condition $condition)
    {
        $this->condition = $condition;
    }

    public function getCondition(): ?Condition
    {
        return $this->condition;
    }

    public function setCondition(?Condition $condition): DefaultTableFilterConfiguration
    {
        $this->condition = $condition;

        return $this;
    }

}