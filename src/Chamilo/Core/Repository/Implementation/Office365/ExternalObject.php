<?php
namespace Chamilo\Core\Repository\Implementation\Office365;

use Chamilo\Core\Repository\Instance\Storage\DataClass\Instance;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\StringUtilities;

class ExternalObject extends \Chamilo\Core\Repository\External\ExternalObject
{
    const OBJECT_TYPE = 'office365';

    const PROPERTY_MODIFIER_ID = 'modifier_id';
    const PROPERTY_URL = 'url';

    const TYPE_FILE = 'file';
    const TYPE_IMAGE = 'image';
    const TYPE_FOLDER = 'folder';

    public static function get_default_property_names($extended_property_names = array())
    {
        return parent:: get_default_property_names(array(self :: PROPERTY_MODIFIER_ID, self :: PROPERTY_URL));
    }

    public static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    public function getModifierId()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIER_ID);
    }

    public function setModifierId($modifierId)
    {
        return $this->set_default_property(self :: PROPERTY_MODIFIER_ID, $modifierId);
    }

    public function getUrl()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    public function setUrl($url)
    {
        return $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    public function get_icon_image()
    {        
        $type = $this->get_type();
        if ($type == self :: TYPE_FOLDER or $type == self :: TYPE_IMAGE or $type == self :: TYPE_FILE)
        {
            return parent :: get_icon_image();
        }
        else
        {
            $camelizedType = StringUtilities::getInstance()->createString($this->get_type())->upperCamelize();
            return '<img src="' . Theme :: getInstance()->getFileExtension($camelizedType) . '" alt="' . $camelizedType . '" title="' . $camelizedType . '"/>';
        }
    }

    public function get_content_data()
    {
        $external_repository = \Chamilo\Core\Repository\Instance\Storage\DataManager:: retrieve_by_id(
            Instance:: class_name(), $this->get_external_repository_id());
        
        return DataConnector:: getInstance($external_repository)->import_external_repository_object($this->get_id());
    }
}
