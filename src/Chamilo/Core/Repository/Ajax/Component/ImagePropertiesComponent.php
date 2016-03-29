<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\Platform\Session\Request;

/**
 *
 * @package Chamilo\Core\Repository\Ajax\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ImagePropertiesComponent extends \Chamilo\Core\Repository\Ajax\Manager
{

    public function run()
    {
        $object = Request :: post('content_object');
        $object = DataManager :: retrieve_by_id(ContentObject :: class_name(), $object);

        $full_path = $object->get_full_path();
        $dimensions = getimagesize($full_path);

        $properties = array();
        $properties[ContentObject :: PROPERTY_ID] = $object->get_id();
        $properties[ContentObject :: PROPERTY_TITLE] = $object->get_title();
        $properties['fullPath'] = $full_path;
        $properties['webPath'] = \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $object->get_id(),
            $object->calculate_security_code());

        $properties[File :: PROPERTY_FILENAME] = $object->get_filename();
        $properties[File :: PROPERTY_PATH] = $object->get_path();
        $properties[File :: PROPERTY_FILESIZE] = $object->get_filesize();

        $properties['width'] = $dimensions[0];
        $properties['height'] = $dimensions[1];
        $properties['type'] = $object->get_extension();

        $jsonAjaxResult = new JsonAjaxResult();
        $jsonAjaxResult->set_properties($properties);
        $jsonAjaxResult->display();
    }
}