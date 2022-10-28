<?php
namespace Chamilo\Core\Repository\Implementation\Vimeo\Table\ExternalObject;

use Chamilo\Core\Repository\Implementation\Vimeo\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;

class ExternalObjectTable extends DataClassListTableRenderer
{
    const TABLE_IDENTIFIER = Manager::PARAM_EXTERNAL_REPOSITORY_ID;
}
