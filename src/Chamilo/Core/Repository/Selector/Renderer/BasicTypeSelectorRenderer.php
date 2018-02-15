<?php
namespace Chamilo\Core\Repository\Selector\Renderer;

use Chamilo\Core\Repository\Selector\TabsTypeSelectorSupport;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorOption;
use Chamilo\Core\Repository\Selector\TypeSelectorRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Core\Repository\Selector\Option\ContentObjectTypeSelectorOption;

/**
 *
 * @package Chamilo\Core\Repository\Selector
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BasicTypeSelectorRenderer extends TypeSelectorRenderer
{
    // Type selector tabs
    const TAB_MOST_USED = 'most_used';
    const TAB_EXTRA = 'extra';

    /**
     *
     * @var string[][]
     */
    private $additional_links;

    /**
     *
     * @var boolean
     */
    private $use_general_statistics;

    /**
     *
     * @param Application $parent
     * @param TypeSelector $type_selector
     */
    public function __construct(Application $parent, TypeSelector $type_selector, $additional_links = array(), 
        $use_general_statistics = false)
    {
        parent::__construct($parent, $type_selector);
        
        if (! $parent instanceof TabsTypeSelectorSupport)
        {
            throw new \Exception(
                get_class($parent) .
                     ' uses the TabsTypeSelectorRender, please implement the TabsTypeSelectorSupport interface');
        }
        
        $this->additional_links = $additional_links;
        $this->use_general_statistics = $use_general_statistics;
    }

    /**
     *
     * @return LinkTypeSelectorOption[]
     */
    public function get_additional_links()
    {
        return $this->additional_links;
    }

    /**
     *
     * @return boolean
     */
    public function has_additional_links()
    {
        return count($this->get_additional_links()) > 0;
    }

    /**
     *
     * @return boolean
     */
    public function get_use_general_statistics()
    {
        return $this->use_general_statistics;
    }

    /**
     * Render the tabs
     * 
     * @return string
     */
    public function render()
    {
        $html = array();
        
        $html[] = $this->renderMostUsedOptions();
        $html[] = $this->renderAllOptions();
        $html[] = $this->renderAdditionalLinks();
        
        return implode(PHP_EOL, $html);
    }

    protected function renderAdditionalLinks()
    {
        if ($this->has_additional_links() > 0)
        {
            return $this->renderOptions('extra-options', Translation::get('Extra'), $this->get_additional_links());
        }
    }

    protected function renderMostUsedOptions()
    {
        if ($this->get_type_selector()->count_options() > 15)
        {
            return $this->renderOptions('most-used-options', Translation::get('MostUsed'), $this->getMostOptions());
        }
    }

    protected function renderAllOptions()
    {
        $options = array();
        
        foreach ($this->get_type_selector()->get_categories() as $category)
        {
            foreach ($category->get_options() as $option)
            {
                $options[] = $option;
            }
        }
        
        $this->sortOptions($options);
        
        return $this->renderOptions('all-options', Translation::get('AllContentTypes'), $options);
    }

    protected function sortOptions(&$options)
    {
        usort(
            $options, 
            function ($option_left, $option_right)
            {
                return strcasecmp($option_left->get_name(), $option_right->get_name());
            });
    }

    protected function renderOptions($id, $title, $options)
    {
        $html = array();
        
        $html[] = '<div class="content-object-options">';
        $html[] = '<div id="' . $id . '" class="content-object-options-type">';
        $html[] = '<h4>' . $title . '</h4>';
        $html[] = '<ul class="list-group">';
        
        foreach ($options as $option)
        {
            if ($option instanceof ContentObjectTypeSelectorOption)
            {
                $url = $this->get_parent()->get_content_object_type_creation_url(
                    $option->get_template_registration_id());
            }
            else
            {
                $url = $option->get_url();
            }
            
            $html[] = $this->renderOption($option, $url);
        }

        /**
         * Add empty list group items as a css bugfix for the column layout
         */
        $restOptions = count($options) % 4;
        for($i = 0; $i < $restOptions; $i++)
        {
            $html[] = '<li class="list-group-item"></li>';
        }
        
        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Render the most used content object types tab
     * 
     * @return string
     */
    protected function getMostOptions()
    {
        if (! $this->get_use_general_statistics())
        {
            $statistics_condition = new EqualityCondition(
                new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_OWNER_ID), 
                new StaticConditionVariable($this->get_parent()->get_user_id()));
        }
        else
        {
            $statistics_condition = null;
        }
        
        $content_object_type_counts = array();
        $most_used_type_count = 0;
        
        foreach ($this->get_type_selector()->get_categories() as $category)
        {
            foreach ($category->get_options() as $option)
            {
                $conditions = array();
                
                if (! is_null($statistics_condition))
                {
                    $conditions[] = $statistics_condition;
                }
                
                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(
                        ContentObject::class_name(), 
                        ContentObject::PROPERTY_TEMPLATE_REGISTRATION_ID), 
                    new StaticConditionVariable($option->get_template_registration_id()));
                $condition = new AndCondition($conditions);
                
                $context = $option->get_template_registration()->get_content_object_type();
                $package = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($context);
                $type = $context . '\Storage\DataClass\\' .
                     (string) StringUtilities::getInstance()->createString($package)->upperCamelize();
                
                $parameters = new DataClassCountParameters($condition);
                $count = \Chamilo\Core\Repository\Storage\DataManager::count_active_content_objects($type, $parameters);
                
                if ($count > 0)
                {
                    $content_object_type_counts[serialize($option)] = $count;
                    
                    if ($count > $most_used_type_count)
                    {
                        $most_used_type_count = $count;
                    }
                }
            }
        }
        
        uasort(
            $content_object_type_counts, 
            function ($count_a, $count_b)
            {
                return $count_a < $count_b;
            });
        
        $mostUsedTypes = array_slice($content_object_type_counts, 0, 10);
        
        $options = array();
        
        foreach ($mostUsedTypes as $typeOption => $count)
        {
            $options[] = unserialize($typeOption);
        }
        
        $this->sortOptions($options);
        
        return $options;
    }

    /**
     *
     * @param TypeSelectorOption $option
     * @return string
     */
    protected function renderOption(TypeSelectorOption $option, $url)
    {
        $html = array();
        
        $html[] = '<li class="list-group-item">';
        $html[] = '<img src="' . $option->get_image_path(Theme::ICON_MEDIUM) . '" />';
        $html[] = '&nbsp;&nbsp;';
        $html[] = '<a href="' . $url . '" title="' . htmlentities($option->get_label()) . '">';
        $html[] = $option->get_label();
        $html[] = '</a>';
        $html[] = '</li>';
        
        return implode('', $html);
    }

    /**
     * Render any available additional links
     * 
     * @return string
     */
    protected function getAdditionalSubButtons()
    {
        $buttons = array();
        
        foreach ($this->get_additional_links() as $link)
        {
            $buttons[] = $this->getButton($link, $link->get_url());
        }
        
        return $buttons;
    }
}
