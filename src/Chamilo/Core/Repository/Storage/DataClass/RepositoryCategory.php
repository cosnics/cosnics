<?php
namespace Chamilo\Core\Repository\Storage\DataClass;

use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * This class describes a category for content objects in the repository
 *
 * @author Sven Vanpoucke
 */
class RepositoryCategory extends PlatformCategory
{
    public const CONTEXT = Manager::CONTEXT;

    public const PROPERTY_TYPE = 'type';
    public const PROPERTY_TYPE_ID = 'type_id';

    /**
     * Checks if the data of this object is valid + adds some default values if some data is not available
     *
     * @return bool
     */
    public function checkBeforeSave(): bool
    {
        if (StringUtilities::getInstance()->isNullOrEmpty($this->get_name()))
        {
            $this->addError(Translation::get('TitleIsRequired'));
        }

        if (!$this->get_type_id())
        {
            $this->addError(Translation::get('TypeIdIsRequired'));
        }

        if (!$this->getType())
        {
            $this->addError(Translation::get('TypeIsRequired'));
        }

        if (!$this->get_parent())
        {
            $this->set_parent(0);
        }
        else
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_ID),
                new StaticConditionVariable($this->get_parent())
            );
            $count = DataManager::count(RepositoryCategory::class, new DataClassCountParameters($condition));
            if ($count == 0)
            {
                $this->addError(Translation::get('ParentDoesNotExist'));
            }
        }

        if (!$this->get_display_order())
        {
            $this->set_display_order(
                DataManager::select_next_category_display_order(
                    $this->get_parent(), $this->get_type_id(), $this->getType()
                )
            );
        }

        $conditions = [];

        if ($this->get_id())
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_ID),
                    new StaticConditionVariable($this->get_id())
                )
            );
        }

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_NAME),
            new StaticConditionVariable($this->get_name())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_PARENT),
            new StaticConditionVariable($this->get_parent())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE_ID),
            new StaticConditionVariable($this->get_type_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_TYPE),
            new StaticConditionVariable($this->getType())
        );

        $condition = new AndCondition($conditions);
        $count = DataManager::count(RepositoryCategory::class, new DataClassCountParameters($condition));

        if ($count > 0)
        {
            $this->addError('CategoryWithSameNameExists');
        }

        return !$this->hasErrors();
    }

    /**
     * Creates this category
     *
     * @param $create_in_batch bool - Creates objects in batch without fixing the right / left values (faster)
     *
     * @return bool
     */
    public function create(): bool
    {
        $category = $this;

        // TRANSACTION
        $success = DataManager::transactional(
            function ($c) use ($category) {
                if (!$category->checkBeforeSave())
                {
                    return false;
                }

                if (!DataManager::create($category))
                {
                    $this->addError(
                        Translation::get(
                            'CouldNotCreateObjectInDatabase',
                            ['OBJECT' => Translation::get('Category'), StringUtilities::LIBRARIES]
                        )
                    );

                    return false;
                }

                return true;
            }
        );

        return $success;
    }

    /**
     * Deletes this object
     *
     * @return bool
     */
    public function delete(): bool
    {
        $category = $this;

        // TRANSACTION
        $success = DataManager::transactional(
            function ($c) use ($category) {
                if ($category->getType() == Workspace::WORKSPACE_TYPE)
                {
                    if (!DataManager::delete_workspace_category_recursive($category))
                    {
                        $category->addError(Translation::get('CouldNotDeleteCategoryInDatabase'));

                        return false;
                    }
                }
                else
                {
                    $deleted_content_objects = DataManager::retrieve_recycled_content_objects_from_category(
                        $category->get_id()
                    );

                    foreach ($deleted_content_objects as $deleted_content_object)
                    {
                        $deleted_content_object->move(0);
                    }

                    if (!DataManager::delete_category_recursive($this->getPublicationAggregator(), $category))
                    {
                        $category->addError(Translation::get('CouldNotDeleteCategoryInDatabase'));

                        return false;
                    }
                }

                return true;
            }
        );

        return $success;
    }

    /**
     * Returns the available property names
     *
     * @return string[]
     */
    public static function getDefaultPropertyNames(array $extendedPropertyNames = []): array
    {
        return [
            self::PROPERTY_TYPE_ID,
            self::PROPERTY_TYPE,
            self::PROPERTY_ID,
            self::PROPERTY_NAME,
            self::PROPERTY_PARENT,
            self::PROPERTY_DISPLAY_ORDER
        ];
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface | object
     */
    public function getPublicationAggregator()
    {
        $containerBuilder = DependencyInjectionContainerBuilder::getInstance();
        $container = $containerBuilder->createContainer();

        return $container->get(PublicationAggregator::class);
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_repository_category';
    }

    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */

    /**
     * Returns the type of this object
     *
     * @return int
     */
    public function getType()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE);
    }

    public function get_children_ids($recursive = true)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_PARENT),
            new StaticConditionVariable($this->get_id())
        );

        if (!$recursive)
        {
            $parameters = new DataClassDistinctParameters(
                $condition, new RetrieveProperties([new PropertyConditionVariable(self::class, self::PROPERTY_ID)])
            );

            return (DataManager::distinct(self::class, $parameters));
        }
        else
        {
            $children_ids = [];
            $children = DataManager::retrieve_categories($condition);

            foreach ($children as $child)
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
            return [0];
        }
        else
        {
            $parent = DataManager::retrieve_by_id(RepositoryCategory::class, $this->get_parent());

            $parent_ids = [];
            $parent_ids[] = $parent->get_id();
            $parent_ids = array_merge($parent->get_parent_ids(), $parent_ids);

            return $parent_ids;
        }
    }

    /**
     * @deprecated Use RepositoryCategory::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     * @return int
     */
    public function get_type_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_TYPE_ID);
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    public function has_children()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(self::class, self::PROPERTY_PARENT),
            new StaticConditionVariable($this->get_id())
        );

        return DataManager::count(RepositoryCategory::class, new DataClassCountParameters($condition)) > 0;
    }

    public function setType($type)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE, $type);
    }

    /**
     * @deprecated Use RepositoryCategory::setType() now
     */
    public function set_type($type)
    {
        $this->setType($type);
    }

    /**
     * @param $type_id int
     */
    public function set_type_id($type_id)
    {
        $this->setDefaultProperty(self::PROPERTY_TYPE_ID, $type_id);
    }

    /**
     * Updates this object
     *
     * @param $move bool
     *
     * @return bool
     */
    public function update($move = false): bool
    {
        $category = $this;

        // TRANSACTION
        $success = DataManager::transactional(
            function ($c) use ($move, $category) {
                if (!$category->checkBeforeSave())
                {
                    return false;
                }

                if (!DataManager::update($category))
                {
                    $category->addError(
                        Translation::get(
                            'CouldNotUpdateObjectInDatabase',
                            ['OBJECT' => Translation::get('Category'), StringUtilities::LIBRARIES]
                        )
                    );
                }

                return true;
            }
        );

        return $success;
    }
}
