<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\RepositoryRights;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * This class describes a category for content objects in the repository
 * 
 * @author Sven Vanpoucke
 */
class RepositoryCategory extends \Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_TYPE = 'type';
    
    /**
     * **************************************************************************************************************
     * Type Definition *
     * **************************************************************************************************************
     */
    const TYPE_NORMAL = 1;
    const TYPE_SHARED = 2;

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Creates this category
     * 
     * @param $create_in_batch boolean - Creates objects in batch without fixing the right / left values (faster)
     * @return boolean
     */
    public function create($create_in_batch = false)
    {
        $category = $this;
        
        // TRANSACTION
        $success = DataManager :: transactional(
            function ($c) use($create_in_batch, $category)
            {
                $user_id = $category->get_user_id();
                
                if (! $user_id)
                {
                    $user_id = Session :: get_user_id();
                }
                
                if (! $category->check_before_save())
                {
                    return false;
                }
                
                if (! DataManager :: create($category))
                {
                    $this->add_error(
                        Translation :: get(
                            'CouldNotCreateObjectInDatabase', 
                            array('OBJECT' => Translation :: get('Category'), Utilities :: COMMON_LIBRARIES)));
                    
                    return false;
                }
                
                $parent = $category->get_parent();
                if ($parent == 0)
                {
                    $parent_id = RepositoryRights :: get_instance()->get_user_root_id($user_id);
                }
                else
                {
                    $parent_id = RepositoryRights :: get_instance()->get_location_id_by_identifier_from_user_subtree(
                        RepositoryRights :: TYPE_USER_CATEGORY, 
                        $category->get_parent(), 
                        $user_id);
                }
                
                if (! RepositoryRights :: get_instance()->create_location_in_user_tree(
                    RepositoryRights :: TYPE_USER_CATEGORY, 
                    $category->get_id(), 
                    $parent_id, 
                    $user_id, 
                    $create_in_batch))
                {
                    $category->add_error(
                        Translation :: get('CouldNotCreateLocation'), 
                        array(), 
                        Utilities :: COMMON_LIBRARIES);
                    return false;
                }
                
                return true;
            });
        return $success;
    }

    /**
     * Checks if the data of this object is valid + adds some default values if some data is not available
     * 
     * @return boolean
     */
    public function check_before_save()
    {
        if (StringUtilities :: getInstance()->isNullOrEmpty($this->get_name()))
        {
            $this->add_error(Translation :: get('TitleIsRequired'));
        }
        
        if (! $this->get_user_id())
        {
            $user_id = Session :: get_user_id();
            if ($user_id)
            {
                $this->set_user_id($user_id);
            }
            else
            {
                $this->add_error(Translation :: get('UserIdIsRequired'));
            }
        }
        if (! $this->get_parent())
        {
            $this->set_parent(0);
        }
        else
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_ID), 
                new StaticConditionVariable($this->get_parent()));
            $count = DataManager :: count(RepositoryCategory :: class_name(), $condition);
            if ($count == 0)
            {
                $this->add_error(Translation :: get('ParentDoesNotExist'));
            }
        }
        
        if (! $this->get_display_order())
        {
            $this->set_display_order(
                DataManager :: select_next_category_display_order(
                    $this->get_parent(), 
                    $this->get_user_id(), 
                    $this->get_type()));
        }
        
        $conditions = array();
        
        if ($this->get_id())
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_ID), 
                    new StaticConditionVariable($this->get_id())));
        }
        
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_NAME), 
            new StaticConditionVariable($this->get_name()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_PARENT), 
            new StaticConditionVariable($this->get_parent()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_USER_ID), 
            new StaticConditionVariable($this->get_user_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory :: class_name(), RepositoryCategory :: PROPERTY_TYPE), 
            new StaticConditionVariable($this->get_type()));
        
        $condition = new AndCondition($conditions);
        $count = DataManager :: count(RepositoryCategory :: class_name(), $condition);
        
        if ($count > 0)
        {
            $this->add_error('CategoryWithSameNameExists');
        }
        return ! $this->has_errors();
    }

    /**
     * Updates this object
     * 
     * @param $move boolean
     *
     * @return boolean
     */
    public function update($move = false)
    {
        $category = $this;
        
        // TRANSACTION
        $success = DataManager :: transactional(
            function ($c) use($move, $category)
            {
                if (! $category->CheckBeforeSave())
                {
                    return false;
                }
                
                if (! DataManager :: update($category))
                {
                    $category->add_error(
                        Translation :: get(
                            'CouldNotUpdateObjectInDatabase', 
                            array('OBJECT' => Translation :: get('Category'), Utilities :: COMMON_LIBRARIES)));
                }
                
                if ($move)
                {
                    if ($category->get_parent())
                    {
                        $new_parent_id = RepositoryRights :: get_instance()->get_location_id_by_identifier_from_user_subtree(
                            RepositoryRights :: TYPE_USER_CATEGORY, 
                            $category->get_parent(), 
                            $category->get_user_id());
                    }
                    else
                    {
                        $new_parent_id = RepositoryRights :: get_instance()->get_user_root_id(Session :: get_user_id());
                    }
                    
                    $location = RepositoryRights :: get_instance()->get_location_by_identifier_from_users_subtree(
                        RepositoryRights :: TYPE_USER_CATEGORY, 
                        $category->get_id(), 
                        $category->get_user_id());
                    
                    if (! $location->move($new_parent_id))
                    {
                        $category->add_error(Translation :: get('CouldNotMoveLocation'));
                        return false;
                    }
                }
                
                return true;
            });
        return $success;
    }

    /**
     * Deletes this object
     * 
     * @return boolean
     */
    public function delete()
    {
        $category = $this;
        
        // TRANSACTION
        $success = DataManager :: transactional(
            function ($c) use($category)
            {
                if ($category->GetType() == $category :: TYPE_SHARED)
                {
                    if (! DataManager :: delete_share_category_recursive($category))
                    {
                        $category->add_error(Translation :: get('CouldNotDeleteCategoryInDatabase'));
                        return false;
                    }
                }
                else
                {
                    $deleted_content_objects = DataManager :: retrieve_recycled_content_objects_from_category(
                        $category->get_id());
                    
                    while ($deleted_content_object = $deleted_content_objects->next_result())
                    {
                        $deleted_content_object->move(0);
                    }
                    
                    if (! DataManager :: delete_category_recursive($category))
                    {
                        $category->add_error(Translation :: get('CouldNotDeleteCategoryInDatabase'));
                        return false;
                    }
                }
                
                $location = RepositoryRights :: get_instance()->get_location_by_identifier_from_users_subtree(
                    RepositoryRights :: TYPE_USER_CATEGORY, 
                    $category->get_id(), 
                    $category->get_user_id());
                if ($location)
                {
                    if (! $location->delete())
                    {
                        $category->add_error(Translation :: get('CouldNotDeleteLocation'));
                        return false;
                    }
                }
                
                return true;
            });
        return $success;
    }

    /**
     * Returns the available property names
     * 
     * @return string[]
     */
    public static function get_default_property_names()
    {
        return array(
            self :: PROPERTY_USER_ID, 
            self :: PROPERTY_TYPE, 
            self :: PROPERTY_ID, 
            self :: PROPERTY_NAME, 
            self :: PROPERTY_PARENT, 
            self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the user id of this object
     * 
     * @return int
     */
    public function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    /**
     * Sets the user id of this object
     * 
     * @param $user_id int
     */
    public function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    /**
     * Returns the type of this object
     * 
     * @return int
     */
    public function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Sets the type of this object
     * 
     * @param $type int
     */
    public function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    public function has_children()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_PARENT), 
            new StaticConditionVariable($this->get_id()));
        return DataManager :: count(RepositoryCategory :: class_name(), new DataClassCountParameters($condition)) > 0;
    }

    public function get_children_ids($recursive = true)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_PARENT), 
            new StaticConditionVariable($this->get_id()));
        if (! $recursive)
        {
            $parameters = new PropertyConditionVariable(self :: class_name(), self :: PROPERTY_ID, $condition);
            return (DataManager :: distinct(self :: class_name(), $parameters));
        }
        else
        {
            $children_ids = array();
            $children = DataManager :: retrieve_categories($condition);
            while ($child = $children->next_result())
            {
                $children_ids[] = $child->get_id();
                $children_ids = array_merge($children_ids, $child->get_children_ids($recursive));
            }
            return $children_ids;
        }
    }

    public function get_parent_ids()
    {
        if ($this->get_parent() == 0)
        {
            return array(0);
        }
        else
        {
            $parent = DataManager :: retrieve_by_id(ContentObject :: class_name(), $this->get_parent());
            
            $parent_ids = array();
            $parent_ids[] = $parent->get_id();
            $parent_ids = array_merge($parent->get_parent_ids(), $parent_ids);
            return $parent_ids;
        }
    }
}
