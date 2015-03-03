<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;

class ExternalObject extends \Chamilo\Core\Repository\External\ExternalObject
{
    const OBJECT_TYPE = 'google_docs';
    const PROPERTY_VIEWED = 'viewed';
    const PROPERTY_CONTENT = 'content';
    const PROPERTY_MODIFIER_ID = 'modifier_id';
    const PROPERTY_ACL = 'acl';

    public static function get_default_property_names()
    {
        return parent :: get_default_property_names(
            array(self :: PROPERTY_VIEWED, self :: PROPERTY_CONTENT, self :: PROPERTY_MODIFIER_ID, self :: PROPERTY_ACL));
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

    public function get_acl()
    {
        return $this->get_default_property(self :: PROPERTY_ACL);
    }

    public function set_acl($acl)
    {
        return $this->set_default_property(self :: PROPERTY_ACL, $acl);
    }

    public static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    public function get_export_types()
    {
        switch ($this->get_type())
        {
            case 'document' :
                return array('pdf', 'odt', 'docx');
                break;
            case 'presentation' :
                return array('pdf', 'pptx'); // 'swf');
                break;
            case 'spreadsheet' :
                return array('pdf', 'ods', 'xlsx');
                break;
            case 'pdf' :
                // return array('pdf');
                break;
        }
    }

    /**
     *
     * @return string
     */
    public function get_resource_id()
    {
        return urlencode($this->get_type() . ':' . $this->get_id());
    }

    public function get_content_data($export_format)
    {
        switch ($this->get_type())
        {
            case 'document' :
                $url = $this->get_content() . '&format=' . $export_format;
                break;
            case 'presentation' :
                $url = $this->get_content() . '&exportFormat=' . $export_format;
                break;
            case 'spreadsheet' :
                $url = $this->get_content() . '&fmcmd=' . $export_format;
                break;
            default :
                // Get the document's content link entry.
                // return array('pdf');
                break;
        }
        
        $external_repository = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_by_id(
            Instance :: class_name(), 
            $this->get_external_repository_id());
        return DataConnector :: get_instance($external_repository)->download_external_repository_object($url);
    }
}
