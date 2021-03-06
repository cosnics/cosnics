<?php
namespace Chamilo\Libraries\Support;

use Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererInterface;

/**
 *
 * @package Chamilo\Libraries\Support
 */
class DiagnoserCellRenderer implements SimpleTableCellRendererInterface
{

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererInterface::render_cell()
     */
    public function render_cell($default_property, $data)
    {
        $data = $data[$default_property];

        if (is_null($data))
        {
            $data = '-';
        }

        return $data;
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererInterface::get_properties()
     */
    public function get_properties()
    {
        return array('', 'Section', 'Setting', 'Current', 'Expected', 'Comment');
    }

    /**
     *
     * @see \Chamilo\Libraries\Format\Table\Interfaces\SimpleTableCellRendererInterface::get_prefix()
     */
    public function get_prefix()
    {
        return '';
    }
}
