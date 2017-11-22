<?php
namespace Chamilo\Libraries\Format\Table\Extension\GalleryTable;

use Chamilo\Libraries\Format\Table\Interfaces\TableCellRendererActionsColumnSupport;
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
     * @throws \Exception
     */
    public function __construct($table)
    {
        TableComponent::__construct($table);
    }

    /**
     * Renders a single cell
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     * @return string
     */
    public function render_cell($result)
    {
        $html = array();

        $html[] = '<div class="panel panel-default panel-gallery">';
        $html[] = '<div class="panel-body panel-body-thumbnail">';

        if ($this->get_table()->has_form_actions())
        {
            $html[] = '__CHECKBOX_PLACEHOLDER__';
        }

        $html[] = $this->renderContent($result);
        $html[] = '</div>';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">';
        $html[] = $this->renderTitle($result);
        $html[] = '</h3>';
        $html[] = '</div>';

        if ($this instanceof TableCellRendererActionsColumnSupport)
        {
            $html[] = '<div class="panel-body">';
            $html[] = $this->get_actions($result);
            $html[] = '</div>';
        }

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     * @return string
     */
    abstract public function renderContent($result);

    /**
     *
     * @param \Chamilo\Libraries\Storage\DataClass\DataClass|string[] $result
     * @return string
     */
    abstract public function renderTitle($result);
}
