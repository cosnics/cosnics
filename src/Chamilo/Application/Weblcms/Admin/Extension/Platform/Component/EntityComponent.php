<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\PlatformGroupEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\UserEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Table\EntityTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class EntityComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getUser()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }

        $this->set_parameter(self::PARAM_ENTITY_TYPE, $this->get_selected_entity_type());

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getLinkTabsRenderer()->render($this->get_tabs(self::ACTION_ENTITY), $this->get_entity_tabs());
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getEntityCondition(): EqualityCondition
    {
        return new EqualityCondition(
            new PropertyConditionVariable(Admin::class, Admin::PROPERTY_ENTITY_TYPE),
            new StaticConditionVariable($this->get_selected_entity_type())
        );
    }

    public function getEntityTableRenderer(): EntityTableRenderer
    {
        return $this->getService(EntityTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \Exception
     */
    public function get_entity_tabs(): string
    {
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
                        $entity_type::ENTITY_TYPE, $this->getTranslator()->trans(
                        $this->getStringUtilities()->createString($entity_type::ENTITY_NAME)->upperCamelize()
                            ->__toString()
                    ), $glyph, $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_ENTITY,
                            self::PARAM_ENTITY_TYPE => $entity_type::ENTITY_TYPE
                        ]
                    ), $this->get_selected_entity_type() == $entity_type::ENTITY_TYPE
                    )
                );
            }
        }

        return $this->getLinkTabsRenderer()->render($tabs, $this->renderTable());
    }

    /**
     * @throws \TableException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $helperClass = $this->get_selected_entity_class(true);

        $totalNumberOfItems = $helperClass::count_table_data($this->getEntityCondition());
        $entityTableRenderer = $this->getEntityTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $entityTableRenderer->getParameterNames(), $entityTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $entities = $helperClass::retrieve_table_data(
            $this->getEntityCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
            $tableParameterValues->getOffset(), $entityTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $entityTableRenderer->legacyRender($this, $tableParameterValues, $entities);
    }
}
