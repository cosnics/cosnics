<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type\Table;

use Chamilo\Core\Repository\ContentObject\Glossary\Display\Manager;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTable;

class GlossaryViewerTable extends DataClassTable
{
    const TABLE_IDENTIFIER = Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID;
}
