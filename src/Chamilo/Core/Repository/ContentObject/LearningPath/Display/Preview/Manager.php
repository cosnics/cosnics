<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Domain\LearningPathTrackingParameters;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Preview\Storage\Repository\LearningPathTrackingRepository;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathAttemptService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\LearningPathTreeBuilder;
use Chamilo\Core\Repository\Display\PreviewResetSupport;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\Page;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Preview implements PreviewResetSupport
{

    /**
     * @var LearningPathTrackingService
     */
    protected $learningPathTrackingService;

    /**
     * @var LearningPathTrackingRepository
     */
    protected $learningPathTrackingRepository;

    /**
     *
     * @see \core\repository\display\Preview::get_root_content_object()
     */
    function get_root_content_object()
    {
        if ($this->is_embedded())
        {
            $embedded_content_object_id = $this->get_embedded_content_object_id();
            $this->set_parameter(Embedder::PARAM_EMBEDDED_CONTENT_OBJECT_ID, $embedded_content_object_id);
            $this->set_parameter(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID,
                Request::get(\Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID)
            );

            return \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(),
                $embedded_content_object_id
            );
        }
        else
        {
            $this->set_parameter(
                \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_LEARNING_PATH_ITEM_ID,
                Request::get(
                    \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_LEARNING_PATH_ITEM_ID
                )
            );
            $this->set_parameter(
                \Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID,
                Request::get(\Chamilo\Core\Repository\Display\Manager::PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID)
            );

            return parent::get_root_content_object();
        }
    }

    /**
     *
     * @see \libraries\architecture\application\Application::render_header()
     */
    public function render_header()
    {
        if ($this->is_embedded())
        {
            $page = Page::getInstance();
            $page->setViewMode(Page::VIEW_MODE_HEADERLESS);

            return $page->getHeader()->toHtml();
        }
        else
        {
            return parent::render_header();
        }
    }

    /**
     *
     * @return boolean
     */
    function is_embedded()
    {
        $embedded_content_object_id = $this->get_embedded_content_object_id();

        return isset($embedded_content_object_id);
    }

    /**
     *
     * @return int
     */
    function get_embedded_content_object_id()
    {
        return Embedder::get_embedded_content_object_id();
    }

    /**
     * Preview mode, so always return true.
     *
     * @param $right
     *
     * @return boolean
     */
    function is_allowed($right)
    {
        return true;
    }

    /**
     * Functionality is publication dependent, so not available in preview mode.
     */
    function get_publication()
    {
        $this->not_available(Translation::get('ImpossibleInPreviewMode'));
    }

    // FUNCTIONS FOR COMPLEX DISPLAY SUPPORT
    public function is_allowed_to_edit_content_object()
    {
        return true;
    }

    public function is_allowed_to_view_content_object()
    {
        return true;
    }

    function is_allowed_to_add_child()
    {
        return true;
    }

    function is_allowed_to_delete_child()
    {
        return true;
    }

    function is_allowed_to_delete_feedback()
    {
        return true;
    }

    function is_allowed_to_edit_feedback()
    {
        return true;
    }

    public function is_allowed_to_set_content_object_rights()
    {
        return true;
    }

    /**
     * Returns the currently selected learning path child id from the request
     *
     * @return int
     */
    public function getCurrentLearningPathChildId()
    {
        return (int) $this->getRequest()->get(
            \Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager::PARAM_CHILD_ID, 0
        );
    }

    /**
     * Returns the LearningPathTreeBuilder service
     *
     * @return LearningPathTreeBuilder | object
     */
    protected function getLearningPathTreeBuilder()
    {
        return $this->getService(
            'chamilo.core.repository.content_object.learning_path.service.learning_path_tree_builder'
        );
    }

    /**
     * Returns the LearningPathTree for the current learning path root
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTree
     */
    protected function getLearningPathTree()
    {
        if (!isset($this->learningPathTree))
        {
            $this->learningPathTree = $this->getLearningPathTreeBuilder()->buildLearningPathTree(
                parent::get_root_content_object()
            );
        }

        return $this->learningPathTree;
    }

    /**
     * Returns the LearningPathTreeNode for the current step
     *
     * @return \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\LearningPathTreeNode
     */
    public function getCurrentLearningPathTreeNode()
    {
        $learningPathTree = $this->getLearningPathTree();

        return $learningPathTree->getLearningPathTreeNodeById($this->getCurrentLearningPathChildId());
    }

    /**
     * Resets the storage for the preview
     */
    public function reset()
    {
        $this->buildLearningPathTrackingService();
        $this->learningPathTrackingRepository->resetStorage();

        return true;
    }

    /**
     * Builds the LearningPathTrackingService
     *
     * @return LearningPathTrackingService
     */
    public function buildLearningPathTrackingService()
    {
        if (!isset($this->learningPathTrackingService))
        {
            $this->learningPathTrackingRepository = new LearningPathTrackingRepository();

            $learningPathAttemptService = new LearningPathAttemptService(
                $this->learningPathTrackingRepository, new LearningPathTrackingParameters()
            );

            $this->learningPathTrackingService = new LearningPathTrackingService(
                $learningPathAttemptService, $this->learningPathTrackingRepository
            );
        }

        return $this->learningPathTrackingService;
    }
}
