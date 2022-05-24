<?php
namespace Chamilo\Core\Repository\Table\Link;

use ArrayIterator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

class LinkTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        if ($this->get_table()->getType() == LinkTable::TYPE_PUBLICATIONS)
        {
            return $this->get_component()->getContentObject()->count_publications($this->get_component()->get_user());
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_PARENTS)
        {
            return $this->get_component()->getContentObject()->count_parents();
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_CHILDREN)
        {
            return $this->get_component()->getContentObject()->count_children();
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_ATTACHED_TO)
        {
            return $this->get_component()->getContentObject()->count_attachers();
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_ATTACHES)
        {
            return $this->get_component()->getContentObject()->count_attachments();
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_INCLUDED_IN)
        {
            return $this->get_component()->getContentObject()->count_includers();
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_INCLUDES)
        {
            return $this->get_component()->getContentObject()->count_includes();
        }

        return 0;
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        if ($this->get_table()->getType() == LinkTable::TYPE_PUBLICATIONS)
        {
            return $this->get_component()->getContentObject()->get_publications($count, $offset, $orderBy);
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_PARENTS)
        {
            return $this->get_component()->getContentObject()->get_parents($orderBy, $offset, $count);
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_CHILDREN)
        {
            return $this->get_component()->getContentObject()->get_children($orderBy, $offset, $count);
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_ATTACHED_TO)
        {
            return new ArrayIterator(
                $this->get_component()->getContentObject()->get_attachers($orderBy, $offset, $count)
            );
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_ATTACHES)
        {
            return new ArrayIterator(
                $this->get_component()->getContentObject()->get_attachments(
                    ContentObject::ATTACHMENT_NORMAL, $orderBy, $offset, $count
                )
            );
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_INCLUDED_IN)
        {
            return new ArrayIterator(
                $this->get_component()->getContentObject()->get_includers($orderBy, $offset, $count)
            );
        }

        if ($this->get_table()->getType() == LinkTable::TYPE_INCLUDES)
        {
            return new ArrayIterator(
                $this->get_component()->getContentObject()->get_includes($orderBy, $offset, $count)
            );
        }
    }
}
