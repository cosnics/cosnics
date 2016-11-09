<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;

class ExternalObject extends \Chamilo\Core\Repository\External\ExternalObject
{
    const OBJECT_TYPE = 'google_docs';
    const PROPERTY_VIEWED = 'viewed';
    const PROPERTY_CONTENT = 'content';
    const PROPERTY_MODIFIER_ID = 'modifier_id';
    const PROPERTY_PREVIEW = 'preview';
    const PROPERTY_ICON_LINK = 'icon_link';
    const PROPERTY_EXPORT_LINKS = 'export_links';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent:: get_default_property_names(
            array(self :: PROPERTY_VIEWED, self :: PROPERTY_CONTENT, self :: PROPERTY_MODIFIER_ID)
        );
    }

    public function get_viewed()
    {
        return $this->get_default_property(self :: PROPERTY_VIEWED);
    }

    public function set_viewed($viewed)
    {
        return $this->set_default_property(self :: PROPERTY_VIEWED, $viewed);
    }

    public function get_content()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT);
    }

    public function set_content($content)
    {
        return $this->set_default_property(self :: PROPERTY_CONTENT, $content);
    }

    public function get_modifier_id()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIER_ID);
    }

    public function set_modifier_id($modifier_id)
    {
        return $this->set_default_property(self :: PROPERTY_MODIFIER_ID, $modifier_id);
    }

    public function get_preview()
    {
        return $this->get_default_property(self :: PROPERTY_PREVIEW);
    }

    public function set_preview($preview)
    {
        return $this->set_default_property(self :: PROPERTY_PREVIEW, $preview);
    }

    public function get_icon_link()
    {
        return $this->get_default_property(self::PROPERTY_ICON_LINK);
    }

    public function set_icon_link($icon_link)
    {
        $this->set_default_property(self::PROPERTY_ICON_LINK, $icon_link);
    }

    public static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    public function get_export_links()
    {
        return $this->get_default_property(self::PROPERTY_EXPORT_LINKS);
    }

    public function get_export_types()
    {
        return array_keys($this->get_export_links());
    }

    public function set_export_links($export_links = array())
    {
        $this->set_default_property(self::PROPERTY_EXPORT_LINKS, $export_links);
    }

    public function get_export_link($exportFormat)
    {
        $exportLinks = $this->get_export_links();

        if(!array_key_exists($exportFormat,$exportLinks))
        {
            throw new \InvalidArgumentException('Could not find a valid export link for the given format');
        }

        return $exportLinks[$exportFormat];
    }

    /**
     *
     * @return string
     */
    public function get_resource_id()
    {
        return urlencode($this->get_type() . ':' . $this->get_id());
    }

    public function get_content_data($exportFormat)
    {
        $external_repository = \Chamilo\Core\Repository\Instance\Storage\DataManager:: retrieve_by_id(
            Instance:: class_name(),
            $this->get_external_repository_id()
        );

        $externalExportURL = $this->get_export_link($exportFormat);

        return DataConnector:: getInstance($external_repository)->import_external_repository_object(
            $externalExportURL
        );
    }

    public function get_icon_image()
    {
        return '<img src="' . $this->get_icon_link() . '" />';
    }
}
