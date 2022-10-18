<?php
namespace Chamilo\Core\Metadata\Provider\Table\ProviderLink;

use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package Chamilo\Core\Metadata\Provider\Table\ProviderLink
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ProviderLinkTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{
    const PROPERTY_TYPE = 'type';
    const PROPERTY_NAME = 'name';
    const PROPERTY_SCHEMA = 'schema';
    const PROPERTY_ELEMENT = 'element';

    /**
     * Initializes the columns for the table
     */
    public function initializeColumns()
    {
        $this->addColumn(new StaticTableColumn(self::PROPERTY_TYPE));
        $this->addColumn(new StaticTableColumn(self::PROPERTY_NAME));
        $this->addColumn(new StaticTableColumn(self::PROPERTY_SCHEMA));
        $this->addColumn(new StaticTableColumn(self::PROPERTY_ELEMENT));
    }
}