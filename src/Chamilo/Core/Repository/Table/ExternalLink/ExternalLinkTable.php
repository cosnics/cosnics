<?php
namespace Chamilo\Core\Repository\Table\ExternalLink;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;

class ExternalLinkTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager::PARAM_EXTERNAL_INSTANCE;
}
