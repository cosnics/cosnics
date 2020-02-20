<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\ResultSet\ArrayResultSet;

class LinkTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        if ($this->get_table()->get_type() == LinkTable::TYPE_PUBLICATIONS)
        {
            return $this->get_component()->getContentObject()->get_publications($count, $offset, $order_property);
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_PARENTS)
        {
            return $this->get_component()->getContentObject()->get_parents($order_property, $offset, $count);
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_CHILDREN)
        {
            return $this->get_component()->getContentObject()->get_children($order_property, $offset, $count);
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_ATTACHED_TO)
        {
            return new ArrayResultSet(
                $this->get_component()->getContentObject()->get_attachers($order_property, $offset, $count)
            );
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_ATTACHES)
        {
            return new ArrayResultSet(
                $this->get_component()->getContentObject()->get_attachments(
                    ContentObject::ATTACHMENT_NORMAL, $order_property, $offset, $count
                )
            );
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_INCLUDED_IN)
        {
            return new ArrayResultSet(
                $this->get_component()->getContentObject()->get_includers($order_property, $offset, $count)
            );
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_INCLUDES)
        {
            return new ArrayResultSet(
                $this->get_component()->getContentObject()->get_includes($order_property, $offset, $count)
            );
        }
    }

    public function count_data($condition)
    {
        if ($this->get_table()->get_type() == LinkTable::TYPE_PUBLICATIONS)
        {
            return $this->get_component()->getContentObject()->count_publications($this->get_component()->get_user());
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_PARENTS)
        {
            return $this->get_component()->getContentObject()->count_parents();
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_CHILDREN)
        {
            return $this->get_component()->getContentObject()->count_children();
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_ATTACHED_TO)
        {
            return $this->get_component()->getContentObject()->count_attachers();
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_ATTACHES)
        {
            return $this->get_component()->getContentObject()->count_attachments();
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_INCLUDED_IN)
        {
            return $this->get_component()->getContentObject()->count_includers();
        }

        if ($this->get_table()->get_type() == LinkTable::TYPE_INCLUDES)
        {
            return $this->get_component()->getContentObject()->count_includes();
        }

        return 0;
    }
}
