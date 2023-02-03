<?php
namespace Chamilo\Configuration\Category\Table;

use Chamilo\Configuration\Category\Interfaces\CategoryVisibilitySupported;
use Chamilo\Configuration\Category\Manager;
use Chamilo\Configuration\Category\Service\CategoryManagerImplementerInterface;
use Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\StaticTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Extension\DataClassListTableRenderer;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Configuration\Category\Table
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class CategoryTableRenderer extends DataClassListTableRenderer implements TableRowActionsSupport
{
    public const CATEGORY = 'Category';
    public const SUBCATEGORIES = 'Subcategories';

    public const TABLE_IDENTIFIER = Manager::PARAM_CATEGORY_ID;

    protected CategoryManagerImplementerInterface $categoryManagerImplementer;

    public function getCategoryManagerImplementer(): CategoryManagerImplementerInterface
    {
        return $this->categoryManagerImplementer;
    }

    protected function initializeColumns()
    {
        $translator = $this->getTranslator();
        $categoryManagerImplementer = $this->getCategoryManagerImplementer();
        $categoryClassName = $categoryManagerImplementer->getCategoryClassName();

        $this->addColumn(
            new StaticTableColumn(self::CATEGORY, $translator->trans(self::CATEGORY, [], $categoryClassName::context()))
        );
        $this->addColumn(new DataClassPropertyTableColumn($categoryClassName, PlatformCategory::PROPERTY_NAME));

        if ($categoryManagerImplementer->areSubcategoriesAllowed())
        {
            $this->addColumn(
                new StaticTableColumn(
                    self::SUBCATEGORIES, $translator->trans(self::SUBCATEGORIES, [], $categoryClassName::context())
                )
            );
        }
    }

    /**
     * @param \Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory $category
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $category): string
    {
        $categoryManagerImplementer = $this->getCategoryManagerImplementer();

        switch ($column->get_name())
        {
            case self::CATEGORY :
                $glyph = new FontAwesomeGlyph('folder');

                return $glyph->render();
            case PlatformCategory::PROPERTY_NAME :
                return '<a href="' . $categoryManagerImplementer->getBrowseCategoriesUrl($category) . '" alt="' .
                    $category->get_name() . '">' . $category->get_name() . '</a>';
            case self::SUBCATEGORIES :
                return (string) $categoryManagerImplementer->countSubCategories($category);
        }

        return parent::renderCell($column, $resultPosition, $category);
    }

    /**
     * @param \Chamilo\Configuration\Category\Storage\DataClass\PlatformCategory $category
     */
    public function renderTableRowActions(TableResultPosition $resultPosition, $category): string
    {
        $translator = $this->getTranslator();
        $categoryManagerImplementer = $this->getCategoryManagerImplementer();

        $toolbar = new Toolbar();

        if ($category instanceof CategoryVisibilitySupported)
        {
            if ($categoryManagerImplementer->isAllowedToChangeCategoryVisibility($category))
            {
                $glyph = new FontAwesomeGlyph('eye');
                $text = 'Visible';

                if (!$category->get_visibility())
                {
                    $glyph = new FontAwesomeGlyph('eye', ['text-muted']);
                    $text = 'Invisible';
                }

                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans($text, [], StringUtilities::LIBRARIES), $glyph,
                        $categoryManagerImplementer->getToggleVisibilityCategoryUrl($category),
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('VisibleNA', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('eye', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        if ($categoryManagerImplementer->isAllowedToEditCategory($category))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Edit', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('pencil-alt'),
                    $categoryManagerImplementer->getUpdateCategoryUrl($category), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('EditNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('pencil-alt', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($categoryManagerImplementer->supportsImpactView())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $categoryManagerImplementer->getImpactViewUrl($category), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        elseif ($categoryManagerImplementer->isAllowedToDeleteCategory($category))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('times'),
                    $categoryManagerImplementer->getDeleteCategoryUrl($category), ToolbarItem::DISPLAY_ICON, true
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('Delete', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('times', ['text-muted']), null, ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        if ($category->get_display_order() > 1)
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUp', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-up'),
                    $categoryManagerImplementer->getMoveCategoryUrl($category, - 1), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveUpNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-up', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if (!$resultPosition->isLast())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDown', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('sort-down'),
                    $categoryManagerImplementer->getMoveCategoryUrl($category), ToolbarItem::DISPLAY_ICON
                )
            );
        }
        else
        {
            $toolbar->add_item(
                new ToolbarItem(
                    $translator->trans('MoveDownNA', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('sort-down', ['text-muted']), null, ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($categoryManagerImplementer->areSubcategoriesAllowed())
        {
            if ($resultPosition->getTotalNumberOfItems() > 1)
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('Move', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal'], null, 'fas'),
                        $categoryManagerImplementer->getChangeCategoryParentUrl($category), ToolbarItem::DISPLAY_ICON
                    )
                );
            }
            else
            {
                $toolbar->add_item(
                    new ToolbarItem(
                        $translator->trans('MoveNA', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('window-restore', ['fa-flip-horizontal', 'text-muted'], null, 'fas'), null,
                        ToolbarItem::DISPLAY_ICON
                    )
                );
            }
        }

        return $toolbar->render();
    }
}
