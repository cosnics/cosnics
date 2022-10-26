<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable;

class LinkTable extends DataClassTable
{
    const TYPE_PUBLICATIONS = 1;
    const TYPE_PARENTS = 2;
    const TYPE_CHILDREN = 3;
    const TYPE_ATTACHED_TO = 4;
    const TYPE_ATTACHES = 5;
    const TYPE_INCLUDED_IN = 6;
    const TYPE_INCLUDES = 7;

    private $type;

    public function __construct($component, $type)
    {
        $this->type = $type;
        parent::__construct($component);
    }

    /**
     * @deprecated Use LinkTable::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    public function getType()
    {
        return $this->type;
    }
}
