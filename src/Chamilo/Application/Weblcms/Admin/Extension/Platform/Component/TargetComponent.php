<?php
namespace Chamilo\Application\Weblcms\Admin\Extension\Platform\Component;

use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseCategoryEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Entity\CourseEntity;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Manager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataClass\Admin;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Storage\DataManager;
use Chamilo\Application\Weblcms\Admin\Extension\Platform\Table\TargetTableRenderer;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class TargetComponent extends Manager
{

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \ReflectionException
     * @throws \TableException
     */
    public function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->getLinkTabsRenderer()->render($this->get_tabs(self::ACTION_TARGET), $this->get_target_tabs());
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    public function getTargetCondition(): AndCondition
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

        return new AndCondition($conditions);
    }

    public function getTargetTableRenderer(): TargetTableRenderer
    {
        return $this->getService(TargetTableRenderer::class);
    }

    /**
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    public function get_target_tabs(): string
    {
        $tabs = new TabsCollection();

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
                $tabs->add(
                    new LinkTab(
                        $target_type::ENTITY_TYPE, $this->getTranslator()->trans(
                        $this->getStringUtilities()->createString($target_type::ENTITY_NAME)->upperCamelize()
                            ->__toString()
                    ), $glyph, $this->get_url(
                        [
                            self::PARAM_ACTION => self::ACTION_TARGET,
                            self::PARAM_ENTITY_ID => $this->get_selected_entity_id(),
                            self::PARAM_ENTITY_TYPE => $this->get_selected_entity_type(),
                            self::PARAM_TARGET_TYPE => $target_type::ENTITY_TYPE
                        ]
                    ), $this->get_selected_target_type() == $target_type::ENTITY_TYPE
                    )
                );
            }
        }

        return $this->getLinkTabsRenderer()->render($tabs, $this->renderTable());
    }

    /**
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     */
    protected function renderTable(): string
    {
        $helperClass = $this->get_selected_target_class(true);

        $totalNumberOfItems = $helperClass::count_table_data($this->getTargetCondition());
        $targetTableRenderer = $this->getTargetTableRenderer();

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $targetTableRenderer->getParameterNames(), $targetTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $entities = $helperClass::retrieve_table_data(
            $this->getTargetCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
            $tableParameterValues->getOffset(), $targetTableRenderer->determineOrderBy($tableParameterValues)
        );

        return $targetTableRenderer->legacyRender($this, $tableParameterValues, $entities);
    }
}
