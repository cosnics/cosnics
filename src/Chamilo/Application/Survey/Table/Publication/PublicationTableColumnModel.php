<?php
namespace Chamilo\Application\Survey\Table\Publication;

use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

/**
 *
 * @package Chamilo\Application\Survey\Table\Publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PublicationTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\TableColumnModel::initialize_columns()
     */
    public function initialize_columns()
    {
        $this->add_column(new DataClassPropertyTableColumn(Publication::class_name(), Publication::PROPERTY_TITLE));
        $this->add_column(
            new DataClassPropertyTableColumn(Publication::class_name(), Publication::PROPERTY_PUBLISHER_ID, null, false));
        $this->add_column(
            new DataClassPropertyTableColumn(Publication::class_name(), Publication::PROPERTY_PUBLISHED));
    }
}
