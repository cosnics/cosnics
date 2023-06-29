<?php
namespace Chamilo\Core\Repository\Table;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * @package Chamilo\Core\Repository\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DoublesTableRenderer extends DoublesDetailsTableRenderer implements TableRowActionsSupport
{
    public const PROPERTY_DUPLICATES = 'duplicates';

    protected function initializeColumns(): void
    {
        parent::initializeColumns();

        $duplicatesGlyph =
            new FontAwesomeGlyph('clone', [], $this->getTranslator()->trans('Duplicates', [], Manager::CONTEXT));
        $this->addColumn(new StaticTableColumn(self::PROPERTY_DUPLICATES, $duplicatesGlyph->render()));
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $contentObject): string
    {
        switch ($column->get_name())
        {
            case self::PROPERTY_DUPLICATES :
                $conditions = [];

                $conditions[] = new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_CONTENT_HASH),
                    new StaticConditionVariable($contentObject->get_content_hash())
                );

                $condition = new AndCondition($conditions);

                return (string) DataManager::count_active_content_objects(
                    ContentObject::class, new DataClassCountParameters($condition)
                );
        }

        return parent::renderCell($column, $resultPosition, $contentObject);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $contentObject): string
    {
        $toolbar = new Toolbar();

        $toolbar->add_item(
            new ToolbarItem(
                $this->getTranslator()->trans('ViewItem', [], Manager::CONTEXT), new FontAwesomeGlyph('folder'),
                $this->getUrlGenerator()->fromRequest([Manager::PARAM_CONTENT_OBJECT_ID => $contentObject->getId()]),
                ToolbarItem::DISPLAY_ICON
            )
        );

        return $toolbar->render();
    }
}
