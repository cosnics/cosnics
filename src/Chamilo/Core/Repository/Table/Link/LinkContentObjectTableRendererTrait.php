<?php
namespace Chamilo\Core\Repository\Table\Link;

use Chamilo\Core\Repository\Service\ContentObjectUrlGenerator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Table\Column\DataClassPropertyTableColumn;
use Chamilo\Libraries\Format\Table\Column\TableColumn;
use Chamilo\Libraries\Format\Table\TableResultPosition;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Table\Link
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait LinkContentObjectTableRendererTrait
{
    protected ContentObjectUrlGenerator $contentObjectUrlGenerator;

    protected StringUtilities $stringUtilities;

    abstract protected function addColumn(TableColumn $column, ?int $index = null);

    public function getContentObjectUrlGenerator(): ContentObjectUrlGenerator
    {
        return $this->contentObjectUrlGenerator;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    protected function initializeColumns()
    {
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TYPE, null, false)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(ContentObject::class, ContentObject::PROPERTY_TITLE, null, false)
        );
        $this->addColumn(
            new DataClassPropertyTableColumn(
                ContentObject::class, ContentObject::PROPERTY_DESCRIPTION, null, false
            )
        );
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @throws \Exception
     */
    protected function renderCell(TableColumn $column, TableResultPosition $resultPosition, $contentObject): string
    {
        $stringUtilities = $this->getStringUtilities();

        switch ($column->get_name())
        {
            case ContentObject::PROPERTY_DESCRIPTION :
                return $stringUtilities->truncate($contentObject->get_description(), 50);
            case ContentObject::PROPERTY_TITLE :
                $viewUrl = $this->getContentObjectUrlGenerator()->getViewUrl($contentObject);

                return '<a href="' . $viewUrl . '">' . $stringUtilities->truncate($contentObject->get_title(), 50) .
                    '</a>';
            case ContentObject::PROPERTY_TYPE :
                return $contentObject->get_icon_image();
        }

        return parent::renderCell($column, $resultPosition, $contentObject);
    }
}