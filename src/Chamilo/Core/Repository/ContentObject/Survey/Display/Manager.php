<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Display;

use Chamilo\Core\Repository\ContentObject\Survey\Service\AnswerServiceFactory;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Platform\Session\Request;

abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const ANSWER_SERVICE_KEY = 'answerService';
    const ACTION_ACTIVITY = 'Activity';
    const ACTION_MOVE = 'Mover';
    const ACTION_SORT = 'Sorter';
    const ACTION_MANAGER = 'Manager';
    const ACTION_CREATE_CONFIGURATION = 'ConfigurationCreator';
    const ACTION_QUESTION_MANAGER = 'QuestionManager';
    const ACTION_DELETE_CONFIGURATION = 'ConfigurationDeleter';
    const ACTION_UPDATE_CONFIG = 'ConfigUpdater';
    const ACTION_CHANGE_QUESTION_VISIBILITY = 'VisibilityChanger';
    
    // Parameters
    const PARAM_STEP = 'step';
    const PARAM_PAGE_ITEM_ID = 'page_item_id';
    const PARAM_VIRTUAL_USER_ID = 'virtual_user_id';
    const PARAM_SORT = 'sort';
    const PARAM_AJAX_CONTEXT = 'ajax_context';
    const PARAM_CONFIGURATION_ID = 'config_id';
    const PARAM_COMPLEX_QUESTION_ITEM_ID = 'complex_question_item_id';
    
    // Sorting
    const SORT_UP = 'Up';
    const SORT_DOWN = 'Down';
    
    // Default action
    const DEFAULT_ACTION = self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

    /**
     *
     * @var int
     */
    private $current_step;

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        if ($applicationConfiguration->get(self::ANSWER_SERVICE_KEY) == null)
        {
            $answerServiceFactory = new AnswerServiceFactory('Chamilo\Core\Repository\ContentObject\Survey');
            $answerService = $answerServiceFactory->getAnswerService();
            $applicationConfiguration->set(self::ANSWER_SERVICE_KEY, $answerService);
        }
        parent::__construct($applicationConfiguration);
    }

    /**
     * Get the id of the currently requested step
     * 
     * @return int
     */
    public function get_current_step()
    {
        if (! isset($this->current_step))
        {
            $this->current_step = Request::get(self::PARAM_STEP) ? Request::get(self::PARAM_STEP) : 1;
            if (is_array($this->current_step))
            {
                $this->current_step = $this->current_step[0];
            }
        }
        
        return $this->current_step;
    }

    /**
     * Get the content object linked to the current step
     * 
     * @return \core\repository\ContentObject
     */
    public function get_current_content_object()
    {
        return $this->get_current_node()->get_content_object();
    }

    /**
     * Get the complex content object item linked to the current step
     * 
     * @return \core\repository\storage\data_class\ComplexContentObjectItem
     */
    public function get_current_complex_content_object_item()
    {
        return $this->get_current_node()->get_complex_content_object_item();
    }

    public function get_current_complex_content_object_path_node()
    {
        return $this->get_parent()->get_root_content_object()->get_complex_content_object_path()->get_node(
            $this->get_current_step());
    }

    public function count_steps()
    {
        return $this->get_parent()->get_root_content_object()->get_complex_content_object_path()->count_nodes();
    }

    /**
     * Get the node linked to the current step
     * 
     * @return \core\repository\common\path\ComplexContentObjectPathNode
     */
    public function get_current_node()
    {
        return $this->get_parent()->get_root_content_object()->get_complex_content_object_path()->get_node(
            $this->get_current_step());
    }

    public function get_complex_content_object_path()
    {
        return $this->get_parent()->get_root_content_object()->get_complex_content_object_path();
    }
}
?>