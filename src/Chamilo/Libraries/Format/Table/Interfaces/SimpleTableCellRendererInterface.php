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
     * @return string
     */
    public function get_prefix();

    /**
     *
     * @return string[]
     */
    public function get_properties();

    /**
     *
     * @param string $defaultProperty
     * @param string[] $data
     */
    public function render_cell($defaultProperty, $data);
}
