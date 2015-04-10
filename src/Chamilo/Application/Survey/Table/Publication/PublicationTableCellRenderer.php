<?php
namespace Chamilo\Application\Survey\Table\Publication;

use Chamilo\Application\Survey\Component\BrowserComponent;
use Chamilo\Application\Survey\Rights\Rights;
use Chamilo\Application\Survey\Storage\DataClass\Publication;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class PublicationTableCellRenderer extends DataClassTableCellRenderer implements TableCellRendererActionsColumnSupport
{

    function render_cell($column, $object)
    {
        switch ($column->get_name())
        {
            case Publication :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $object);
                // $title = Survey :: parse($this->get_component()->get_user_id(), null, $title);
                $url = '<a href="' . htmlentities($this->get_component()->get_browse_survey_participants_url($object)) .
                     '" title="' . $title . '">' . $title . '</a>';

                switch ($this->get_component()->get_table_type())
                {
                    case BrowserComponent :: TAB_PARTICIPATE :
                        if ($object->is_publication_period())
                        {
                            $url = '<a href="' .
                                 htmlentities($this->get_component()->get_survey_publication_taker_url($object)) .
                                 '" title="' . $title . '">' . $title . '</a>';
                        }
                        else
                        {
                            $url = $title;
                        }
                        break;

                    case BrowserComponent :: TAB_EXPORT :
                        $url = '<a href="' .
                             htmlentities($this->get_component()->get_survey_publication_export_url($object)) .
                             '" title="' . $title . '">' . $title . '</a>';
                        break;

                    case BrowserComponent :: TAB_MY_PUBLICATIONS :
                        $url = '<a href="' .
                             htmlentities($this->get_component()->get_browse_survey_participants_url($object)) .
                             '" title="' . $title . '">' . $title . '</a>';
                        break;
                }
                return $url;
            case Publication :: PROPERTY_FROM_DATE :
                return $this->get_date($object->get_from_date());
                break;
            case Publication :: PROPERTY_TO_DATE :
                return $this->get_date($object->get_to_date());
                break;
        }

        return parent :: render_cell($column, $object);
    }

    public function get_actions($object)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        switch ($this->get_component()->get_table_type())
        {

            case BrowserComponent :: TAB_PARTICIPATE :
                if (Rights :: get_instance()->is_right_granted(Rights :: PARTICIPATE_RIGHT, $object->get_id()))
                {
                    if ($object->is_publication_period())
                    {
                        $toolbar->add_item(
                            new ToolbarItem(
                                Translation :: get('TakeSurvey'),
                                Theme :: getInstance()->getCommonImagePath('Action/Next'),
                                $this->get_component()->get_survey_publication_taker_url($object),
                                ToolbarItem :: DISPLAY_ICON));
                    }
                    else
                    {
                        $toolbar->add_item(
                            new ToolbarItem(
                                Translation :: get('TakeSurvey'),
                                Theme :: getInstance()->getCommonImagePath('Action/NextNa'),
                                null,
                                ToolbarItem :: DISPLAY_ICON));
                    }
                }

                break;

            case BrowserComponent :: TAB_EXPORT :
                if (Rights :: get_instance()->is_right_granted(Rights :: RIGHT_EXPORT_RESULT, $object->get_id()))
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('ExportToExcel', array(), reporting),
                            Theme :: getInstance()->getCommonImagePath('Export/Excel'),
                            $this->get_component()->get_survey_publication_export_url($object),
                            ToolbarItem :: DISPLAY_ICON));
                }
                break;

            case BrowserComponent :: TAB_MY_PUBLICATIONS :
                if (Rights :: get_instance()->is_right_granted(Rights :: RIGHT_EDIT, $object->get_id()))
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('Edit', array(), Utilities :: COMMON_LIBRARIES),
                            Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                            $this->get_component()->get_update_survey_publication_url($object),
                            ToolbarItem :: DISPLAY_ICON));
                }
                if (Rights :: get_instance()->is_right_granted(Rights :: RIGHT_DELETE, $object->get_id()))
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('Delete', array(), Utilities :: COMMON_LIBRARIES),
                            Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                            $this->get_component()->get_delete_survey_publication_url($object),
                            ToolbarItem :: DISPLAY_ICON,
                            true));
                }
                if (Rights :: get_instance()->is_right_granted(Rights :: INVITE_RIGHT, $object->get_id()))
                {
                    $toolbar->add_item(
                        new ToolbarItem(
                            Translation :: get('ViewParticipants'),
                            Theme :: getInstance()->getCommonImagePath('Action/Subscribe'),
                            $this->get_component()->get_browse_survey_participants_url($object),
                            ToolbarItem :: DISPLAY_ICON));
                }
                break;
        }

        return $toolbar->as_html();
    }

    private function get_date($date)
    {
        if ($date == 0)
        {
            return Translation :: get('NoDate');
        }
        else
        {
            return date("Y-m-d H:i", $date);
        }
    }
}
?>