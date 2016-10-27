<?php
namespace Chamilo\Core\Repository\ContentObject\File\Integration\Chamilo\Core\Metadata\PropertyProvider;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Integration\Chamilo\Core\Metadata\PropertyProvider
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectPropertyProvider extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\PropertyProvider\ContentObjectPropertyProvider
{
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_FILE_EXTENSION = 'file_extension';
    const PROPERTY_FILESIZE = 'filesize';

    /**
     *
     * @see \Chamilo\Core\Metadata\Provider\PropertyProviderInterface::getEntityType()
     */
    public function getEntityType()
    {
        return File :: class_name();
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\PropertyProvider\ContentObjectPropertyProvider::getAvailableProperties()
     */
    public function getAvailableProperties()
    {
        $availableProperties = parent :: getAvailableProperties();

        $availableProperties[] = self :: PROPERTY_FILENAME;
        $availableProperties[] = self :: PROPERTY_FILE_EXTENSION;
        $availableProperties[] = self :: PROPERTY_FILESIZE;

        return $availableProperties;
    }

    /**
     *
     * @see \Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\PropertyProvider\ContentObjectPropertyProvider::renderProperty()
     */
    public function renderProperty($property, DataClass $contentObject)
    {
        switch ($property)
        {
            case self :: PROPERTY_FILE_EXTENSION :
                return $contentObject->get_extension();
        }

        return parent :: renderProperty($property, $contentObject);
    }
}