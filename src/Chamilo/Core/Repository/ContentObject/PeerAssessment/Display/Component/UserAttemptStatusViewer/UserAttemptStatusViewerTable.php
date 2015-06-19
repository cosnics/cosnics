<?php
namespace Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Component\UserAttemptStatusViewer;

use Chamilo\Core\Repository\ContentObject\PeerAssessment\Display\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

class UserAttemptStatusViewerTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_ATTEMPT;
}
