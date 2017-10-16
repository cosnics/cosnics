<?php
namespace Chamilo\Libraries\Format\Table\Interfaces;

/**
 *
 * @package Chamilo\Libraries\Format\Table
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface SimpleTableCellRendererInterface
{

    /**
     *
     * @param string $defaultProperty
     * @param string[] $data
     */
    public function render_cell($defaultProperty, $data);

    /**
     *
     * @return string[]
     */
    public function get_properties();

    /**
     *
     * @return string
     */
    public function get_prefix();
}
