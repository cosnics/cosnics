<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;

/**
 * $Id: learning_path_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 *
 * @package application.lib.weblcms.tool.learning_path
 */
/**
 * This tool allows a user to publish learning paths in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable, IntroductionTextSupportInterface
{
    const ACTION_DOWNLOAD_DOCUMENTS = 'DocumentSaver';
    const ACTION_VIEW_ASSESSMENT_RESULTS = 'AssessmentResultsViewer';
    const ACTION_EXPORT_RAW_RESULTS = 'AssessmentRawResultsExporter';
    const ACTION_VIEW_STATISTICS = 'StatisticsViewer';
    const PARAM_ASSESSMENT_ID = 'assessment';
    const PARAM_LEARNING_PATH_ITEM_ATTEMPT_ID = 'lpi_attempt';
    const PARAM_OBJECT_ID = 'object_id';
    const PARAM_LEARNING_PATH = 'lp';
    const PARAM_LP_STEP = 'step';
    const PARAM_LEARNING_PATH_ID = 'lpid';
    const PARAM_ATTEMPT_ID = 'attempt_id';

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        return $browser_types;
    }

    public static function get_allowed_types()
    {
        return array(LearningPath :: class_name());
    }

    public function add_content_object_publication_actions($toolbar, $publication)
    {
        $allowed = $this->is_allowed(WeblcmsRights :: EDIT_RIGHT);

        if (! $this->is_empty_learning_path($publication))
        {
            if ($allowed)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('Statistics'),
                        Theme :: getInstance()->getCommonImagePath('Action/Statistics'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_VIEW_STATISTICS,
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID])),
                        ToolbarItem :: DISPLAY_ICON));

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('ExportRawResults'),
                        Theme :: getInstance()->getCommonImagePath('Action/Export'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_EXPORT_RAW_RESULTS,
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID])),
                        ToolbarItem :: DISPLAY_ICON));
            }
        }
        else
        {
            if ($allowed)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation :: get('StatisticsNA'),
                        Theme :: getInstance()->getCommonImagePath('Action/StatisticsNa'),
                        null,
                        ToolbarItem :: DISPLAY_ICON));
            }
        }
    }

    public function addContentObjectPublicationButtons($publication, ButtonGroup $buttonGroup,
        DropdownButton $dropdownButton)
    {
        $allowed = $this->is_allowed(WeblcmsRights :: EDIT_RIGHT);

        if (! $this->is_empty_learning_path($publication))
        {
            if ($allowed)
            {
                $dropdownButton->prependSubButton(
                    new SubButton(
                        Translation :: get('Statistics'),
                        Theme :: getInstance()->getCommonImagePath('Action/Statistics'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_VIEW_STATISTICS,
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID])),
                        SubButton :: DISPLAY_LABEL));

                $dropdownButton->prependSubButton(
                    new SubButton(
                        Translation :: get('ExportRawResults'),
                        Theme :: getInstance()->getCommonImagePath('Action/Export'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_ACTION => self :: ACTION_EXPORT_RAW_RESULTS,
                                \Chamilo\Application\Weblcms\Tool\Manager :: PARAM_PUBLICATION_ID => $publication[ContentObjectPublication :: PROPERTY_ID])),
                        SubButton :: DISPLAY_LABEL));
            }
        }
    }

    private static $checked_publications = array();

    public function is_empty_learning_path($publication)
    {
        if (! array_key_exists($publication[ContentObjectPublication :: PROPERTY_ID], $this->checked_publications))
        {
            $object = $publication[ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID];
            $condition = new EqualityCondition(
                new PropertyConditionVariable(
                    ComplexContentObjectItem :: class_name(),
                    ComplexContentObjectItem :: PROPERTY_PARENT),
                new StaticConditionVariable($object));
            $count = \Chamilo\Core\Repository\Storage\DataManager :: count_complex_content_object_items(
                ComplexContentObjectItem :: class_name(),
                $condition);

            $this->checked_publications[$publication[ContentObjectPublication :: PROPERTY_ID]] = $count == 0;
        }

        return $this->checked_publications[$publication[ContentObjectPublication :: PROPERTY_ID]];
    }
}
