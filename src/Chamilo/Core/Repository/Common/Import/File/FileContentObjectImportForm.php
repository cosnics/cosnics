<?php
namespace Chamilo\Core\Repository\Common\Import\File;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Architecture\Application\Application;

class FileContentObjectImportForm extends ContentObjectImportForm
{
    const PARAM_DOCUMENT_TYPE = 'document_type';
    const PROPERTY_LINK = 'url';
    const DOCUMENT_UPLOAD = 0;
    const DOCUMENT_LINK = 1;

    public function build_basic_form()
    {
        parent::build_basic_form();
        
        $this->addElement(
            'radio', 
            self::PARAM_DOCUMENT_TYPE, 
            Translation::get('File'), 
            Translation::get('Upload'), 
            self::DOCUMENT_UPLOAD);
        
        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="document_upload">');
        
        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
                (int) $this->get_application()->get_user_id()));
        
        $uploadUrl = new Redirect(
            array(
                Application::PARAM_CONTEXT => \Chamilo\Core\Repository\Ajax\Manager::context(), 
                \Chamilo\Core\Repository\Ajax\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Ajax\Manager::ACTION_IMPORT_FILE));
        
        $dropZoneParameters = array(
            'name' => self::IMPORT_FILE_NAME, 
            'maxFilesize' => $calculator->getMaximumUploadSize(), 
            'uploadUrl' => $uploadUrl->getUrl(), 
            'successCallbackFunction' => 'chamilo.core.repository.import.processUploadedFile', 
            'sendingCallbackFunction' => 'chamilo.core.repository.import.prepareRequest', 
            'removedfileCallbackFunction' => 'chamilo.core.repository.import.deleteUploadedFile');
        
        if (! $this->importFormParameters->canUploadMultipleFiles())
        {
            $dropZoneParameters['maxFiles'] = 1;
        }
        
        $this->addFileDropzone(self::IMPORT_FILE_NAME, $dropZoneParameters, false);
        
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath(Manager::context(), true) . 'Plugin/jquery.file.upload.import.js'));
        
        $this->addElement('html', '</div>');
        
        $this->addElement(
            'radio', 
            self::PARAM_DOCUMENT_TYPE, 
            null, 
            Translation::getInstance()->getTranslation('ImportFromLink', null, Manager::context()), 
            self::DOCUMENT_LINK);
        
        $this->addElement('html', '<div style="padding-left: 25px; display: block;" id="document_link">');
        $this->add_textfield(self::PROPERTY_LINK, null, false);
        $this->addElement('html', '</div>');
    }

    public function setDefaults($defaults = array())
    {
        parent::setDefaults(
            array(
                self::PARAM_DOCUMENT_TYPE => self::DOCUMENT_UPLOAD, 
                self::PROPERTY_TYPE => ContentObjectImport::FORMAT_FILE, 
                self::PROPERTY_LINK => 'http://'));
    }
    
    /**
     * @return boolean
     */
    protected function implementsDropZoneSupport()
    {
        return true;
    }
}
