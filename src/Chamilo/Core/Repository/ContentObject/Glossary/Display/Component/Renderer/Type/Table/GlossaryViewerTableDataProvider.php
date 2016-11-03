<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type\Table;

use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class GlossaryViewerTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->get_component()->get_objects($offset, $count, $order_property);
    }

    public function count_data($condition)
    {
        return $this->get_component()->count_objects();
    }
}
