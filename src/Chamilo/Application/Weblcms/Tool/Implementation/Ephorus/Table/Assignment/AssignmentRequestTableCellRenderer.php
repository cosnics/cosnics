<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Assignment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Table\Request\RequestTableColumnModel;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableCellRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * CellRenderer for ephorus requests browser table.
 * 
 * @author Tom Goethals - Hogeschool Gent
 */
class AssignmentRequestTableCellRenderer extends DataClassTableCellRenderer implements 
    TableCellRendererActionsColumnSupport
{

    /**
     * Renders the cell for a given column and row (object)
     * 
     * @param NewObjectTableColumn $column
     * @param DataClass $object
     *
     * @return string
     */
    public function render_cell($column, $object)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_DESCRIPTION :
                return Utilities :: htmlentities(
                    Utilities :: truncate_string(
                        $object->get_default_property(ContentObject :: PROPERTY_DESCRIPTION), 
                        50));
            case RequestTableColumnModel :: COLUMN_NAME_AUTHOR :
                return $object->get_optional_property(User :: PROPERTY_FIRSTNAME) . ' ' .
                     $object->get_optional_property(User :: PROPERTY_LASTNAME);
            // return $object->get_author()->get_fullname();
            case Request :: PROPERTY_REQUEST_TIME :
                if ($object->get_optional_property(Request :: PROPERTY_REQUEST_TIME))
                {
                    return DatetimeUtilities :: format_locale_date(
                        null, 
                        $object->get_optional_property(Request :: PROPERTY_REQUEST_TIME));
                }
                else
                {
                    return '-';
                }
            case AssignmentSubmission :: PROPERTY_SUBMITTER_TYPE :
                switch ($object->get_optional_property(AssignmentSubmission :: PROPERTY_SUBMITTER_TYPE))
                {
                    case AssignmentSubmission :: SUBMITTER_TYPE_COURSE_GROUP :
                        return Translation :: get('CourseGroup');
                    case AssignmentSubmission :: SUBMITTER_TYPE_PLATFORM_GROUP :
                        return Translation :: get('PlatformGroup');
                    case AssignmentSubmission :: SUBMITTER_TYPE_USER :
                        return Translation :: get('User');
                }
            case AssignmentSubmission :: PROPERTY_DATE_SUBMITTED :
                return DatetimeUtilities :: format_locale_date(
                    null, 
                    $object->get_optional_property(AssignmentSubmission :: PROPERTY_DATE_SUBMITTED));
            case Request :: PROPERTY_STATUS :
                if ($object->get_optional_property(Request :: PROPERTY_STATUS) > 0)
                {
                    return Request :: status_as_string($object->get_optional_property(Request :: PROPERTY_STATUS));
                }
                else
                {
                    return '-';
                }
            case Request :: PROPERTY_PERCENTAGE :
                if ($object->get_optional_property(Request :: PROPERTY_STATUS) != null)
                {
                    return $object->get_optional_property(Request :: PROPERTY_PERCENTAGE) . '%';
                }
                else
                {
                    return '-';
                }
            case Request :: PROPERTY_VISIBLE_IN_INDEX :
                if ($object->get_optional_property(Request :: PROPERTY_VISIBLE_IN_INDEX) != null)
                {
                    return $object->get_optional_property(Request :: PROPERTY_VISIBLE_IN_INDEX) ? Translation :: get(
                        'YesVisible') : Translation :: get('NoVisible');
                }
                else
                {
                    return '-';
                }
        }
        
        return parent :: render_cell($column, $object);
    }

    /**
     * Returns the actions toolbar
     * 
     * @param DataClass $object
     *
     * @return String
     */
    public function get_actions($object)
    {
        $toolbar = new Toolbar();
        
        $request_id = $object->get_optional_property(Request :: PROPERTY_REQUEST_ID);
        if ($request_id != null)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('ViewResult'), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_reporting.png', 
                    $this->get_component()->get_ephorus_request_url($request_id), 
                    ToolbarItem :: DISPLAY_ICON));
        }
        
        if ($object->get_optional_property(Request :: PROPERTY_REQUEST_TIME))
        {
            if ($object->get_optional_property(Request :: PROPERTY_STATUS) != Request :: STATUS_DUPLICATE)
            {
                if (! $object->get_optional_property(Request :: PROPERTY_VISIBLE_IN_INDEX))
                {
                    $icon = 'action_invisible.png';
                    $translation = Translation :: get('AddDocumentToIndex');
                }
                else
                {
                    $icon = 'action_visible.png';
                    $translation = Translation :: get('RemoveDocumentFromIndex');
                }
                
                $toolbar->add_item(
                    new ToolbarItem(
                        $translation, 
                        Theme :: getInstance()->getCommonImagePath() . $icon, 
                        $this->get_component()->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_INDEX_VISIBILITY_CHANGER, 
                                \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager :: ACTION_CHANGE_INDEX_VISIBILITY, 
                                Manager :: PARAM_REQUEST_IDS => $request_id, 
                                \Chamilo\Application\Weblcms\Manager :: PARAM_PUBLICATION => $this->get_component()->get_publication_id())), 
                        ToolbarItem :: DISPLAY_ICON));
            }
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('AddDocument'), 
                    Theme :: getInstance()->getCommonImagePath() . 'action_up.png', 
                    $this->get_component()->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => Manager :: ACTION_ASSIGNMENT_EPHORUS_REQUEST, 
                            \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager :: PARAM_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Request\Manager :: ACTION_CREATE, 
                            Manager :: PARAM_CONTENT_OBJECT_IDS => $object->get_id()))));
        }
        
        return $toolbar->as_html();
    }
}
