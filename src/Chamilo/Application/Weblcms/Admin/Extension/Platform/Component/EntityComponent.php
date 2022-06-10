<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\PlatformGroupEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\UserEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Table\Entity\EntityTable;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class EntityComponent extends Manager implements TableSupport
{

    public function run()
    {
        if (!$this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $this->set_parameter(self::PARAM_ENTITY_TYPE, $this->get_selected_entity_type());

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->get_tabs(self::ACTION_ENTITY, $this->get_entity_tabs())->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    public function get_entity_tabs()
    {
        $table = new EntityTable($this);
        $content = $table->as_html();

        $tabs = new TabsCollection();

        foreach ($this->get_entity_types() as $entity_type)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
                new StaticConditionVariable($entity_type::ENTITY_TYPE)
            );
            $count = DataManager::count(Admin::class, new DataClassCountParameters($condition));

            if ($count > 0)
            {
                switch ($entity_type::ENTITY_TYPE)
                {
                    case UserEntity::ENTITY_TYPE:
                        $glyph = new FontAwesomeGlyph('user', [], null, 'fas');
                        break;
                    case PlatformGroupEntity::ENTITY_TYPE:
                        $glyph = new FontAwesomeGlyph('users', [], null, 'fas');
                        break;
                    default:
                        $glyph = '';
                        break;
                }

                $tabs->add(
                    new LinkTab(
                        $entity_type::ENTITY_TYPE, Translation::get(
                        StringUtilities::getInstance()->createString($entity_type::ENTITY_NAME)->upperCamelize()
                            ->__toString()
                    ), $glyph, $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_ENTITY,
                            self::PARAM_ENTITY_TYPE => $entity_type::ENTITY_TYPE
                        )
                    ), $this->get_selected_entity_type() == $entity_type::ENTITY_TYPE
                    )
                );
            }
        }

        return $this->getLinkTabsRenderer()->render($tabs);
    }

    public function get_table_condition($table_class_name)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($this->get_selected_entity_type())
        );

        return $condition;
    }
}
