<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Core\Repository\Instance\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use InvalidArgumentException;

class ExternalObject extends \Chamilo\Core\Repository\External\ExternalObject
{
    const OBJECT_TYPE = 'google_docs';

    const PROPERTY_CONTENT = 'content';

    const PROPERTY_EXPORT_LINKS = 'export_links';

    const PROPERTY_ICON_LINK = 'icon_link';

    const PROPERTY_MODIFIER_ID = 'modifier_id';

    const PROPERTY_PREVIEW = 'preview';

    const PROPERTY_VIEWED = 'viewed';

    public function get_content()
    {
        return $this->getDefaultProperty(self::PROPERTY_CONTENT);
    }

    public function get_content_data($exportFormat)
    {
        $external_repository = DataManager::retrieve_by_id(
            Instance::class, $this->get_external_repository_id()
        );
        $downloadMethod = $this->get_export_link($exportFormat);

        return DataConnector::getInstance($external_repository)->import_external_repository_object(
            $this->get_id(), $downloadMethod
        );
    }

    public static function getDefaultPropertyNames($extendedPropertyNames = []): array
    {
        return parent::getDefaultPropertyNames(
            array(self::PROPERTY_VIEWED, self::PROPERTY_CONTENT, self::PROPERTY_MODIFIER_ID)
        );
    }

    public function get_export_link($exportFormat)
    {
        $exportLinks = $this->get_export_links();

        if (!array_key_exists($exportFormat, $exportLinks))
        {
            throw new InvalidArgumentException('Could not find a valid export link for the given format');
        }

        return $exportLinks[$exportFormat];
    }

    public function get_export_links()
    {
        return $this->getDefaultProperty(self::PROPERTY_EXPORT_LINKS);
    }

    public function get_export_types()
    {
        return array_keys($this->get_export_links());
    }

    public function get_icon_image($size = IdentGlyph::SIZE_SMALL, $isAvailable = true, $extraClasses = [])
    {
        return '<img src="' . $this->get_icon_link() . '" />';
    }

    public function get_icon_link()
    {
        return $this->getDefaultProperty(self::PROPERTY_ICON_LINK);
    }

    public function get_modifier_id()
    {
        return $this->getDefaultProperty(self::PROPERTY_MODIFIER_ID);
    }

    public static function get_object_type()
    {
        return self::OBJECT_TYPE;
    }

    public function get_preview()
    {
        return $this->getDefaultProperty(self::PROPERTY_PREVIEW);
    }

    /**
     *
     * @return string
     */
    public function get_resource_id()
    {
        return urlencode($this->get_type() . ':' . $this->get_id());
    }

    public function get_viewed()
    {
        return $this->getDefaultProperty(self::PROPERTY_VIEWED);
    }

    public function set_content($content)
    {
        return $this->setDefaultProperty(self::PROPERTY_CONTENT, $content);
    }

    public function set_export_links($export_links = [])
    {
        $this->setDefaultProperty(self::PROPERTY_EXPORT_LINKS, $export_links);
    }

    public function set_icon_link($icon_link)
    {
        $this->setDefaultProperty(self::PROPERTY_ICON_LINK, $icon_link);
    }

    public function set_modifier_id($modifier_id)
    {
        return $this->setDefaultProperty(self::PROPERTY_MODIFIER_ID, $modifier_id);
    }

    public function set_preview($preview)
    {
        return $this->setDefaultProperty(self::PROPERTY_PREVIEW, $preview);
    }

    public function set_viewed($viewed)
    {
        return $this->setDefaultProperty(self::PROPERTY_VIEWED, $viewed);
    }
}
