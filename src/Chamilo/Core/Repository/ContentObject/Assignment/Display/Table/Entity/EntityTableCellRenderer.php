<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Storage\DataClass\Entry;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Table\Column\ActionsTableColumn;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Table\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class EntityTableCellRenderer extends RecordTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    public function render_cell($column, $entity)
    {
        if ($column instanceof ActionsTableColumn && $this instanceof TableCellRendererActionsColumnSupport)
        {
            return $this->get_actions($entity);
        }

        switch ($column->get_name())
        {
            case EntityTableColumnModel :: PROPERTY_FIRST_ENTRY_DATE :
                if (is_null($entity[EntityTableColumnModel :: PROPERTY_FIRST_ENTRY_DATE]))
                {
                    return '-';
                }
                return $this->formatDate($entity[EntityTableColumnModel :: PROPERTY_FIRST_ENTRY_DATE]);
                break;
            case EntityTableColumnModel :: PROPERTY_LAST_ENTRY_DATE :
                if (is_null($entity[EntityTableColumnModel :: PROPERTY_LAST_ENTRY_DATE]))
                {
                    return '-';
                }
                return $this->formatDate($entity[EntityTableColumnModel :: PROPERTY_LAST_ENTRY_DATE]);
                break;
            case EntityTableColumnModel :: PROPERTY_FEEDBACK_COUNT :
                return $this->get_table()->getAssignmentDataProvider()->countFeedbackByEntityTypeAndEntityId(
                    $this->get_table()->getAssignmentDataProvider()->getCurrentEntityType(),
                    $entity[Entry :: PROPERTY_ENTITY_ID]);
                break;
        }

        return parent :: render_cell($column, $entity);
    }

    public function render_id_cell($row)
    {
        return $row[Entry :: PROPERTY_ENTITY_ID];
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport::get_actions()
     */
    public function get_actions($entity)
    {
        $toolbar = new Toolbar();

        $entityId = $entity[Entry :: PROPERTY_ENTITY_ID];
        $isEntity = $this->isEntity($entityId, $this->get_component()->get_user_id());

        $assignment = $this->get_table()->get_component()->get_root_content_object();

        if ($isEntity || $assignment->get_visibility_submissions() == 1 ||
             $this->get_table()->getAssignmentDataProvider()->canEditAssignment())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ViewSubmissions'),
                    Theme :: getInstance()->getCommonImagePath('Action/Browser'),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_BROWSE,
                            Manager :: PARAM_ENTITY_TYPE => $entity[Entry :: PROPERTY_ENTITY_TYPE],
                            Manager :: PARAM_ENTITY_ID => $entity[Entry :: PROPERTY_ENTITY_ID])),
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($this->get_table()->getAssignmentDataProvider()->canEditAssignment() &&
             $entity[EntityTableColumnModel :: PROPERTY_ENTRY_COUNT] > 0)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('DownloadAllSubmissions'),
                    Theme :: getInstance()->getCommonImagePath('Action/Download'),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_DOWNLOAD,
                            Manager :: PARAM_ENTITY_TYPE => $entity[Entry :: PROPERTY_ENTITY_TYPE],
                            Manager :: PARAM_ENTITY_ID => $entity[Entry :: PROPERTY_ENTITY_ID])),
                    ToolbarItem :: DISPLAY_ICON));
        }

        if ($isEntity)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('SubmissionSubmit'),
                    Theme :: getInstance()->getCommonImagePath('Action/Add'),
                    $this->get_component()->get_url(
                        array(
                            Manager :: PARAM_ACTION => Manager :: ACTION_CREATE,
                            Manager :: PARAM_ENTITY_TYPE => $entity[Entry :: PROPERTY_ENTITY_TYPE],
                            Manager :: PARAM_ENTITY_ID => $entity[Entry :: PROPERTY_ENTITY_ID])),
                    ToolbarItem :: DISPLAY_ICON));
        }

        return $toolbar->as_html();
    }

    /**
     * Formats a date.
     *
     * @param int $date the date to be formatted.
     * @return string
     */
    protected function formatDate($date)
    {
        $formatted_date = DatetimeUtilities :: format_locale_date(
            Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES),
            $date);

        if ($this->get_table()->getAssignmentDataProvider()->isDateAfterAssignmentEndTime($date))
        {
            return '<span style="color:red">' . $formatted_date . '</span>';
        }

        return $formatted_date;
    }

    abstract protected function isEntity($entityId, $userId);
}