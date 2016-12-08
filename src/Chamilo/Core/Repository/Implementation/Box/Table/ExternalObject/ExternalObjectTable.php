<?php
namespace Chamilo\Core\Repository\Implementation\Box\Table\ExternalObject;

use Chamilo\Core\Repository\Implementation\Box\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

class ExternalObjectTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager::PARAM_EXTERNAL_REPOSITORY_ID;
}
