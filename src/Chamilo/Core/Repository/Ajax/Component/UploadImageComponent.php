<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\NoAuthenticationSupport;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\StringUtilities;

class UploadImageComponent extends \Chamilo\Core\Repository\Ajax\Manager implements NoAuthenticationSupport
{

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(\Chamilo\Core\User\Manager::PARAM_USER_USER_ID);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        if (! empty($_FILES))
        {
            
            $upload_path = Path::getInstance()->getRepositoryPath();
            $owner = $this->getPostDataValue(\Chamilo\Core\User\Manager::PARAM_USER_USER_ID);
            
            $filename = $_FILES['Filedata']['name'];
            $hash = md5($_FILES['Filedata']['name']);
            
            $path = $owner . '/' . Text::char_at($hash, 0);
            $full_path = $upload_path . $path;
            
            Filesystem::create_dir($full_path);
            $hash = Filesystem::create_unique_name($full_path, $hash);
            
            $path = $path . '/' . $hash;
            $full_path = $full_path . '/' . $hash;
            
            move_uploaded_file($_FILES['Filedata']['tmp_name'], $full_path) or die(
                'Failed to create "' . $full_path . '"');
            
            $document = new File();
            $document->set_owner_id($owner);
            $document->set_parent_id(0);
            $document->set_storage_path($upload_path);
            $document->set_path($path);
            $document->set_filename($filename);
            $document->set_filesize(Filesystem::get_disk_space($full_path));
            $document->set_hash($hash);
            
            $title_parts = explode('.', $filename);
            $extension = array_pop($title_parts);
            
            $title = (string) StringUtilities::getInstance()->createString(implode('_', $title_parts))->humanize()->toTitleCase();
            
            $document->set_title($title);
            $document->set_description($title);
            $document->create();
            
            $dimensions = getimagesize($full_path);
            
            $path = Path::getInstance()->getBasePath(true) . Manager::get_document_downloader_url($document->get_id());
            
            $properties = array();
            $properties[ContentObject::PROPERTY_ID] = $document->get_id();
            $properties[ContentObject::PROPERTY_TITLE] = $document->get_title();
            $properties['fullPath'] = $full_path;
            $properties['webPath'] = \Chamilo\Core\Repository\Manager::get_document_downloader_url(
                $document->get_id(), 
                $document->calculate_security_code());
            $properties[File::PROPERTY_FILENAME] = $document->get_filename();
            $properties[File::PROPERTY_PATH] = $document->get_path();
            $properties[File::PROPERTY_FILESIZE] = $document->get_filesize();
            $properties['width'] = $dimensions[0];
            $properties['height'] = $dimensions[1];
            $properties['type'] = $document->get_extension();
            $properties['owner'] = $owner;
            
            $result = new JsonAjaxResult(200);
            $result->set_properties($properties);
            $result->display();
        }
    }
}
