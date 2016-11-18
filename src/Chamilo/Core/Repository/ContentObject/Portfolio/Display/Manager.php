<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display;

use Chamilo\Libraries\Platform\Session\Request;

/**
 * Portfolio display manager which serves as a base for all matters related to the displaying of portfolios
 * 
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
    
    // Parameters
    const PARAM_STEP = 'step';
    const PARAM_PORTFOLIO_ITEM_ID = 'portfolio_item_id';
    const PARAM_VIRTUAL_USER_ID = 'virtual_user_id';
    const PARAM_SORT = 'sort';
    
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
}
