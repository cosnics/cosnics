<?php
namespace Chamilo\Core\Rights\Editor\Table\LocationEntity;

use Chamilo\Core\Rights\Editor\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

abstract class LocationEntityTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager::PARAM_ENTITY_ID;

    private $type;

    public function __construct($component, $type)
    {
        parent::__construct($component);
        $this->type = $type;
    }

    public function get_type()
    {
        return $this->type;
    }
}
