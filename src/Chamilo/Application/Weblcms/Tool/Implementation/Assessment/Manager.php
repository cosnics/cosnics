<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assessment;

use Chamilo\Application\Weblcms\Integration\Chamilo\Core\Tracking\Storage\DataClass\AssessmentAttempt;
use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.assessment
 */

/**
 * This tool allows a user to publish assessments in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
    implements Categorizable, IntroductionTextSupportInterface
{
    public const ACTION_ATTEMPT_RESULT_VIEWER = 'AttemptResultViewer';
    public const ACTION_DELETE_RESULTS = 'ResultsDeleter';
    public const ACTION_RAW_EXPORT_RESULTS = 'RawExportResults';
    public const ACTION_SAVE_DOCUMENTS = 'DocumentSaver';
    public const ACTION_TAKE_ASSESSMENT = 'ComplexDisplay';
    public const ACTION_VIEW_RESULTS = 'ResultsViewer';

    public const CONTEXT = __NAMESPACE__;

    public const PARAM_ANONYMOUS = 'anonymous';
    public const PARAM_ASSESSMENT = 'aid';
    public const PARAM_INVITATION_ID = 'invitation_id';
    public const PARAM_PUBLICATION_ACTION = 'publication_action';
    public const PARAM_QUESTION_ATTEMPT = 'qaid';
    public const PARAM_USER_ASSESSMENT = 'uaid';

    private static $checked_publications = [];

    public function addContentObjectPublicationButtons(
        $publication, ButtonGroup $buttonGroup, DropdownButton $dropdownButton
    )
    {
        $publication_id = $publication[ContentObjectPublication::PROPERTY_ID];

        $buttonGroup->prependButton(
            new Button(
                Translation::get('Take'), new FontAwesomeGlyph('play'), $this->get_complex_display_url($publication_id),
                Button::DISPLAY_ICON, null, ['btn-link']
            )
        );

        $dropdownButton->prependSubButton(
            new SubButton(
                Translation::get('ManageAttempts'), new FontAwesomeGlyph('file-signature', [], 'fas'), $this->get_url(
                [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_RESULTS,
                    self::PARAM_ASSESSMENT => $publication_id
                ]
            ), SubButton::DISPLAY_LABEL
            )
        );
    }

    public function add_content_object_publication_actions($toolbar, $publication)
    {
        $publication_id = $publication[ContentObjectPublication::PROPERTY_ID];

        if ($publication[ContentObject::PROPERTY_TYPE] == Assessment::class)
        {
            $complex_display_item = $toolbar->get_item(1);
            $complex_display_item->set_image(new FontAwesomeGlyph('forward'));
            $complex_display_item->set_label(Translation::get('Take'));
        }

        if ($publication[ContentObject::PROPERTY_TYPE] == Hotpotatoes::class)
        {
            $toolbar->insert_item(
                new ToolbarItem(
                    Translation::get('Take'), new FontAwesomeGlyph('forward'),
                    $this->get_complex_display_url($publication_id), ToolbarItem::DISPLAY_ICON
                ), 1
            );
        }

        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('ManageAttempts'), new FontAwesomeGlyph('file-signature', [], 'fas'), $this->get_url(
                [
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_VIEW_RESULTS,
                    self::PARAM_ASSESSMENT => $publication_id
                ]
            ), ToolbarItem::DISPLAY_ICON
            )
        );
    }

    public static function get_allowed_types()
    {
        return [Assessment::class, Hotpotatoes::class];
    }

    public function get_available_browser_types()
    {
        $browser_types = [];
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;

        return $browser_types;
    }

    public function is_content_object_attempt_possible($publication)
    {
        if (!array_key_exists($publication->get_id(), self::$checked_publications))
        {
            $assessment = $publication->get_content_object();
            $track = new AssessmentAttempt();
            $condition_t = new EqualityCondition(
                new PropertyConditionVariable(
                    AssessmentAttempt::class, AssessmentAttempt::PROPERTY_ASSESSMENT_ID
                ), new StaticConditionVariable($publication->get_id())
            );
            $condition_u = new EqualityCondition(
                new PropertyConditionVariable(
                    AssessmentAttempt::class, AssessmentAttempt::PROPERTY_USER_ID
                ), new StaticConditionVariable($this->get_user_id())
            );
            $condition = new AndCondition([$condition_t, $condition_u]);

            $trackers = DataManager::retrieves(
                AssessmentAttempt::class, new DataClassRetrievesParameters($condition)
            );

            $count = count($trackers);

            foreach ($trackers as $tracker)
            {
                if ($tracker->get_status() == AssessmentAttempt::STATUS_NOT_COMPLETED)
                {
                    $this->active_tracker = $tracker;
                    $count --;
                    break;
                }
            }

            self::$checked_publications[$publication->get_id()] =
                ($assessment->get_maximum_attempts() == 0 || $count < $assessment->get_maximum_attempts());
        }

        return self::$checked_publications[$publication->get_id()];
    }
}
