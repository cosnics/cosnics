<?php
namespace Chamilo\Libraries\Architecture;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * This class represents a default Json response as provided and used by the various AJAX calls throughout Chamilo
 *
 * @package Chamilo\Libraries\Architecture
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class JsonDataClassTableResponse extends JsonResponse
{
    const PROPERTY_DATA_CLASS_TABLE = 'data_class_table';
    const PROPERTY_ROW_DATA = 'row_data';
    const PROPERTY_ROW_COUNT = 'row_count';

    /**
     *
     * @param string[] $rowData
     * @param integer $rowCount
     */
    public function __construct($rowData = [], $rowCount = 0)
    {
        $properties = [
            self::PROPERTY_DATA_CLASS_TABLE => [
                self::PROPERTY_ROW_DATA => $rowData,
                self::PROPERTY_ROW_COUNT => $rowCount]];

        parent::__construct($properties);
    }
}
