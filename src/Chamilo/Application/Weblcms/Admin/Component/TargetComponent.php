<?php
namespace Chamilo\Application\Weblcms\Admin\Component;

use Chamilo\Application\Weblcms\Admin\Manager;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Application\Weblcms\Admin\Storage\DataClass\Admin;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Application\Weblcms\Admin\Table\Target\TargetTable;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Application\Weblcms\Admin\Storage\DataManager;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Format\Theme;

class TargetComponent extends Manager implements TableSupport
{

    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->get_tabs(self :: ACTION_TARGET, $this->get_target_tabs())->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
        $conditions = array();

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($this->get_selected_entity_type()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_ID),
            new StaticConditionVariable($this->get_selected_entity_id()));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_TARGET_TYPE),
            new StaticConditionVariable($this->get_selected_target_type()));

        $condition = new AndCondition($conditions);

        return $condition;
    }

    public function get_target_tabs()
    {
        $current_tab = null;

        $table = new TargetTable($this);

        $tabs = new DynamicVisualTabsRenderer(
            ClassnameUtilities :: getInstance()->getClassnameFromNamespace(__NAMESPACE__, true),
            $table->as_html());

        foreach ($this->get_target_types() as $target_type)
        {
            $conditions = array();

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable($this->get_selected_entity_type()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_ENTITY_ID),
                new StaticConditionVariable($this->get_selected_entity_id()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Admin :: class_name(), Admin :: PROPERTY_TARGET_TYPE),
                new StaticConditionVariable($target_type :: ENTITY_TYPE));

            $condition = new AndCondition($conditions);

            $count = DataManager :: count(Admin :: class_name(), new DataClassCountParameters($condition));

            if ($count > 0)
            {
                $tabs->add_tab(
                    new DynamicVisualTab(
                        $target_type :: ENTITY_TYPE,
                        Translation :: get(
                            StringUtilities :: getInstance()->createString($target_type :: ENTITY_NAME)->upperCamelize()->__toString()),
                        Theme :: getInstance()->getImagePath(self :: package(), 'Target/' . $target_type :: ENTITY_TYPE),
                        $this->get_url(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_TARGET,
                                self :: PARAM_ENTITY_ID => $this->get_selected_entity_id(),
                                self :: PARAM_ENTITY_TYPE => $this->get_selected_entity_type(),
                                self :: PARAM_TARGET_TYPE => $target_type :: ENTITY_TYPE)),
                        ($this->get_selected_target_type() == $target_type :: ENTITY_TYPE ? true : false)));
            }
        }

        return $tabs->render();
    }
}
