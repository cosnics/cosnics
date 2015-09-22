<?php
namespace Chamilo\Core\Repository\Publication\Table;

use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

class PublicationTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager :: PARAM_PUBLICATION_ID;
    const DEFAULT_ROW_COUNT = 500;
}
