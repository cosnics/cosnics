<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Table\Entity\EntityTable;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

class EntityComponent extends Manager implements TableSupport
{

    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }
        
        $this->set_parameter(self::PARAM_ENTITY_TYPE, $this->get_selected_entity_type());
        
        $html = array();
        
        $html[] = $this->render_header();
        $html[] = $this->get_tabs(self::ACTION_ENTITY, $this->get_entity_tabs())->render();
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Admin::class_name(), Admin::PROPERTY_ENTITY_TYPE), 
            new StaticConditionVariable($this->get_selected_entity_type()));
        return $condition;
    }

    public function get_entity_tabs()
    {
        $table = new EntityTable($this);
        $content = $table->as_html();
        
        $tabs = new DynamicVisualTabsRenderer(
            ClassnameUtilities::getInstance()->getClassnameFromNamespace(__CLASS__, true), 
            $table->as_html());
        
        foreach ($this->get_entity_types() as $entity_type)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Admin::class_name(), Admin::PROPERTY_ENTITY_TYPE), 
                new StaticConditionVariable($entity_type::ENTITY_TYPE));
            $count = DataManager::count(Admin::class_name(), new DataClassCountParameters($condition));
            
            if ($count > 0)
            {
                $tabs->add_tab(
                    new DynamicVisualTab(
                        $entity_type::ENTITY_TYPE, 
                        Translation::get(
                            StringUtilities::getInstance()->createString($entity_type::ENTITY_NAME)->upperCamelize()->__toString()), 
                        Theme::getInstance()->getImagePath(self::package(), 'Entity/' . $entity_type::ENTITY_TYPE), 
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_ENTITY, 
                                self::PARAM_ENTITY_TYPE => $entity_type::ENTITY_TYPE)), 
                        ($this->get_selected_entity_type() == $entity_type::ENTITY_TYPE ? true : false)));
            }
        }
        
        return $tabs->render();
    }
}
