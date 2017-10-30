<?php
namespace Chamilo\Core\Repository\Ajax\Tables;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\DataTable\DataTableCellRenderer;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
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
            case $this->determineColumnName(ContentObject::class, ContentObject::PROPERTY_TITLE) :
                return StringUtilities::getInstance()->truncate($contentObject->get_title(), 50, true);
            case $this->determineColumnName(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION) :
                return StringUtilities::getInstance()->truncate(
                    html_entity_decode($contentObject->get_description()),
                    50);
            case $this->determineColumnName(ContentObject::class, ContentObject::PROPERTY_MODIFICATION_DATE) :
                return DatetimeUtilities::format_locale_date(
                    Translation::getInstance()->getTranslation('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
                    $contentObject->get_modification_date());
        }

        return parent::renderCell($column, $contentObject);
    }

    /**
     *
     * @param string $className
     * @param string $property
     * @return string
     */
    public function determineColumnName($className, $property)
    {
        $classNameSlug = StringUtilities::getInstance()->createString($className)->replace('\\', '_')->__toString();
        return $classNameSlug . ':' . $property;
    }
}

