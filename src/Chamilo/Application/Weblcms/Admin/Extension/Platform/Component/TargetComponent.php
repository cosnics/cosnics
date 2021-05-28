<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseCategoryEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Table\Target\TargetTable;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class TargetComponent extends Manager implements TableSupport
{

    public function run()
    {
        if (!$this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->get_tabs(self::ACTION_TARGET, $this->get_target_tabs())->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_table_condition($table_class_name)
    {
        $conditions = [];

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($this->get_selected_entity_type())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_ID),
            new StaticConditionVariable($this->get_selected_entity_id())
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Admin::class, Admin::PROPERTY_TARGET_TYPE),
            new StaticConditionVariable($this->get_selected_target_type())
        );

        $condition = new AndCondition($conditions);

        return $condition;
    }

    public function get_target_tabs()
    {
        $current_tab = null;

        $table = new TargetTable($this);

        $tabs = new DynamicVisualTabsRenderer(
            ClassnameUtilities::getInstance()->getClassnameFromNamespace(__CLASS__, true), $table->as_html()
        );

        foreach ($this->get_target_types() as $target_type)
        {
            $conditions = [];

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable($this->get_selected_entity_type())
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_ID),
                new StaticConditionVariable($this->get_selected_entity_id())
            );
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Admin::class, Admin::PROPERTY_TARGET_TYPE),
                new StaticConditionVariable($target_type::ENTITY_TYPE)
            );

            $condition = new AndCondition($conditions);

            $count = DataManager::count(Admin::class, new DataClassCountParameters($condition));

            switch ($target_type::ENTITY_TYPE)
            {
                case CourseEntity::ENTITY_TYPE:
                    $glyph = new FontAwesomeGlyph('chalkboard', [], null, 'fas');
                    break;
                case CourseCategoryEntity::ENTITY_TYPE:
                    $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');
                    break;
                default:
                    $glyph = '';
                    break;
            }

            if ($count > 0)
            {
                $tabs->add_tab(
                    new DynamicVisualTab(
                        $target_type::ENTITY_TYPE, Translation::get(
                        StringUtilities::getInstance()->createString($target_type::ENTITY_NAME)->upperCamelize()
                            ->__toString()
                    ), $glyph, $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_TARGET,
                            self::PARAM_ENTITY_ID => $this->get_selected_entity_id(),
                            self::PARAM_ENTITY_TYPE => $this->get_selected_entity_type(),
                            self::PARAM_TARGET_TYPE => $target_type::ENTITY_TYPE
                        )
                    ), $this->get_selected_target_type() == $target_type::ENTITY_TYPE
                    )
                );
            }
        }

        return $tabs->render();
    }
}
