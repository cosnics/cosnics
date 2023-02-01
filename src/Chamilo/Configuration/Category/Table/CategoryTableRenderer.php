<?php
namespace Chamilo\Configuration\Category\Table;

use Chamilo\Configuration\Category\Manager;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Configuration\Category\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CategoryTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const CATEGORY = 'Categorie';
    public const SUBCATEGORIES = 'Subcategories';

    public const TABLE_IDENTIFIER = Manager::PARAM_CATEGORY_ID;

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();
        $category_class_name = get_class($this->get_component()->get_parent()->getCategory());

        $this->addColumn(new StaticTableColumn($translator->trans(self::CATEGORY, [])));
        $this->addColumn(new DataClassPropertyTableColumn($category_class_name, PlatformCategory::PROPERTY_NAME));

        if ($this->get_component()->get_subcategories_allowed())
        {
            $this->addColumn(new StaticTableColumn($translator->trans(self::SUBCATEGORIES)));
        }
    }

    /**
     * @param \Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory $category
     */
    protected function renderCell(TableColumn $column, $category): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();

        switch ($column->get_name())
        {
            case self::CATEGORY :
                $glyph = new FontAwesomeGlyph('folder');

                return $glyph->render();
            case PlatformCategory::PROPERTY_NAME :
                $url = $urlGenerator->fromRequest(
                    [
                        Manager::PARAM_ACTION => Manager::ACTION_BROWSE_CATEGORIES,
                        Manager::PARAM_CATEGORY_ID => $category->getId()
                    ]
                );

                return '<a href="' . $url . '" alt="' . $category->get_name() . '">' . $category->get_name() . '</a>';
            case self::SUBCATEGORIES :
                $count = $this->get_component()->get_parent()->count_categories(
                    new EqualityCondition(
                        new PropertyConditionVariable(get_class($category), PlatformCategory::PROPERTY_PARENT),
                        new StaticConditionVariable($category->get_id())
                    )
                );

                return $count;
        }

        return parent::renderCell($column, $element);
    }

    /**
     * @param \Chamilo\Configuration\Form\Storage\DataClass\Element $element
     */
    public function renderTableRowActions($element): string
    {
        $urlGenerator = $this->getUrlGenerator();
        $translator = $this->getTranslator();

        $toolbar = new Toolbar();

        $updateUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Manager::PARAM_ACTION => Manager::ACTION_UPDATE_FORM_ELEMENT,
            Manager::PARAM_DYNAMIC_FORM_ELEMENT_ID => $element->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                $updateUrl, ToolbarItem::DISPLAY_ICON
            )
        );

        $deleteUrl = $urlGenerator->fromParameters([
            Application::PARAM_CONTEXT => Manager::CONTEXT,
            Manager::PARAM_ACTION => Manager::ACTION_DELETE_FORM_ELEMENT,
            Manager::PARAM_DYNAMIC_FORM_ELEMENT_ID => $element->getId()
        ]);

        $toolbar->add_item(
            new ToolbarItem(
                $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'), $deleteUrl,
                ToolbarItem::DISPLAY_ICON, true
            )
        );

        return $toolbar->render();
    }
}
