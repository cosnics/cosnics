<?php
namespace Chamilo\Core\Repository\Implementation\Scribd\Table\ExternalObject;

use Chamilo\Core\Repository\Implementation\Scribd\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;
use Chamilo\Libraries\Format\Table\Interfaces\TablePageSelectionProhibition;

class ExternalObjectTable extends DataClassTable implements TablePageSelectionProhibition
{
    const TABLE_IDENTIFIER = Manager :: PARAM_EXTERNAL_REPOSITORY_ID;
}
