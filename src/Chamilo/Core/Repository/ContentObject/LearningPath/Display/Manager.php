<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package core\repository\content_object\learning_path\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    // Actions
    const ACTION_FEEDBACK = 'Feedback';
    const ACTION_BOOKMARK = 'Bookmarker';
    const ACTION_ACTIVITY = 'Activity';
    const ACTION_RIGHTS = 'Rights';
    const ACTION_MOVE = 'Mover';
    const ACTION_SORT = 'Sorter';
    const ACTION_MANAGE = 'Manager';
    const ACTION_USER = 'User';
    const ACTION_BUILD_PREREQUISITES = 'PrerequisitesBuilder';
    const ACTION_TYPE_SPECIFIC = 'TypeSpecific';
    const ACTION_BUILD = 'Builder';
    const ACTION_REPORTING = 'Reporting';
    const ACTION_ATTEMPT = 'Attempt';
    const ACTION_MOVE_DIRECTLY = 'DirectMover';
    
    // Parameters
    const PARAM_STEP = 'step';
    const PARAM_SHOW_PROGRESS = 'show_progress';
    const PARAM_DETAILS = 'details';
    const PARAM_LEARNING_PATH_ITEM_ID = 'learning_path_item_id';
    const PARAM_SORT = 'sort';
    const PARAM_ITEM_ATTEMPT_ID = 'item_attempt_id';
    const PARAM_FULL_SCREEN = 'full_screen';
    const PARAM_PARENT_ID = 'parent_id';
    const PARAM_DISPLAY_ORDER = 'display_order';
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
    
    // Sorting
    const SORT_UP = 'Up';
    const SORT_DOWN = 'Down';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

    /**
     *
     * @var int
     */
    protected $current_step;

    /**
     * Get the id of the currently requested step
     * 
     * @return int
     */
    public function get_current_step()
    {
        if (! isset($this->current_step))
        {
            if ($this->is_current_step_set())
            {
                $this->current_step = $this->get_current_step_from_request();
                
                if (is_array($this->current_step))
                {
                    $this->current_step = $this->current_step[0];
                }
            }
            else
            {
                $this->current_step = $this->get_complex_content_object_path()->get_root()->get_id();
            }
        }
        
        return $this->current_step;
    }

    /**
     *
     * @return boolean
     */
    public function is_current_step_set()
    {
        $currentStepFromRequest = $this->get_current_step_from_request();
        return ! is_null($currentStepFromRequest);
    }

    /**
     *
     * @return int
     */
    private function get_current_step_from_request()
    {
        $step = $this->getRequest()->request->get(self::PARAM_STEP);
        if(empty($step))
        {
            $step = $this->getRequest()->query->get(self::PARAM_STEP);
        }

        return $step;
    }

    /**
     * Get the content object linked to the current step
     * 
     * @return ContentObject
     */
    public function get_current_content_object()
    {
        return $this->get_current_node()->get_content_object();
    }

    /**
     * Get the complex content object item linked to the current step
     * 
     * @return ComplexContentObjectItem
     */
    public function get_current_complex_content_object_item()
    {
        return $this->get_current_node()->get_complex_content_object_item();
    }

    /**
     * Get the node linked to the current step
     * 
     * @return \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode
     */
    public function get_current_node()
    {
        try
        {
            return $this->get_complex_content_object_path()->get_node($this->get_current_step());
        }
        catch (\Exception $ex)
        {
            throw new UserException(
                Translation::getInstance()->getTranslation(
                    'CouldNotRetrieveSelectedNode', null, 'Chamilo\Core\Repository'
                )
            );
        }
    }

    /**
     *
     * @return ComplexContentObjectPath
     */
    public function get_complex_content_object_path()
    {
        $learning_path_item_attempt_data = $this->get_parent()->retrieve_learning_path_tracker_items(
            $this->get_parent()->retrieve_learning_path_tracker());
        
        return $this->get_parent()->get_root_content_object()->get_complex_content_object_path(
            $learning_path_item_attempt_data);
    }

    /**
     *
     * @return boolean
     */
    public function is_allowed_to_edit_attempt_data()
    {
        return $this->get_application()->is_allowed_to_edit_learning_path_attempt_data();
    }

    /**
     *
     * @see \libraries\architecture\application\Application::get_additional_parameters()
     */
    public function get_additional_parameters()
    {
        return array(self::PARAM_STEP, self::PARAM_FULL_SCREEN);
    }

    /**
     * Helper function to validate the current node based on the given content object id
     */
    protected function validateCurrentNode()
    {
        if ($this->get_current_node()->get_content_object()->getId() !=
             $this->getRequest()->get(self::PARAM_CONTENT_OBJECT_ID))
        {
            throw new UserException(
                Translation::getInstance()->getTranslation('StepNoLongerValid', null, Manager::context()));
        }
    }

    /**
     * Helper function to detect the best possible node based on the current content object id
     */
    protected function detectBestPossibleNode()
    {
        $contentObjectId = $this->getRequest()->get(self::PARAM_CONTENT_OBJECT_ID);
        $nodes = $this->get_complex_content_object_path()->get_nodes();
        
        foreach ($nodes as $node)
        {
            if ($node->get_content_object()->getId() == $contentObjectId)
            {
                return $node;
            }
        }
        
        return null;
    }

    /**
     * Helper function to validate and possibly fix the current step when it became corrupt
     */
    protected function validateAndFixCurrentStep()
    {
        try
        {
            $this->validateCurrentNode();
        }
        catch (\Exception $ex)
        {
            $bestPossibleNode = $this->detectBestPossibleNode();

            if(!$bestPossibleNode)
            {
                throw new UserException(
                    Translation::getInstance()->getTranslation(
                        'CouldNotRetrieveSelectedNode', null, 'Chamilo\Core\Repository'
                    )
                );
            }

            $this->current_step = $bestPossibleNode->get_id();
        }
    }
}