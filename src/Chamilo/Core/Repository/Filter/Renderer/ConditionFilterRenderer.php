<?php
namespace Chamilo\Core\Repository\Filter\Renderer;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserViewRelContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ConditionFilterRenderer extends ContextFilterRenderer
{

    /**
     *
     * @param \core\repository\filter\FilterData $filter_data
     * @param int $user_id
     * @param string[] $content_object_types
     */
    public function __construct(FilterData $filter_data, $user_id, $content_object_types)
    {
        parent :: __construct($filter_data, $user_id, $content_object_types);
    }
    
    /*
     * (non-PHPdoc) @see \core\repository\FilterRenderer::render()
     */
    public function render()
    {
        $filter_data = $this->get_filter_data();
        $conditions = array();
        
        // Text
        if ($filter_data->has_filter_property(FilterData :: FILTER_TEXT))
        {
            if ($filter_data->has_filter_property(FilterData :: FILTER_TYPE))
            {
                $template_id = $this->get_type();
                $template_registration = \Chamilo\Core\Repository\Configuration :: registration_by_id(
                    $filter_data->get_filter_property(FilterData :: FILTER_TYPE));
                $class_name = $template_registration->get_content_object_type() . '\\' . ClassnameUtilities :: getInstance()->getPackageNameFromNamespace(
                    $template_registration->get_content_object_type(), 
                    true);
            }
            else
            {
                $class_name = ContentObject :: class_name();
            }
            
            $text = $filter_data->get_filter_property(FilterData :: FILTER_TEXT);
            $searchable_property_names = $class_name :: get_searchable_property_names();
            
            $text_conditions = array();
            $text_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE), 
                '*' . $text . '*');
            $text_conditions[] = new PatternMatchCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_DESCRIPTION), 
                '*' . $text . '*');
            
            foreach ($searchable_property_names as $searchable_property_name)
            {
                $text_conditions[] = new PatternMatchCondition(
                    new PropertyConditionVariable($class_name, $searchable_property_name), 
                    '*' . $text . '*');
            }
            
            $words = explode(' ', $text);
            if (count($words) > 1)
            {
                foreach ($words as $word)
                {
                    $text_conditions[] = new PatternMatchCondition(
                        new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TITLE), 
                        '*' . $word . '*');
                    $text_conditions[] = new PatternMatchCondition(
                        new PropertyConditionVariable(
                            ContentObject :: class_name(), 
                            ContentObject :: PROPERTY_DESCRIPTION), 
                        '*' . $word . '*');
                    
                    foreach ($searchable_property_names as $searchable_property_name)
                    {
                        $text_conditions[] = new PatternMatchCondition(
                            new PropertyConditionVariable($class_name, $searchable_property_name), 
                            '*' . $word . '*');
                    }
                }
            }
            $conditions[] = new OrCondition($text_conditions);
        }
        
        // Category id
        $category_id = $filter_data->get_filter_property(FilterData :: FILTER_CATEGORY);
        
        if (isset($category_id) && $category_id >= 0)
        {
            $recursive = (boolean) $filter_data->get_filter_property(FilterData :: FILTER_CATEGORY_RECURSIVE);
            
            if ($recursive)
            {
                if ($category_id == 0)
                {
                    // Don't set an additional condition as we are searching in the user's entire repository
                }
                else
                {
                    $category = DataManager :: retrieve_by_id(RepositoryCategory :: class_name(), $category_id);
                    $category_ids = $category->get_children_ids();
                    $category_ids[] = $category_id;
                    
                    $conditions[] = new InCondition(
                        new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_PARENT_ID), 
                        $category_ids);
                }
            }
            else
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_PARENT_ID), 
                    new StaticConditionVariable($category_id));
            }
        }
        
        // Type
        if ($filter_data->has_filter_property(FilterData :: FILTER_TYPE))
        {
            $type = $filter_data->get_filter_property(FilterData :: FILTER_TYPE);
            
            // Category
            if (! is_numeric($type) && ! empty($type))
            {
                $type_selector = TypeSelector :: populate(DataManager :: get_registered_types());
                
                try
                {
                    $types = $type_selector->get_category_by_type($type)->get_unique_content_object_template_ids();
                }
                catch (\Exception $exception)
                {
                    $types = array();
                }
                
                $conditions[] = new InCondition(
                    new PropertyConditionVariable(
                        ContentObject :: class_name(), 
                        ContentObject :: PROPERTY_TEMPLATE_REGISTRATION_ID), 
                    $types);
            }
            // Template id
            elseif (is_numeric($type) && ! empty($type))
            {
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObject :: class_name(), 
                        ContentObject :: PROPERTY_TEMPLATE_REGISTRATION_ID), 
                    new StaticConditionVariable($type));
            }
        }
        
        // User view id
        if ($filter_data->has_filter_property(FilterData :: FILTER_USER_VIEW))
        {
            $user_view_rel_content_objects = DataManager :: retrieves(
                UserViewRelContentObject :: class_name(), 
                new EqualityCondition(
                    new PropertyConditionVariable(
                        UserViewRelContentObject :: class_name(), 
                        UserViewRelContentObject :: PROPERTY_USER_VIEW_ID), 
                    new StaticConditionVariable($filter_data->get_filter_property(FilterData :: FILTER_USER_VIEW))));
            
            while ($user_view_rel_content_object = $user_view_rel_content_objects->next_result())
            {
                $visible_template_ids[] = $user_view_rel_content_object->get_content_object_template_id();
            }
            
            if (count($visible_template_ids) > 0)
            {
                $conditions[] = new InCondition(
                    new PropertyConditionVariable(
                        ContentObject :: class_name(), 
                        ContentObject :: PROPERTY_TEMPLATE_REGISTRATION_ID), 
                    $visible_template_ids);
            }
        }
        
        // Creation date
        if ($filter_data->has_date(FilterData :: FILTER_CREATION_DATE))
        {
            $creation_date_conditions = array();
            $creation_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_CREATION_DATE), 
                InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_creation_date(FilterData :: FILTER_FROM_DATE))));
            $creation_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_CREATION_DATE), 
                InequalityCondition :: LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_creation_date(FilterData :: FILTER_TO_DATE))));
            $conditions[] = new AndCondition($creation_date_conditions);
        }
        else
        {
            if ($filter_data->get_creation_date(FilterData :: FILTER_FROM_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_CREATION_DATE), 
                    InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                    new StaticConditionVariable(
                        strtotime($filter_data->get_creation_date(FilterData :: FILTER_FROM_DATE))));
            }
            elseif ($filter_data->get_creation_date(FilterData :: FILTER_TO_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_CREATION_DATE), 
                    InequalityCondition :: LESS_THAN_OR_EQUAL, 
                    new StaticConditionVariable(strtotime($filter_data->get_creation_date(FilterData :: FILTER_TO_DATE))));
            }
        }
        
        // Modification date
        if ($filter_data->has_date(FilterData :: FILTER_MODIFICATION_DATE))
        {
            $modification_date_conditions = array();
            $modification_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_MODIFICATION_DATE), 
                InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                new StaticConditionVariable(
                    strtotime($filter_data->get_modification_date(FilterData :: FILTER_FROM_DATE))));
            $modification_date_conditions[] = new InequalityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_MODIFICATION_DATE), 
                InequalityCondition :: LESS_THAN_OR_EQUAL, 
                new StaticConditionVariable(strtotime($filter_data->get_modification_date(FilterData :: FILTER_TO_DATE))));
            $conditions[] = new AndCondition($modification_date_conditions);
        }
        else
        {
            if ($filter_data->get_modification_date(FilterData :: FILTER_FROM_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(
                        ContentObject :: class_name(), 
                        ContentObject :: PROPERTY_MODIFICATION_DATE), 
                    InequalityCondition :: GREATER_THAN_OR_EQUAL, 
                    new StaticConditionVariable(
                        strtotime($filter_data->get_modification_date(FilterData :: FILTER_FROM_DATE))));
            }
            elseif ($filter_data->get_modification_date(FilterData :: FILTER_TO_DATE))
            {
                $conditions[] = new InequalityCondition(
                    new PropertyConditionVariable(
                        ContentObject :: class_name(), 
                        ContentObject :: PROPERTY_MODIFICATION_DATE), 
                    InequalityCondition :: LESS_THAN_OR_EQUAL, 
                    new StaticConditionVariable(
                        strtotime($filter_data->get_modification_date(FilterData :: FILTER_TO_DATE))));
            }
        }
        
        if (count($conditions) > 0)
        {
            return new AndCondition($conditions);
        }
    }

    /**
     *
     * @param \core\repository\filter\FilterData $filter_data
     * @param int $user_id
     * @param string[] $content_object_types
     * @param string $url;
     * @return \core\repository\filter\renderer\ConditionFilterRenderer
     */
    public static function factory(FilterData $filter_data, $user_id, $content_object_types, $url = null)
    {
        $class_name = $filter_data->get_context() . '\Filter\Renderer\ConditionFilterRenderer';
        return new $class_name($filter_data, $user_id, $content_object_types, $url);
    }
}