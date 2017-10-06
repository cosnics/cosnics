<?php
namespace Chamilo\Libraries\Support;

/**
 *
 * @package common.diagnoser
 */
class DiagnoserCellRenderer
{

    public function render_cell($default_property, $data)
    {
        $data = $data[$default_property];

        if (is_null($data))
        {
            $data = '-';
        }

        return $data;
    }

    public function get_properties()
    {
        return array('', 'Section', 'Setting', 'Current', 'Expected', 'Comment');
    }

    public function get_prefix()
    {
        return '';
    }
}
