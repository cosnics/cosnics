<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\LearningPath;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters;
use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingServiceBuilder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\TreeNodeDataService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Libraries\Architecture\Interfaces\Categorizable;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.tool.learning_path
 */

/**
 * This tool allows a user to publish learning paths in his or her course.
 */
abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
    implements Categorizable, IntroductionTextSupportInterface
{
    const ACTION_EXPORT_RAW_RESULTS = 'AssessmentRawResultsExporter';

    /**
     *
     * @var int[]
     */
    protected $checkedPublications;

    /**
     *
     * @return array
     */
    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer::TYPE_LIST;

        return $browser_types;
    }

    /**
     *
     * @return array
     */
    public static function get_allowed_types()
    {
        return array(LearningPath::class_name());
    }

    /**
     *
     * @param Toolbar $toolbar
     * @param array $publication
     */
    public function add_content_object_publication_actions($toolbar, $publication)
    {
        $allowed = $this->is_allowed(WeblcmsRights::EDIT_RIGHT);

        if (!$this->isEmptyLearningPath($publication))
        {
            if ($allowed)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('Statistics'), new FontAwesomeGlyph('chart-bar'), $this->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_USER_PROGRESS
                        )
                    ), ToolbarItem::DISPLAY_ICON
                    )
                );

                $toolbar->add_item(
                    new ToolbarItem(
                        Translation::get('ExportRawResults'), new FontAwesomeGlyph('download'), $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_EXPORT_RAW_RESULTS,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                        )
                    ), ToolbarItem::DISPLAY_ICON
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
                        Translation::get('StatisticsNA'), new FontAwesomeGlyph('chart-bar', array('text-muted')), null,
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }
    }

    /**
     *
     * @param array $publication
     * @param ButtonGroup $buttonGroup
     * @param DropdownButton $dropdownButton
     */
    public function addContentObjectPublicationButtons(
        $publication, ButtonGroup $buttonGroup, DropdownButton $dropdownButton
    )
    {
        $allowed = $this->is_allowed(WeblcmsRights::EDIT_RIGHT);

        if (!$this->isEmptyLearningPath($publication))
        {
            if ($allowed)
            {
                $dropdownButton->prependSubButton(
                    new SubButton(
                        Translation::get('Statistics'), new FontAwesomeGlyph('chart-bar'), $this->get_url(
                        array(
                            Manager::PARAM_ACTION => Manager::ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID],
                            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_ACTION => \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::ACTION_VIEW_USER_PROGRESS
                        )
                    ), SubButton::DISPLAY_LABEL
                    )
                );

                $dropdownButton->prependSubButton(
                    new SubButton(
                        Translation::get('ExportRawResults'), new FontAwesomeGlyph('download'), $this->get_url(
                        array(
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_EXPORT_RAW_RESULTS,
                            \Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => $publication[ContentObjectPublication::PROPERTY_ID]
                        )
                    ), SubButton::DISPLAY_LABEL
                    )
                );
            }
        }
    }

    /**
     *
     * @param $publication
     *
     * @return int
     */
    public function isEmptyLearningPath($publication)
    {
        if (!array_key_exists($publication[ContentObjectPublication::PROPERTY_ID], $this->checkedPublications))
        {
            $object = $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID];
            $learningPath = new LearningPath();
            $learningPath->setId($object);

            $this->checkedPublications[$publication[ContentObjectPublication::PROPERTY_ID]] =
                $this->getLearningPathService()->isLearningPathEmpty(
                    $learningPath
                );
        }

        return $this->checkedPublications[$publication[ContentObjectPublication::PROPERTY_ID]];
    }

    /**
     * Returns the TreeNodeDataService
     *
     * @return TreeNodeDataService | object
     */
    protected function getTreeNodeDataService()
    {
        return $this->getService(TreeNodeDataService::class);
    }

    /**
     * Returns the LearningPathService
     *
     * @return LearningPathService | object
     */
    protected function getLearningPathService()
    {
        return $this->getService(LearningPathService::class);
    }

    /**
     * Creates the TrackingService for a given Publication and Course
     *
     * @param int $publicationId
     * @param int $courseId
     *
     * @return TrackingService
     */
    public function createTrackingServiceForPublicationAndCourse($publicationId, $courseId)
    {
        $trackingServiceBuilder = $this->getTrackingServiceBuilder();

        return $trackingServiceBuilder->buildTrackingService($this->getTrackingParameters($publicationId));
    }

    /**
     * Creates the TrackingParameters class
     *
     * @param int $publicationId
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\LearningPath\Domain\TrackingParameters
     */
    public function getTrackingParameters($publicationId = null)
    {
        if (empty($publicationId))
        {
            $publicationId =
                $this->getRequest()->getFromUrl(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        }

        return new TrackingParameters((int) $publicationId);
    }

    /**
     *
     * @return TrackingServiceBuilder | object
     */
    protected function getTrackingServiceBuilder()
    {
        return new TrackingServiceBuilder($this->getDataClassRepository());
    }

    /**
     *
     * @return \Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository
     */
    protected function getDataClassRepository()
    {
        return $this->getService('Chamilo\Libraries\Storage\DataManager\Doctrine\DataClassRepository');
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
        return $this->getLearningPathService()->getTree($learningPath);
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
