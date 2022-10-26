<?php
namespace Chamilo\Core\Repository\Publication\Table;

use Chamilo\Core\Repository\Publication\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable;

class PublicationTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager::PARAM_PUBLICATION_ID;
    const DEFAULT_MAXIMUM_NUMBER_OF_RESULTS = 500;
}
