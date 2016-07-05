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
        $contentObject = $this->getContentObject();

        if ($contentObject instanceof File && $contentObject->is_image())
        {
            $full_path = $contentObject->get_full_path();

            $properties = array();

            $properties[ContentObject::PROPERTY_ID] = $contentObject->get_id();
            $properties[ContentObject::PROPERTY_TITLE] = $contentObject->get_title();

            $properties[File::PROPERTY_FILENAME] = $contentObject->get_filename();
            $properties[File::PROPERTY_PATH] = $contentObject->get_path();
            $properties[File::PROPERTY_FILESIZE] = $contentObject->get_filesize();

            $properties['fullPath'] = $full_path;
            $properties['webPath'] = \Chamilo\Core\Repository\Manager::get_document_downloader_url(
                $contentObject->get_id(),
                $contentObject->calculate_security_code());
            $properties['type'] = $contentObject->get_extension();

            $dimensions = getimagesize($full_path);

            $properties['width'] = $dimensions[0];
            $properties['height'] = $dimensions[1];

            $jsonAjaxResult = new JsonAjaxResult();
            $jsonAjaxResult->set_properties($properties);
            $jsonAjaxResult->display();
        }
        else
        {
            JsonAjaxResult::general_error();
        }
    }

    /**
     *
     * @return \Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File
     */
    public function getContentObject()
    {
        $contentObjectId = Request::post('content_object');
        return DataManager::retrieve_by_id(ContentObject::class_name(), $contentObjectId);
    }
}