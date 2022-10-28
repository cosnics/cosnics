<?php
namespace Chamilo\Core\Repository\Table\ExternalLink;

use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;

class ExternalLinkTable extends DataClassListTableRenderer
{
    const TABLE_IDENTIFIER = Manager::PARAM_EXTERNAL_INSTANCE;
}
