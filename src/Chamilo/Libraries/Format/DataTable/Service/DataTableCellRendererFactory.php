<?php
namespace Chamilo\Libraries\Format\DataTable\Service;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Format\DataTable\Service
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DataTableCellRendererFactory
{

    /**
     *
     * @param string $context
     * @param string $type
     * @return \Chamilo\Libraries\Format\DataTable\DataTableCellRenderer
     */
    public function getDataTableCellRenderer($context, $type)
    {
        $stringUtilities = StringUtilities::getInstance();
        $translationUtilities = Translation::getInstance();
        $dateTimeUtilities = new DatetimeUtilities();

        $className = $context . '\\' . $type . 'DataTableCellRenderer';
        return new $className($stringUtilities, $translationUtilities, $dateTimeUtilities);
    }
}

