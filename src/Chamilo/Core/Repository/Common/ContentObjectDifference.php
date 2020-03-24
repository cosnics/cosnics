<?php
namespace Chamilo\Core\Repository\Common;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Diff;

/**
 * @package Chamilo\Core\Repository\Common
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ContentObjectDifference
{

    /**
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $contentObject;

    /**
     * @var \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    private $contentObjectVersion;

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObjectVersion
     */
    public function __construct(ContentObject $contentObject, ContentObject $contentObjectVersion)
    {
        $this->contentObject = $contentObject;
        $this->contentObjectVersion = $contentObjectVersion;
    }

    /**
     * @return \Diff[]
     */
    public function compare()
    {
        $defaultPropertyNames = array(
            ContentObject::PROPERTY_TITLE,
            ContentObject::PROPERTY_DESCRIPTION,
            ContentObject::PROPERTY_MODIFICATION_DATE
        );

        $differences = array();

        foreach ($defaultPropertyNames as $defaultPropertyName)
        {
            $differences[$defaultPropertyName] = new Diff(
                $this->getVisualDefaultPropertyValue($this->getContentObjectVersion(), $defaultPropertyName),
                $this->getVisualDefaultPropertyValue($this->getContentObject(), $defaultPropertyName)
            );
        }

        foreach ($this->getAdditionalPropertyNames() as $additionalPropertyName)
        {
            $differences[$additionalPropertyName] = new Diff(
                $this->getVisualAdditionalPropertyValue($this->getContentObjectVersion(), $additionalPropertyName),
                $this->getVisualAdditionalPropertyValue($this->getContentObject(), $additionalPropertyName)
            );
        }

        return $differences;
    }

    public static function factory(ContentObject $object, ContentObject $version)
    {
        $class = $object->package() . '\\Common\\' .
            ClassnameUtilities::getInstance()->getPackageNameFromNamespace($object->package()) . 'Difference';

        return new $class($object, $version);
    }

    /**
     * @return string[]
     */
    public function getAdditionalPropertyNames()
    {
        return $this->getContentObject()->get_additional_property_names();
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObject()
    {
        return $this->contentObject;
    }

    /**
     * @return \Chamilo\Core\Repository\Storage\DataClass\ContentObject
     */
    public function getContentObjectVersion()
    {
        return $this->contentObjectVersion;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $propertyName
     *
     * @return string[]
     */
    public function getVisualAdditionalPropertyValue(ContentObject $contentObject, string $propertyName)
    {
        switch ($propertyName)
        {
            default:
                $content = $contentObject->get_additional_property($propertyName);
        }

        return explode(PHP_EOL, $content);
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     * @param string $propertyName
     *
     * @return string[]
     */
    public function getVisualDefaultPropertyValue(ContentObject $contentObject, string $propertyName)
    {
        switch ($propertyName)
        {
            case ContentObject::PROPERTY_DESCRIPTION:
                $content = strip_tags($contentObject->get_description());
                break;
            case ContentObject::PROPERTY_MODIFICATION_DATE:
                $content = DatetimeUtilities::format_locale_date(
                    Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES),
                    $contentObject->get_modification_date()
                );
                break;
            default:
                $content = $contentObject->getDefaultProperty($propertyName);
        }

        return explode(PHP_EOL, $content);
    }

}
