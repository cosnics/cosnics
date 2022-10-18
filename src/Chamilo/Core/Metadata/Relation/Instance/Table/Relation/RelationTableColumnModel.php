<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Table\Relation;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package Chamilo\Core\Metadata\Relation\Instance\Table\Relation
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class RelationTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_SOURCE = 'source';
    const PROPERTY_RELATION = 'relation';
    const PROPERTY_TARGET = 'target';

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $this->addColumn(new StaticTableColumn(self::PROPERTY_SOURCE));
        $this->addColumn(new StaticTableColumn(self::PROPERTY_RELATION));
        $this->addColumn(new StaticTableColumn(self::PROPERTY_TARGET));
    }
}