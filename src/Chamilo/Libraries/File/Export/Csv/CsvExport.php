<?php
namespace Chamilo\Libraries\File\Export\Csv;

use Chamilo\Libraries\File\Export\Export;

/**
 * Exports data to CSV-format
 *
 * @package Chamilo\Libraries\File\Export\Csv
 */
class CsvExport extends Export
{
    const EXPORT_TYPE = 'csv';

    /**
     *
     * @see \Chamilo\Libraries\File\Export\Export::get_type()
     */
    public function get_type()
    {
        return self::EXPORT_TYPE;
    }

    /**
     *
     * @see \Chamilo\Libraries\File\Export\Export::render_data()
     */
    public function render_data()
    {
        $data = $this->get_data();
        $key_array = array_keys($data[0]);
        $all = implode(';', $key_array) . PHP_EOL;
        foreach ($data as $index => $row)
        {
            $all .= implode(';', $row) . PHP_EOL;
        }

        return ($all);
    }
}
