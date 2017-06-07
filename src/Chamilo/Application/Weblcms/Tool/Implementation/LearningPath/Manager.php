<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\LearningPathTrackingParameters;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingServiceBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
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
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements Categorizable,
    IntroductionTextSupportInterface
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

    /**
     * Tree cache
     *
     * @var Tree[]
     */
    protected $tree;

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;

        return $browser_types;
    }

    public static function get_allowed_types()
    {
        return array(LearningPath::class_name());
    }

    public function add_content_object_publication_actions($toolbar, $publication)
    {
        $allowed = $this->is_allowed(WeblcmsRights::EDIT_RIGHT);

        if (!$this->is_empty_learning_path($publication))
        {
            if ($allowed)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Reporting'),
                        Theme::getInstance()->getCommonImagePath('Action/Statistics'),
                        $this->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION =>
                                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_USER_PROGRESS
                            )
                        ),
                        ToolbarItem::DISPLAY_ICON
                    )
                );

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('ExportRawResults'),
                        Theme::getInstance()->getCommonImagePath('Action/Export'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_EXPORT_RAW_RESULTS,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                            )
                        ),
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }
        else
        {
            if ($allowed)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('StatisticsNA'),
                        Theme::getInstance()->getCommonImagePath('Action/StatisticsNa'),
                        null,
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }
    }

    public function addContentObjectPublicationButtons(
        $publication, ButtonGroup $buttonGroup,
        DropdownButton $dropdownButton
    )
    {
        $allowed = $this->is_allowed(WeblcmsRights::EDIT_RIGHT);

        if (!$this->is_empty_learning_path($publication))
        {
            if ($allowed)
            {
                $dropdownButton->prependSubButton(
                    new SubButton(
                        Translation::get('Statistics'),
                        Theme::getInstance()->getCommonImagePath('Action/Statistics'),
                        $this->get_url(
                            array(
                                Manager::PARAM_ACTION => Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION =>
                                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_USER_PROGRESS
                            )
                        ),
                        SubButton::DISPLAY_LABEL
                    )
                );

                $dropdownButton->prependSubButton(
                    new SubButton(
                        Translation::get('ExportRawResults'),
                        Theme::getInstance()->getCommonImagePath('Action/Export'),
                        $this->get_url(
                            array(
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_EXPORT_RAW_RESULTS,
                                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                            )
                        ),
                        SubButton::DISPLAY_LABEL
                    )
                );
            }
        }
    }

    private static $checked_publications = array();

    public function is_empty_learning_path($publication)
    {
        if (!array_key_exists($publication[ContentObjectPublication::PROPERTY_ID], $this->checked_publications))
        {
            $object = $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID];
            $learningPath = new LearningPath();
            $learningPath->setId($object);

            $this->checked_publications[$publication[ContentObjectPublication::PROPERTY_ID]] =
                $this->getLearningPathService()->isLearningPathEmpty($learningPath);
        }

        return $this->checked_publications[$publication[ContentObjectPublication::PROPERTY_ID]];
    }

    /**
     * Returns the TreeNodeDataService
     *
     * @return TreeNodeDataService | object
     */
    protected function getTreeNodeDataService()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.tree_node_data_service'
        );
    }

    /**
     * Returns the LearningPathService
     *
     * @return LearningPathService | object
     */
    protected function getLearningPathService()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.learning_path_service'
        );
    }

    /**
     * Creates the LearningPathTrackingService for a given Publication and Course
     *
     * @param int $publicationId
     * @param int $courseId
     *
     * @return LearningPathTrackingService
     */
    public function createLearningPathTrackingServiceForPublicationAndCourse($publicationId, $courseId)
    {
        $learningPathTrackingServiceBuilder = $this->getLearningPathTrackingServiceBuilder();

        return $learningPathTrackingServiceBuilder->buildLearningPathTrackingService(
            new LearningPathTrackingParameters((int) $courseId, (int) $publicationId)
        );
    }

    /**
     * @return LearningPathTrackingServiceBuilder | object
     */
    protected function getLearningPathTrackingServiceBuilder()
    {
        return new LearningPathTrackingServiceBuilder($this->getDataClassRepository());
    }

    /**
     * @return object | DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->getService(
            'chamilo.libraries.storage.data_manager.doctrine.data_class_repository'
        );
    }

    /**
     * Returns the TreeBuilder service
     *
     * @return TreeBuilder | object
     */
    protected function getTreeBuilder()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.tree_builder'
        );
    }

    /**
     * Returns the currently selected learning path child id from the request
     *
     * @return int
     */
    public function getCurrentTreeNodeDataId()
    {
        return (int) $this->getRequest()->get(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID, 0
        );
    }

    /**
     * Returns the Tree for the current learning path root
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree
     */
    protected function getTree(LearningPath $learningPath)
    {
        if (!isset($this->tree[$learningPath->getId()]))
        {
            $this->tree[$learningPath->getId()] =
                $this->getTreeBuilder()->buildTree($learningPath);
        }

        return $this->tree[$learningPath->getId()];
    }

    /**
     * Returns the TreeNode for the current step
     *
     * @param LearningPath $learningPath
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode
     */
    public function getCurrentTreeNode(LearningPath $learningPath)
    {
        $tree = $this->getTree($learningPath);

        return $tree->getTreeNodeById($this->getCurrentTreeNodeDataId());
    }
}
