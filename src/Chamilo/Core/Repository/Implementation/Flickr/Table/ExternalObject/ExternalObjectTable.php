<?php
namespace Chamilo\Core\Repository\Implementation\Flickr\Table\ExternalObject;

use Chamilo\Core\Repository\Implementation\Flickr\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;

class ExternalObjectTable extends DataClassListTableRenderer
{
    const TABLE_IDENTIFIER = Manager::PARAM_EXTERNAL_REPOSITORY_ID;
}
