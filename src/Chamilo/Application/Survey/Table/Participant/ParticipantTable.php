<?php
namespace Chamilo\Application\Survey\Table\Participant;

use Chamilo\Application\Survey\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

class ParticipantTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_PARTICIPANT_ID;
}
?>