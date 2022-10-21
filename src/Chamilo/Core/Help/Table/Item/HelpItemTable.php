<?php
namespace Chamilo\Core\Help\Table\Item;

use Chamilo\Core\Help\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

class HelpItemTable extends DataClassTable
{
    public const TABLE_IDENTIFIER = Manager::PARAM_HELP_ITEM;
}
