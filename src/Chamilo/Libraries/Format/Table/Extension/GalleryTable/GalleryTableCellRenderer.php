<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable;

use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\Interfaces\TableRowActionsSupport;
use Chamilo\Libraries\Format\Table\TableCellRenderer;
use Chamilo\Libraries\Format\Table\TableComponent;

/**
 * This class represents a cell renderer for a gallery table
 * Refactoring from GalleryObjectTable to support the new Table structure
 *
 * @package Chamilo\Libraries\Format\Table\Extension\GalleryTable
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class GalleryTableCellRenderer extends TableCellRenderer
{

    /**
     *
     * @param \Chamilo\Libraries\Format\Table\Extension\GalleryTable\GalleryTable $table
     *
     * @throws \Exception
     */
    public function __construct(GalleryTable $table)
    {
        TableComponent::__construct($table);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     *
     * @return string
     */
    abstract public function renderContent($result);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     *
     * @return string
     */
    abstract public function renderTitle($result);

    /**
     * Renders a single cell
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     *
     * @return string
     */
    public function renderCell(TableColumn $column, $result): string
    {
        $html = [];

        $html[] = '<div class="panel panel-default panel-gallery">';

        $html[] = '<div class="panel-heading">';

        if ($this->getTable()->hasTableActions())
        {
            $html[] = '__CHECKBOX_PLACEHOLDER__';
        }

        $title = $this->renderTitle($result);

        $html[] = '<h3 class="panel-title" title="' . $title . '">';
        $html[] = $title;
        $html[] = '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body panel-body-thumbnail text-center">';

        $html[] = $this->renderContent($result);
        $html[] = '</div>';

        if ($this instanceof TableRowActionsSupport)
        {
            $html[] = '<div class="panel-footer">';
            $html[] = $this->get_actions($result);
            $html[] = '</div>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
