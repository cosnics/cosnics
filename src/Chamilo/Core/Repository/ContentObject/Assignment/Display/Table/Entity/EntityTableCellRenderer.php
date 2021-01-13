<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EntityTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $entity)
    {
        if ($column instanceof ActionsTableColumn && $this instanceof TableCellRendererActionsColumnSupport)
        {
            return $this->get_actions($entity);
        }

        if (in_array($column->get_name(), $this->getEntityTableParameters()->getEntityProperties()))
        {
            if ($this->canViewEntity($entity))
            {
                return '<a href="' . $this->getEntityUrl($entity) . '">' . $entity[$column->get_name()] . '</a>';
            }
            else
            {
                return $entity[$column->get_name()];
            }
        }

        switch ($column->get_name())
        {
            case EntityTableColumnModel::PROPERTY_MEMBERS:
                return $this->getGroupMembers($entity);
            case EntityTableColumnModel::PROPERTY_FIRST_ENTRY_DATE :
                $entryDate = $entity[EntityTableColumnModel::PROPERTY_FIRST_ENTRY_DATE];
                if (is_null($entryDate))
                {
                    return '-';
                }

                return $this->formatDate($entryDate);
            case EntityTableColumnModel::PROPERTY_LAST_ENTRY_DATE :
                $entryDate = $entity[EntityTableColumnModel::PROPERTY_LAST_ENTRY_DATE];
                if (is_null($entryDate))
                {
                    return '-';
                }

                return $this->formatDate($entryDate);
            case EntityTableColumnModel::PROPERTY_FEEDBACK_COUNT :
                return $this->getFeedbackServiceBridge()->countFeedbackByEntityTypeAndEntityId(
                    $this->getAssignmentServiceBridge()->getCurrentEntityType(),
                    $entity[Entry::PROPERTY_ENTITY_ID]
                );
            case EntityTableColumnModel::PROPERTY_LAST_SCORE:
                $lastScore = $this->getAssignmentServiceBridge()->getLastScoreForEntityTypeAndId(
                    $entity[Entry::PROPERTY_ENTITY_TYPE], $entity[Entry::PROPERTY_ENTITY_ID]
                );

                if (is_null($lastScore))
                {
                    return null;
                }

                return '<div class="text-right">' . $lastScore . '%</div>';
                break;
        }

        return parent::render_cell($column, $entity);
    }

    public function render_id_cell($row)
    {
        return $row[Entry::PROPERTY_ENTITY_ID];
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport::get_actions()
     */
    public function get_actions($entity)
    {
        $toolbar = new Toolbar();

        $entityId = $entity[Entry::PROPERTY_ENTITY_ID];
        $isEntity = $this->isEntity($entityId, $this->getEntityTableParameters()->getUser()->getId());

        if ($this->canViewEntity($entity))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('ViewLastEntry'),
                    Theme::getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_ENTRY,
                            Manager::PARAM_ENTITY_TYPE => $entity[Entry::PROPERTY_ENTITY_TYPE],
                            Manager::PARAM_ENTITY_ID => $entity[Entry::PROPERTY_ENTITY_ID]
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        $hasEntries = $entity[EntityTableColumnModel::PROPERTY_ENTRY_COUNT] > 0;

        if ($this->getRightsService()->canUserDownloadEntriesFromEntity(
                $this->getEntityTableParameters()->getUser(), $this->getEntityTableParameters()->getAssignment(),
                $entity[Entry::PROPERTY_ENTITY_TYPE], $entity[Entry::PROPERTY_ENTITY_ID]
            ) &&
            $hasEntries)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('DownloadAll'),
                    Theme::getInstance()->getCommonImagePath('Action/Download'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DOWNLOAD,
                            Manager::PARAM_ENTITY_TYPE => $entity[Entry::PROPERTY_ENTITY_TYPE],
                            Manager::PARAM_ENTITY_ID => $entity[Entry::PROPERTY_ENTITY_ID]
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($isEntity)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('AddNewEntry'),
                    Theme::getInstance()->getCommonImagePath('Action/Add'),
                    $this->get_component()->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_CREATE,
                            Manager::PARAM_ENTITY_TYPE => $entity[Entry::PROPERTY_ENTITY_TYPE],
                            Manager::PARAM_ENTITY_ID => $entity[Entry::PROPERTY_ENTITY_ID]
                        )
                    ),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        return $toolbar->as_html();
    }

    /**
     * Formats a date.
     *
     * @param int $date the date to be formatted.
     *
     * @return string
     */
    protected function formatDate($date)
    {
        $formatted_date = DatetimeUtilities::format_locale_date(
            Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
            $date
        );

        if ($this->getAssignmentServiceBridge()->isDateAfterAssignmentEndTime($date))
        {
            return $formatted_date . '<br /><div class="badge" style="background-color: red;">' .
                Translation::getInstance()->getTranslation('LateSubmission') . '</div>';
        }

        return $formatted_date;
    }

    protected function canViewEntity($entity)
    {
        /** @var Assignment $assignment */
        $hasEntries = $entity[EntityTableColumnModel::PROPERTY_ENTRY_COUNT] > 0;

        return $this->getRightsService()->canUserViewEntity(
                $this->getEntityTableParameters()->getUser(),
                $this->getEntityTableParameters()->getAssignment(),
                $entity[Entry::PROPERTY_ENTITY_TYPE],
                $entity[Entry::PROPERTY_ENTITY_ID]
            )
            && $hasEntries;
    }

    /**
     *
     * @param array $group
     *
     * @return string
     */
    protected function getGroupMembers($group)
    {
        $entityId = $group[Entry::PROPERTY_ENTITY_ID];
        $users = $this->getAssignmentServiceBridge()->getUsersForEntity(
            $this->getEntityTableParameters()->getEntityType(), $entityId
        );

        if (count($users) == 0)
        {
            return null;
        }

        $html = array();
        $html[] = '<select style="width:180px">';

        foreach ($users as $user)
        {
            $html[] = '<option>' . $user->get_fullname() . '</option>';
        }

        $html[] = '</select>';

        return implode(PHP_EOL, $html);
    }

    protected function getEntityUrl($entity)
    {
        return $this->get_component()->get_url(
            array(
                Manager::PARAM_ACTION => Manager::ACTION_ENTRY,
                Manager::PARAM_ENTITY_TYPE => $entity[Entry::PROPERTY_ENTITY_TYPE],
                Manager::PARAM_ENTITY_ID => $entity[Entry::PROPERTY_ENTITY_ID]
            )
        );
    }

    protected function isEntity($entityId, $userId)
    {
        $user = new User();
        $user->setId($userId);

        return $this->getAssignmentServiceBridge()->isUserPartOfEntity(
            $user, $this->getEntityTableParameters()->getEntityType(), $entityId
        );
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTableParameters
     */
    protected function getEntityTableParameters()
    {
        return $this->getTable()->getEntityTableParameters();
    }

    /**
     * @return \Chamilo\Libraries\Format\Table\Table | \Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity\EntityTable
     */
    protected function getTable()
    {
        return $this->get_table();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\RightsService
     */
    protected function getRightsService()
    {
        return $this->getEntityTableParameters()->getRightService();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\AssignmentServiceBridgeInterface
     */
    protected function getAssignmentServiceBridge()
    {
        return $this->getEntityTableParameters()->getAssignmentServiceBridge();
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Interfaces\FeedbackServiceBridgeInterface
     */
    protected function getFeedbackServiceBridge()
    {
        return $this->getEntityTableParameters()->getFeedbackServiceBridge();
    }
}
