<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Table\ExternalObject;

use Chamilo\Core\Repository\Implementation\GoogleDocs\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;

class ExternalObjectTable extends DataClassListTableRenderer
{
    const TABLE_IDENTIFIER = Manager::PARAM_EXTERNAL_REPOSITORY_ID;
}
