<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs\Component;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Implementation\GoogleDocs\ExternalObject;
use Chamilo\Core\Repository\Implementation\GoogleDocs\Infrastructure\Service\MimeTypeExtensionParser;
use Chamilo\Core\Repository\Implementation\GoogleDocs\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;
use RuntimeException;

class InternalSyncerComponent extends Manager
{

    public function synchronize_internal_repository_object(ExternalObject $external_object)
    {
        $synchronization_data = $external_object->get_synchronization_data();
        $document = $synchronization_data->get_content_object();
        
        $document->set_title($external_object->get_title());
        if (Configuration::getInstance()->get_setting(
            array(\Chamilo\Core\Repository\Manager::context(), 'description_required')) && StringUtilities::getInstance()->isNullOrEmpty(
            $external_object->get_description()))
        {
            $document->set_description('-');
        }
        else
        {
            $document->set_description($external_object->get_description());
        }
        
        $mimeTypeExtensionParser = new MimeTypeExtensionParser();
        $exportFormat = $mimeTypeExtensionParser->getMimeTypeForExtension($document->get_extension());
        
        if (! $exportFormat)
        {
            throw new RuntimeException(
                'Can not synchronize the google docs because there is no valid export format for the current file');
        }
        
        $document->set_in_memory_file($external_object->get_content_data($exportFormat));
        
        $document->set_filename(
            Filesystem::create_safe_name($external_object->get_title()) . '.' . $document->get_extension());
        
        if ($document->update())
        {
            $synchronization_data->set_content_object_timestamp($document->get_modification_date());
            $synchronization_data->set_external_object_timestamp($external_object->get_modified());
            if ($synchronization_data->update())
            {
                $parameters = $this->get_parameters();
                $parameters[Application::PARAM_CONTEXT] = \Chamilo\Core\Repository\Manager::context();
                $parameters[Application::PARAM_ACTION] = \Chamilo\Core\Repository\Manager::ACTION_VIEW_CONTENT_OBJECTS;
                $parameters[\Chamilo\Core\Repository\Manager::PARAM_CONTENT_OBJECT_ID] = $document->get_id();
                $this->redirect(
                    Translation::get(
                        'ObjectUpdated', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        StringUtilities::LIBRARIES),
                    false, 
                    $parameters, 
                    array(Manager::PARAM_EXTERNAL_REPOSITORY));
            }
            else
            {
                $parameters = $this->get_parameters();
                $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
                $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
                $this->redirect(
                    Translation::get(
                        'ObjectFailedUpdated', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        StringUtilities::LIBRARIES),
                    true, 
                    $parameters);
            }
        }
        else
        {
            $parameters = $this->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW_EXTERNAL_REPOSITORY;
            $parameters[Manager::PARAM_EXTERNAL_REPOSITORY_ID] = $external_object->get_id();
            $this->redirect(
                Translation::get(
                    'ObjectFailedUpdated', 
                    array('OBJECT' => Translation::get('ContentObject')), 
                    StringUtilities::LIBRARIES),
                true, 
                $parameters);
        }
    }
}
