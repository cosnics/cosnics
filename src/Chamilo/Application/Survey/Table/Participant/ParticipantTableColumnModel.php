<?php
namespace Chamilo\Application\Survey\Table\Participant;

use Chamilo\Application\Survey\Storage\DataClass\Participant;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableColumnModel;
use Chamilo\Libraries\Format\Table\Interfaces\TableColumnModelActionsColumnSupport;

class ParticipantTableColumnModel extends DataClassTableColumnModel implements TableColumnModelActionsColumnSupport
{

    public function initialize_columns()
    {
        $this->add_column(new StaticTableColumn(User::PROPERTY_USERNAME));
        $this->add_column(new DataClassPropertyTableColumn(Participant::class_name(), Participant::PROPERTY_STATUS));
        $this->add_column(
            new DataClassPropertyTableColumn(Participant::class_name(), Participant::PROPERTY_PROGRESS));
        $this->add_column(
            new DataClassPropertyTableColumn(Participant::class_name(), Participant::PROPERTY_START_TIME));
        $this->add_column(
            new DataClassPropertyTableColumn(Participant::class_name(), Participant::PROPERTY_TOTAL_TIME));
    }
}
?>