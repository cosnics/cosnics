<?php
namespace Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity;

use Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity\Group\EntityTableColumnModel;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\TableResultPosition;

/**
 * @package Chamilo\Application\Weblcms\Bridge\Assignment\Table\Entity
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class GroupEntityTableRenderer extends EntityTableRenderer
{
    public const PROPERTY_GROUP_MEMBERS = 'group_members';

    protected function getGroupMembers($group): string
    {
        $entityId = $group[Entry::PROPERTY_ENTITY_ID];
        $users = $this->getEntityService()->getUsersForEntity($entityId);

        if (count($users) == 0)
        {
            return '';
        }

        $html = [];
        $html[] = '<select style="width:180px">';

        foreach ($users as $user)
        {
            $html[] = '<option>' . $user->get_fullname() . '</option>';
        }

        $html[] = '</select>';

        return implode(PHP_EOL, $html);
    }

    protected function initializeColumns()
    {
        parent::initializeColumns();

        $this->addColumn(
            new StaticTableColumn(
                self::PROPERTY_GROUP_MEMBERS, $this->getTranslator()->trans('GroupMembers', [], Manager::CONTEXT)
            ), 1
        );
    }

    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $entity): string
    {
        if ($column->get_name() == EntityTableColumnModel::PROPERTY_GROUP_MEMBERS)
        {
            return $this->getGroupMembers($entity);
        }

        return parent::renderCell($column, $resultPosition, $entity);
    }

}
