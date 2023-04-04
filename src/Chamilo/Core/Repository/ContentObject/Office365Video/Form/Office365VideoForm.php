<?php
namespace Chamilo\Core\Repository\ContentObject\Office365Video\Form;

use Chamilo\Core\Repository\ContentObject\Office365Video\Storage\DataClass\Office365Video;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Instance\Storage\DataClass\SynchronizationData;
use Chamilo\Libraries\Translation\Translation;

class Office365VideoForm extends ContentObjectForm
{

    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);
        $this->addElement('category', Translation::get('Properties'));
        
        $external_repositories = \Chamilo\Core\Repository\Instance\Manager::get_links(
            array(Office365Video::package()), 
            true);
        if ($external_repositories)
        {
            $this->addElement('static', null, null, $external_repositories);
        }
        
        $this->addElement('hidden', SynchronizationData::PROPERTY_EXTERNAL_ID);
        $this->addElement('hidden', SynchronizationData::PROPERTY_EXTERNAL_OBJECT_ID);
        
        $this->addElement('category');
    }

    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        parent::build_creation_form($htmleditor_options, $in_tab);
    }

    public function create_content_object()
    {
        $object = new Office365Video();
        $this->set_content_object($object);
        
        $success = parent::create_content_object();
        
        if ($success)
        {
            $synchronizationData = new SynchronizationData();
            
            $externalRepositoryId = (int) $this->exportValue(SynchronizationData::PROPERTY_EXTERNAL_ID);
            $synchronizationData->set_external_id($externalRepositoryId);
            
            $externalRepositoryObjectId = (string) $this->exportValue(
                SynchronizationData::PROPERTY_EXTERNAL_OBJECT_ID);
            if (empty($externalRepositoryObjectId))
            {
                return false;
            }
            $synchronizationData->set_external_object_id($externalRepositoryObjectId);
            
            $externalObject = $synchronizationData->get_external_object();
            SynchronizationData::quicksave($object, $externalObject, $externalRepositoryId);
        }
        
        return $success;
    }
}
