<?php

namespace Chamilo\Libraries\Format\Table;

use Chamilo\Libraries\Storage\Query\Condition\Condition;
use TableFilterConfigurationInterface;

class TableFilterConfiguration implements TableFilterConfigurationInterface
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

    public function setCondition(?Condition $condition): TableFilterConfiguration
    {
        $this->condition = $condition;

        return $this;
    }

}