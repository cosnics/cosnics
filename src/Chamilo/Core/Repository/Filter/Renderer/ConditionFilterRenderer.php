<?php
namespace Chamilo\Core\Repository\Filter\Renderer;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\FilterRenderer;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Service\TemplateRegistrationConsulter;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserViewRelContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\WorkspaceContentObjectRelation;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;
use InvalidArgumentException;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConditionFilterRenderer extends FilterRenderer
{
    public function render()
    {
        $filter_data = $this->get_filter_data();
        $conditions = [];

        // Text
        if ($filter_data->has_filter_property(FilterData::FILTER_TEXT))
        {
            if ($filter_data->has_filter_property(FilterData::FILTER_TYPE))
            {
                $type = $filter_data->get_filter_property(FilterData::FILTER_TYPE);

                if (is_numeric($type) || (is_array($type) && count($type) == 1))
                {
                    if (is_array($type))
                    {
                        $type = $type[0];
                    }

                    $template_registration =
                        $this->getTemplateRegistrationConsulter()->getTemplateRegistrationByIdentifier($type);
                    $class_name = $template_registration->get_content_object_type() . '\Storage\DataClass\\' .
                        ClassnameUtilities::getInstance()->getPackageNameFromNamespace(
                            $template_registration->get_content_object_type()
                        );
                }
                else
                {
                    $class_name = ContentObject::class;
                }
            }
            else
            {
                $class_name = ContentObject::class;
            }

            $text = $filter_data->get_filter_property(FilterData::FILTER_TEXT);
            $searchable_property_names = $class_name::get_searchable_property_names();

            $text_conditions = [];
            $text_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE), '*' . $text . '*'
            );
            $text_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION),
                '*' . $text . '*'
            );

            foreach ($searchable_property_names as $searchable_property_name)
            {
                $text_conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable($class_name, $searchable_property_name), '*' . $text . '*'
                );
            }

            $words = explode(' ', $text);
            if (count($words) > 1)
            {
                foreach ($words as $word)
                {
                    $text_conditions[] = new PatternMatchCondition(
                        new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE),
                        '*' . $word . '*'
                    );
                    $text_conditions[] = new PatternMatchCondition(
                        new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION),
                        '*' . $word . '*'
                    );

                    foreach ($searchable_property_names as $searchable_property_name)
                    {
                        $text_conditions[] = new PatternMatchCondition(
                            new PropertyConditionVariable($class_name, $searchable_property_name), '*' . $word . '*'
                        );
                    }
                }
            }
            $conditions[] = new OrCondition($text_conditions);
        }

        // Category id
        $category_id = $filter_data->get_filter_property(FilterData::FILTER_CATEGORY);

        if (isset($category_id) && $category_id >= 0)
        {
            $recursive = (boolean) $filter_data->get_filter_property(FilterData::FILTER_CATEGORY_RECURSIVE);

            if ($recursive)
            {
                if ($category_id == 0)
                {
                    // Don't set an additional condition as we are searching in the user's entire repository
                }
                else
                {
                    $category = DataManager::retrieve_by_id(RepositoryCategory::class, $category_id);

                    if ($category instanceof RepositoryCategory)
                    {
                        $category_ids = $category->get_children_ids();
                        $category_ids[] = $category_id;

                        if ($this->get_workspace() instanceof PersonalWorkspace)
                        {
                            $conditions[] = new InCondition(
                                new PropertyConditionVariable(
                                    ContentObject::class, ContentObject::PROPERTY_PARENT_ID
                                ), $category_ids
                            );
                        }
                        else
                        {
                            $conditions[] = new InCondition(
                                new PropertyConditionVariable(
                                    WorkspaceContentObjectRelation::class,
                                    WorkspaceContentObjectRelation::PROPERTY_CATEGORY_ID
                                ), $category_ids
                            );
                        }
                    }
                    else
                    {
                        $filter_data->set_filter_property(FilterData::FILTER_CATEGORY, null);
                    }
                }
            }
            else
            {
                if ($category_id == 0)
                {
                    if ($this->get_workspace() instanceof PersonalWorkspace)
                    {
                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(
                                ContentObject::class, ContentObject::PROPERTY_PARENT_ID
                            ), new StaticConditionVariable($category_id)
                        );
                    }
                    else
                    {
                        $conditions[] = new EqualityCondition(
                            new PropertyConditionVariable(
                                WorkspaceContentObjectRelation::class,
                                WorkspaceContentObjectRelation::PROPERTY_CATEGORY_ID
                            ), new StaticConditionVariable($category_id)
                        );
                    }
                }
                else
                {
                    $category = DataManager::retrieve_by_id(RepositoryCategory::class, $category_id);

                    if ($category instanceof RepositoryCategory || $category == 0)
                    {
                        if ($this->get_workspace() instanceof PersonalWorkspace)
                        {
                            $conditions[] = new EqualityCondition(
                                new PropertyConditionVariable(
                                    ContentObject::class, ContentObject::PROPERTY_PARENT_ID
                                ), new StaticConditionVariable($category_id)
                            );
                        }
                        else
                        {
                            $conditions[] = new EqualityCondition(
                                new PropertyConditionVariable(
                                    WorkspaceContentObjectRelation::class,
                                    WorkspaceContentObjectRelation::PROPERTY_CATEGORY_ID
                                ), new StaticConditionVariable($category_id)
                            );
                        }
                    }
                    else
                    {
                        $filter_data->set_filter_property(FilterData::FILTER_CATEGORY, null);
                    }
                }
            }
        }

        // Type
        if ($filter_data->has_filter_property(FilterData::FILTER_TYPE))
        {
            $types = $filter_data->get_filter_property(FilterData::FILTER_TYPE);

            if (!empty($types))
            {
                if (!is_array($types) && is_numeric($types))
                {
                    $types = array($types);
                }
                elseif (!is_array($types) && !is_string($types))
                {
                    throw new InvalidArgumentException(
                        'The given filter data "type" should be an array or a numeric value'
                    );
                }
            }
            else
            {
                $types = [];
            }

            // Category
            if (is_string($types))
            {
                $typeSelectorFactory = new TypeSelectorFactory(DataManager::get_registered_types());
                $type_selector = $typeSelectorFactory->getTypeSelector();

                try
                {
                    $types = $type_selector->get_category_by_type($types)->get_unique_content_object_template_ids();
                }
                catch (Exception $exception)
                {
                    $types = [];
                }
            }

            $conditions[] = new InCondition(
                new PropertyConditionVariable(
                    ContentObject::class, ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID
                ), $types
            );
        }

        // User view id
        if ($filter_data->has_filter_property(FilterData::FILTER_USER_VIEW))
        {
            $user_view_rel_content_objects = DataManager::retrieves(
                UserViewRelContentObject::class, new EqualityCondition(
                    new PropertyConditionVariable(
                        UserViewRelContentObject::class, UserViewRelContentObject::PROPERTY_USER_VIEW_ID
                    ), new StaticConditionVariable($filter_data->get_filter_property(FilterData::FILTER_USER_VIEW))
                )
            );

            foreach($user_view_rel_content_objects as $user_view_rel_content_object)
            {
                $visible_template_ids[] = $user_view_rel_content_object->get_content_object_template_id();
            }

            if (count($visible_template_ids) > 0)
            {
                $conditions[] = new InCondition(
                    new PropertyConditionVariable(
                        ContentObject::class, ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID
                    ), $visible_template_ids
                );
            }
        }

        // Creation date
        if ($filter_data->has_date(FilterData::FILTER_CREATION_DATE))
        {
            $creation_date_conditions = [];
            $creation_date_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_CREATION_DATE),
                ComparisonCondition::GREATER_THAN_OR_EQUAL,
                new StaticConditionVariable(strtotime($filter_data->get_creation_date(FilterData::FILTER_FROM_DATE)))
            );
            $creation_date_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_CREATION_DATE),
                ComparisonCondition::LESS_THAN_OR_EQUAL,
                new StaticConditionVariable(strtotime($filter_data->get_creation_date(FilterData::FILTER_TO_DATE)))
            );
            $conditions[] = new AndCondition($creation_date_conditions);
        }
        else
        {
            if ($filter_data->get_creation_date(FilterData::FILTER_FROM_DATE))
            {
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_CREATION_DATE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable(
                        strtotime($filter_data->get_creation_date(FilterData::FILTER_FROM_DATE))
                    )
                );
            }
            elseif ($filter_data->get_creation_date(FilterData::FILTER_TO_DATE))
            {
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_CREATION_DATE),
                    ComparisonCondition::LESS_THAN_OR_EQUAL,
                    new StaticConditionVariable(strtotime($filter_data->get_creation_date(FilterData::FILTER_TO_DATE)))
                );
            }
        }

        // Modification date
        if ($filter_data->has_date(FilterData::FILTER_MODIFICATION_DATE))
        {
            $modification_date_conditions = [];
            $modification_date_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE),
                ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable(
                    strtotime($filter_data->get_modification_date(FilterData::FILTER_FROM_DATE))
                )
            );
            $modification_date_conditions[] = new ComparisonCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE),
                ComparisonCondition::LESS_THAN_OR_EQUAL,
                new StaticConditionVariable(strtotime($filter_data->get_modification_date(FilterData::FILTER_TO_DATE)))
            );
            $conditions[] = new AndCondition($modification_date_conditions);
        }
        else
        {
            if ($filter_data->get_modification_date(FilterData::FILTER_FROM_DATE))
            {
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(
                        ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE
                    ), ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable(
                        strtotime($filter_data->get_modification_date(FilterData::FILTER_FROM_DATE))
                    )
                );
            }
            elseif ($filter_data->get_modification_date(FilterData::FILTER_TO_DATE))
            {
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(
                        ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE
                    ), ComparisonCondition::LESS_THAN_OR_EQUAL, new StaticConditionVariable(
                        strtotime($filter_data->get_modification_date(FilterData::FILTER_TO_DATE))
                    )
                );
            }
        }

        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }

    /*
     * (non-PHPdoc) @see \core\repository\FilterRenderer::render()
     */

    /**
     *
     * @param FilterData $filter_data
     *
     * @return ConditionFilterRenderer
     */
    public static function factory(FilterData $filter_data, WorkspaceInterface $workspace)
    {
        $class_name = $filter_data->get_context() . '\Filter\Renderer\ConditionFilterRenderer';

        return new $class_name($filter_data, $workspace);
    }

    /**
     * @return \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter
     * @throws \Exception
     */
    public function getTemplateRegistrationConsulter()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            TemplateRegistrationConsulter::class
        );
    }
}