<?php
namespace Chamilo\Core\Metadata\Relation\Instance\Table;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Relation\Instance\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumnFactory;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\FormAction\TableAction;
use Chamilo\Libraries\Format\Table\FormAction\TableActions;
use Chamilo\Libraries\Format\Table\Interfaces\TableActionsSupport;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\ListHtmlTableRenderer;
use Chamilo\Libraries\Format\Table\Pager;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Metadata\Relation\Instance\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class RelationInstanceTableRenderer extends DataClassListTableRenderer
    implements TableRowActionsSupport, TableActionsSupport
{
    public const PROPERTY_RELATION = 'relation';
    public const PROPERTY_SOURCE = 'source';
    public const PROPERTY_TARGET = 'target';

    public const TABLE_IDENTIFIER = Manager::PARAM_RELATION_INSTANCE_ID;

    protected DataClassEntityFactory $dataClassEntityFactory;

    public function __construct(
        DataClassEntityFactory $dataClassEntityFactory, Translator $translator, UrlGenerator $urlGenerator,
        ListHtmlTableRenderer $htmlTableRenderer, Pager $pager,
        DataClassPropertyTableColumnFactory $dataClassPropertyTableColumnFactory
    )
    {
        $this->dataClassEntityFactory = $dataClassEntityFactory;

        parent::__construct(
            $translator, $urlGenerator, $htmlTableRenderer, $pager, $dataClassPropertyTableColumnFactory
        );
    }

    public function getDataClassEntityFactory(): DataClassEntityFactory
    {
        return $this->dataClassEntityFactory;
    }

    public function getTableActions(): TableActions
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();
        $actions = new TableActions(__NAMESPACE__, self::TABLE_IDENTIFIER);

        $actions->addAction(
            new TableAction(
                $urlGenerator->fromParameters(
                    [Application::PARAM_CONTEXT => Manager::CONTEXT, Manager::PARAM_ACTION => Manager::ACTION_DELETE]
                ), $translator->trans('RemoveSelected', [], StringUtilities::LIBRARIES)
            )
        );

        return $actions;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();

        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_SOURCE, $translator->trans('RelationSource', [], Manager::CONTEXT))
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_RELATION, $translator->trans('RelationRelation', [], Manager::CONTEXT))
        );
        $this->addColumn(
            new StaticTableColumn(self::PROPERTY_TARGET, $translator->trans('RelationTarget', [], Manager::CONTEXT))
        );
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\RelationInstance $relationInstance
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $relationInstance): string
    {
        $translator = $this->getTranslator();

        switch ($column->get_name())
        {
            case self::PROPERTY_SOURCE :
                return $this->renderEntityByTypeAndIdentifier(
                    $relationInstance->get_source_type(), $relationInstance->get_source_id()
                );
            case self::PROPERTY_TARGET :
                return $this->renderEntityByTypeAndIdentifier(
                    $relationInstance->get_target_type(), $relationInstance->get_target_id()
                );
            case self::PROPERTY_RELATION :
                return $relationInstance->getRelation()->getTranslationByIsocode(
                    $translator->getLocale()
                );
        }

        return parent::renderCell($column, $resultPosition, $relationInstance);
    }

    public function renderEntityByTypeAndIdentifier($entityType, $entityIdentifier = 0): string
    {
        $entityFactory = DataClassEntityFactory::getInstance();
        $entity = $entityFactory->getEntity($entityType, $entityIdentifier);

        return $entity->getName();
    }

    /**
     * @param \Chamilo\Core\Metadata\Storage\DataClass\RelationInstance $relationInstance
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $relationInstance): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Manager::PARAM_ACTION => Manager::ACTION_DELETE,
                        Manager::PARAM_RELATION_INSTANCE_ID => $relationInstance->getId()
                    ]
                ), ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
