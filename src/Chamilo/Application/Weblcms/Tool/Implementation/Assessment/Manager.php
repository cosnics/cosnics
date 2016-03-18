<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * $Id: assessment_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.assessment
 */

/**
 * This tool allows a user to publish assessments in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable
{
    const ACTION_VIEW_RESULTS = 'ResultsViewer';
    const ACTION_ATTEMPT_RESULT_VIEWER = 'AttemptResultViewer';
    const ACTION_SAVE_DOCUMENTS = 'DocumentSaver';
    const ACTION_RAW_EXPORT_RESULTS = 'RawExportResults';
    const ACTION_DELETE_RESULTS = 'ResultsDeleter';
    const ACTION_TAKE_ASSESSMENT = 'ComplexDisplay';
    const PARAM_USER_ASSESSMENT = 'uaid';
    const PARAM_QUESTION_ATTEMPT = 'qaid';
    const PARAM_ASSESSMENT = 'aid';
    const PARAM_ANONYMOUS = 'anonymous';
    const PARAM_INVITATION_ID = 'invitation_id';
    const PARAM_PUBLICATION_ACTION = 'publication_action';

    public static function get_allowed_types()
    {
        return array(Assessment :: class_name(), Hotpotatoes :: class_name());
    }

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        return $browser_types;
    }

    public function add_content_object_publication_actions($toolbar, $publication)
    {
        $publication_id = $publication[ContentObjectPublication :: PROPERTY_ID];

        if ($publication[ContentObject :: PROPERTY_TYPE] == Assessment :: class_name())
        {
            $complex_display_item = $toolbar->get_item(1);
            $complex_display_item->set_image(Theme :: getInstance()->getCommonImagePath('Action/Next'));
            $complex_display_item->set_label(Translation :: get('Take'));
        }

        if ($publication[ContentObject :: PROPERTY_TYPE] == Hotpotatoes :: class_name())
        {
            $toolbar->insert_item(
                new ToolbarItem(
                    Translation :: get('Take'),
                    Theme :: getInstance()->getCommonImagePath('Action/Next'),
                    $this->get_complex_display_url($publication_id),
                    ToolbarItem :: DISPLAY_ICON),
                1);
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('ManageAttempts'),
                Theme :: getInstance()->getImagePath(__NAMESPACE__, 'ManageAttempts'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_VIEW_RESULTS,
                        self :: PARAM_ASSESSMENT => $publication_id)),
                ToolbarItem :: DISPLAY_ICON));
    }

    private static $checked_publications = array();

    public function is_content_object_attempt_possible($publication)
    {
        if (! array_key_exists($publication->get_id(), self :: $checked_publications))
        {
            $assessment = $publication->get_content_object();
            $track = new \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt();
            $condition_t = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: PROPERTY_ASSESSMENT_ID),
                new StaticConditionVariable($publication->get_id()));
            $condition_u = new EqualityCondition(
                new PropertyConditionVariable(
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: class_name(),
                    \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: PROPERTY_USER_ID),
                new StaticConditionVariable($this->get_user_id()));
            $condition = new AndCondition(array($condition_t, $condition_u));

            $trackers = DataManager :: retrieves(
                \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt :: class_name(),
                new DataClassRetrievesParameters($condition))->as_array();

            $count = count($trackers);

            foreach ($trackers as $tracker)
            {
                if ($tracker->get_status() == AssessmentAttempt :: STATUS_NOT_COMPLETED)
                {
                    $this->active_tracker = $tracker;
                    $count --;
                    break;
                }
            }

            self :: $checked_publications[$publication->get_id()] = ($assessment->get_maximum_attempts() == 0 ||
                 $count < $assessment->get_maximum_attempts());
        }

        return self :: $checked_publications[$publication->get_id()];
    }
}
