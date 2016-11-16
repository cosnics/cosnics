<?php
namespace Chamilo\Core\Repository\Instance\Component;

use Chamilo\Core\Repository\Instance\Form\InstanceForm;
use Chamilo\Core\Repository\Instance\Manager;
use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicContentTab;
use Chamilo\Libraries\Format\Tabs\DynamicTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class CreatorComponent extends Manager
{

    public function run()
    {
        $trail = BreadcrumbTrail::getInstance();
        $trail->add_help('external_instance general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $implementation = Request::get(self::PARAM_IMPLEMENTATION);
        
        if ($implementation && self::exists($implementation))
        {
            
            $form = new InstanceForm($this);
            
            if ($form->validate())
            {
                $success = $form->create_external_instance();
                $this->redirect(
                    Translation::get(
                        $success ? 'ObjectAdded' : 'ObjectNotAdded', 
                        array('OBJECT' => Translation::get('ExternalInstance')), 
                        Utilities::COMMON_LIBRARIES), 
                    ($success ? false : true), 
                    array(self::PARAM_ACTION => self::ACTION_BROWSE));
            }
            else
            {
                $html = array();
                
                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            $instance_types = $this->get_types();
            if (count($instance_types['sections']) == 0)
            {
                $html = array();
                
                $html[] = $this->render_header();
                $html[] = $this->display_warning_message(Translation::get('NoExternalInstancesAvailable'));
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
            
            $renderer_name = ClassnameUtilities::getInstance()->getClassnameFromObject($this, true);
            $tabs = new DynamicTabsRenderer($renderer_name);
            
            foreach ($instance_types['sections'] as $category => $category_name)
            {
                $types_html = array();
                
                foreach ($instance_types['types'][$category] as $type => $registration)
                {
                    $types_html[] = '<a href="' . $this->get_url(
                        array(self::PARAM_IMPLEMENTATION => $registration->get_context())) .
                         '"><div class="create_block" style="background-image: url(' . Theme::getInstance()->getImagePath(
                            $registration->get_context(), 
                            'Logo/48') . ');">';
                    $types_html[] = Translation::get('TypeName', null, $registration->get_context());
                    $types_html[] = '</div></a>';
                }
                
                $tabs->add_tab(
                    new DynamicContentTab(
                        $category, 
                        $category_name, 
                        Theme::getInstance()->getImagePath('Chamilo/Core/Repository/External', 'Category/' . $category), 
                        implode(PHP_EOL, $types_html)));
            }
            
            $html = array();
            
            $html[] = $this->render_header();
            $html[] = $tabs->render();
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    public function get_implementation()
    {
        return Request::get(self::PARAM_IMPLEMENTATION);
    }

    public function get_types()
    {
        $active_managers = self::get_registered_types();
        $types = array();
        $sections = array();
        
        while ($active_manager = $active_managers->next_result())
        {
            $package_info = \Chamilo\Configuration\Package\Storage\DataClass\Package::get(
                $active_manager->get_context());
            
            $section = $package_info->get_category() ? $package_info->get_category() : 'various';
            
            $extra = $package_info->get_extra();
            
            if ($extra['multiple'] == 1)
            {
                
                $multiple = true;
            }
            else
            {
                $multiple = false;
            }
            
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Instance::class_name(), Instance::PROPERTY_TYPE), 
                new StaticConditionVariable($active_manager->get_context()));
            $parameters = new DataClassCountParameters($condition);
            $count = DataManager::count(Instance::class_name(), $parameters);
            
            if (! $multiple && $count > 0)
            {
                continue;
            }
            
            if (! in_array($section, array_keys($sections)))
            {
                $sections[$section] = Translation::get(
                    (string) StringUtilities::getInstance()->createString($section)->upperCamelize(), 
                    null, 
                    ClassnameUtilities::getInstance()->getNamespaceParent(
                        ClassnameUtilities::getInstance()->getNamespaceParent($package_info->get_context())));
            }
            
            if (! isset($types[$section]))
            {
                $types[$section] = array();
            }
            
            $types[$section][$active_manager->get_name()] = $active_manager;
            asort($types[$section]);
        }
        
        asort($sections);
        return array('sections' => $sections, 'types' => $types);
    }
}
