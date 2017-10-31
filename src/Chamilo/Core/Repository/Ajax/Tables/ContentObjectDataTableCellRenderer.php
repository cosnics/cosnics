<?php
namespace Chamilo\Core\Repository\Ajax\Tables;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\DataTable\DataTableCellRenderer;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Ajax\Tables
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectDataTableCellRenderer extends DataTableCellRenderer
{

    public function renderCell($column, $contentObject)
    {
        switch ($column->getName())
        {
            case $this->determineColumnName(ContentObject::PROPERTY_TITLE, ContentObject::class) :
                return $this->getStringUtilities()->truncate($contentObject->get_title(), 50, true);
            case $this->determineColumnName(ContentObject::PROPERTY_DESCRIPTION, ContentObject::class) :
                return $this->getStringUtilities()->truncate(html_entity_decode($contentObject->get_description()), 50);
            case $this->determineColumnName(ContentObject::PROPERTY_MODIFICATION_DATE, ContentObject::class) :
                return $this->getDateTimeUtilities()->format_locale_date(
                    $this->getTranslationUtilities()->getTranslation(
                        'DateTimeFormatLong',
                        null,
                        Utilities::COMMON_LIBRARIES),
                    $contentObject->get_modification_date());
        }

        return parent::renderCell($column, $contentObject);
    }
}

