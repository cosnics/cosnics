<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Table\Publication;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Table\Publication\Table\ObjectPublicationTableCellRenderer;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Extension on the content object publication table cell renderer for this tool
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PublicationTableCellRenderer extends ObjectPublicationTableCellRenderer
{

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Renders a cell for a given object
     * 
     * @param $column \libraries\ObjectTableColumn
     *
     * @param mixed $publication
     *
     * @return String
     */
    public function render_cell($column, $publication)
    {
        $content_object = $this->get_component()->get_content_object_from_publication($publication);
        
        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_TITLE :
                return $this->generate_title_link($publication);
            case Assignment::PROPERTY_END_TIME :
                $time = $content_object->get_end_time();
                $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);
                $time = DatetimeUtilities::format_locale_date($date_format, $time);
                if ($publication[ContentObjectPublication::PROPERTY_HIDDEN])
                {
                    return '<span style="color: gray">' . $time . '</span>';
                }
                return $time;
            case Manager::PROPERTY_NUMBER_OF_SUBMISSIONS :
                $tracker = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission();
                $condition = new EqualityCondition(
                    new PropertyConditionVariable(
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                        \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::PROPERTY_PUBLICATION_ID), 
                    new StaticConditionVariable($publication[ContentObjectPublication::PROPERTY_ID]));
                
                return DataManager::count(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssignmentSubmission::class_name(), 
                    new DataClassCountParameters($condition));
            case Assignment::PROPERTY_ALLOW_GROUP_SUBMISSIONS :
                if ($content_object->get_allow_group_submissions())
                {
                    return '<img src="' . Theme::getInstance()->getImagePath(
                        'Chamilo\Application\Weblcms\Tool\Implementation\Assignment', 
                        'Type/Group') . '" alt="' . Translation::get('GroupAssignment') . '" title="' .
                         Translation::get('GroupAssignment') . '"/>';
                }
                return '<img src="' . Theme::getInstance()->getImagePath(
                    'Chamilo\Application\Weblcms\Tool\Implementation\Assignment', 
                    'Type/Individual') . '" alt="' . Translation::get('IndividualAssignment') . '" title="' .
                     Translation::get('IndividualAssignment') . '"/>';
        }
        
        return parent::render_cell($column, $publication);
    }

    /**
     * Generated the HTML for the title column, including link, depending on the status of the current browsing user.
     * 
     * @param $publication type The publication for which the title link is to be generated.
     * @return string The HTML for the link in the title column.
     */
    private function generate_title_link($publication)
    {
        if ($this->get_component()->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            return $this->generate_teacher_title_link($publication);
        }
        return $this->generate_student_title_link($publication);
    }

    /**
     * Generates the link applicable for the current browsing user being a teacher or admin.
     * 
     * @param $publication type The publication for which the link is being generated.
     * @return string The HTML anchor elemnt that represents the link.
     */
    private function generate_teacher_title_link($publication)
    {
        $url = $this->get_component()->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID], 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_BROWSE_SUBMITTERS));
        return '<a href="' . $url . '">' .
             StringUtilities::getInstance()->truncate($publication[ContentObject::PROPERTY_TITLE], 50) . '</a>';
    }

    /**
     * Generates the link applicable for the current browsing user being a student.
     * 
     * @param $publication type The publication for which the link is being generated.
     * @return string The HTML anchor element that represents the link.
     */
    private function generate_student_title_link($publication)
    {
        $url = $this->get_component()->get_url(
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => Manager::ACTION_STUDENT_BROWSE_SUBMISSIONS, 
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]));
        return '<a href="' . $url . '">' .
             StringUtilities::getInstance()->truncate($publication[ContentObject::PROPERTY_TITLE], 50) . '</a>';
    }
}