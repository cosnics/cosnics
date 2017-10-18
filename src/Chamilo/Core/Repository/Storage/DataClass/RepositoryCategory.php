<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

/**
 * This class describes a category for content objects in the repository
 *
 * @author Sven Vanpoucke
 */
class RepositoryCategory extends \Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory
{

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_TYPE_ID = 'type_id';
    const PROPERTY_TYPE = 'type';

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
        $success = DataManager::transactional(
            function ($c) use ($create_in_batch, $category)
            {
                if (! $category->check_before_save())
                {
                    return false;
                }

                if (! DataManager::create($category))
                {
                    $this->add_error(
                        Translation::get(
                            'CouldNotCreateObjectInDatabase',
                            array('OBJECT' => Translation::get('Category'), Utilities::COMMON_LIBRARIES)));

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
        if (StringUtilities::getInstance()->isNullOrEmpty($this->get_name()))
        {
            $this->add_error(Translation::get('TitleIsRequired'));
        }

        if (! $this->get_type_id())
        {
            $this->add_error(Translation::get('TypeIdIsRequired'));
        }

        if (! $this->get_type())
        {
            $this->add_error(Translation::get('TypeIsRequired'));
        }

        if (! $this->get_parent())
        {
            $this->set_parent(0);
        }
        else
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_ID),
                new StaticConditionVariable($this->get_parent()));
            $count = DataManager::count(RepositoryCategory::class_name(), $condition);
            if ($count == 0)
            {
                $this->add_error(Translation::get('ParentDoesNotExist'));
            }
        }

        if (! $this->get_display_order())
        {
            $this->set_display_order(
                DataManager::select_next_category_display_order(
                    $this->get_parent(),
                    $this->get_type_id(),
                    $this->get_type()));
        }

        $conditions = array();

        if ($this->get_id())
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_ID),
                    new StaticConditionVariable($this->get_id())));
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_NAME),
            new StaticConditionVariable($this->get_name()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_PARENT),
            new StaticConditionVariable($this->get_parent()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($this->get_type_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_TYPE),
            new StaticConditionVariable($this->get_type()));

        $condition = new AndCondition($conditions);
        $count = DataManager::count(RepositoryCategory::class_name(), $condition);

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
        $success = DataManager::transactional(
            function ($c) use ($move, $category)
            {
                if (! $category->check_before_save())
                {
                    return false;
                }

                if (! DataManager::update($category))
                {
                    $category->add_error(
                        Translation::get(
                            'CouldNotUpdateObjectInDatabase',
                            array('OBJECT' => Translation::get('Category'), Utilities::COMMON_LIBRARIES)));
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
        $success = DataManager::transactional(
            function ($c) use ($category)
            {
                if ($category->get_type() == Workspace::WORKSPACE_TYPE)
                {
                    if (! DataManager::delete_workspace_category_recursive($category))
                    {
                        $category->add_error(Translation::get('CouldNotDeleteCategoryInDatabase'));
                        return false;
                    }
                }
                else
                {
                    $deleted_content_objects = DataManager::retrieve_recycled_content_objects_from_category(
                        $category->get_id());

                    while ($deleted_content_object = $deleted_content_objects->next_result())
                    {
                        $deleted_content_object->move(0);
                    }

                    if (! DataManager::delete_category_recursive($category))
                    {
                        $category->add_error(Translation::get('CouldNotDeleteCategoryInDatabase'));
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
    public static function get_default_property_names($extended_property_names = array())
    {
        return array(
            self::PROPERTY_TYPE_ID,
            self::PROPERTY_TYPE,
            self::PROPERTY_ID,
            self::PROPERTY_NAME,
            self::PROPERTY_PARENT,
            self::PROPERTY_DISPLAY_ORDER);
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     *
     * @return int
     */
    public function get_type_id()
    {
        return $this->get_default_property(self::PROPERTY_TYPE_ID);
    }

    /**
     *
     * @param $type_id int
     */
    public function set_type_id($type_id)
    {
        $this->set_default_property(self::PROPERTY_TYPE_ID, $type_id);
    }

    /**
     * Returns the type of this object
     *
     * @return int
     */
    public function get_type()
    {
        return $this->get_default_property(self::PROPERTY_TYPE);
    }

    /**
     * Sets the type of this object
     *
     * @param $type int
     */
    public function set_type($type)
    {
        $this->set_default_property(self::PROPERTY_TYPE, $type);
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    public function has_children()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_PARENT),
            new StaticConditionVariable($this->get_id()));

        return DataManager::count(RepositoryCategory::class_name(), new DataClassCountParameters($condition)) > 0;
    }

    public function get_children_ids($recursive = true)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class_name(), self::PROPERTY_PARENT),
            new StaticConditionVariable($this->get_id()));

        if (! $recursive)
        {
            $parameters = new DataClassDistinctParameters(
                $condition,
                new DataClassProperties(array(new PropertyConditionVariable(self::class, self::PROPERTY_ID))));
            return (DataManager::distinct(self::class_name(), $parameters));
        }
        else
        {
            $children_ids = array();
            $children = DataManager::retrieve_categories($condition);

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
            $parent = DataManager::retrieve_by_id(RepositoryCategory::class_name(), $this->get_parent());

            $parent_ids = array();
            $parent_ids[] = $parent->get_id();
            $parent_ids = array_merge($parent->get_parent_ids(), $parent_ids);

            return $parent_ids;
        }
    }
}
