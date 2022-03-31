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
     * @return string
     */
    public function get_type()
    {
        return self::EXPORT_TYPE;
    }

    /**
     * @return string
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

        echo $all;

        return ($all);
    }
}
