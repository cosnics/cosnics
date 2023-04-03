<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidatorHtmlEditorOptions;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use HTML_QuickForm_Rule_Required;

/**
 *
 * @package repository.lib.content_object.document
 */

/**
 * A form to create/update a document.
 * A destinction is made between HTML documents and other documents. For HTML
 * documents an online HTML editor is used to edit the contents of the document.
 */
class WebpageForm extends ContentObjectForm
{

    protected function build_creation_form($htmleditor_options = array(), $in_tab = false)
    {
        $description_options = array();
        $description_options['height'] = '100';
        $description_options['collapse_toolbar'] = true;
        parent::build_creation_form($description_options);
        
        $this->addElement('category', Translation::get('Content', null, Utilities::COMMON_LIBRARIES));
        
        $this->addMessage(
            'warning', 
            'webpage_information', 
            '', 
            Translation::getInstance()->getTranslation(
                'WebpageInformation', 
                null, 
                'Chamilo\Core\Repository\ContentObject\Webpage'), 
            true);
        
        $this->add_html_editor(
            'html_content', 
            null, 
            false, 
            array(
                FormValidatorHtmlEditorOptions::OPTION_HEIGHT => '500', 
                FormValidatorHtmlEditorOptions::OPTION_WIDTH => '100%', 
                FormValidatorHtmlEditorOptions::OPTION_FULL_PAGE => true, 
                FormValidatorHtmlEditorOptions::OPTION_TOOLBAR => 'Webpage'));
        $this->addFormRule(array($this, 'check_document_form'));
        
        $renderer = $this->get_renderer();
        $renderer->setElementTemplate('{element}', 'html_content');
        
        $this->addElement('category');
    }

    protected function build_editing_form($htmleditor_options = array(), $in_tab = false)
    {
        $description_options = array();
        $description_options['height'] = '100';
        $description_options['collapse_toolbar'] = true;
        parent::build_editing_form($description_options);
        
        $this->addElement('category', Translation::get('Content', null, Utilities::COMMON_LIBRARIES));
        $object = $this->get_content_object();
        
        $this->addMessage(
            'warning', 
            'webpage_information', 
            '', 
            Translation::getInstance()->getTranslation(
                'WebpageInformation', 
                null, 
                'Chamilo\Core\Repository\ContentObject\Webpage'), 
            true);
        
        $this->add_html_editor(
            'html_content', 
            null, 
            false, 
            array(
                FormValidatorHtmlEditorOptions::OPTION_HEIGHT => '500', 
                FormValidatorHtmlEditorOptions::OPTION_WIDTH => '100%', 
                FormValidatorHtmlEditorOptions::OPTION_FULL_PAGE => true, 
                FormValidatorHtmlEditorOptions::OPTION_TOOLBAR => 'Webpage'));
        $this->addRule(
            'html_content', 
            Translation::get('DiskQuotaExceeded', null, Utilities::COMMON_LIBRARIES), 
            'disk_quota');
        
        $renderer = $this->get_renderer();
        $renderer->setElementTemplate('{element}', 'html_content');
        
        $this->addElement('category');
    }

    public function setDefaults($defaults = array(), $filter = null)
    {
        $object = $this->get_content_object();
        $defaults['html_content'] = file_get_contents($this->get_upload_path() . $object->get_path());
        parent::setDefaults($defaults);
    }

    public function create_content_object()
    {
        $values = $this->exportValues();
        
        $object = new Webpage();
        
        $object->set_filename($values[Webpage::PROPERTY_TITLE] . '.html');
        $renderer = new ContentObjectResourceRenderer($this, $values['html_content'], true);
        
        $object->set_in_memory_file($renderer->run());
        
        $this->set_content_object($object);
        $document = parent::create_content_object();
        
        $owner = $this->get_owner_id();
        $owner_path = $this->get_upload_path() . $owner;
        
        $permissions_new_files = Configuration::getInstance()->get_setting(
            array('Chamilo\Core\Admin', 'permissions_new_files'));
        
        return $document;
    }

    public function update_content_object()
    {
        $document = $this->get_content_object();
        $values = $this->exportValues();
        
        $document->set_filename($document->get_title() . '.htm');
        $renderer = new ContentObjectResourceRenderer($this, $values['html_content'], true);
        
        $document->set_in_memory_file($renderer->run());
        
        if ((isset($values['version']) && $values['version'] == 0) || ! isset($values['version']))
        {
            $document->set_save_as_new_version(false);
        }
        else
        {
            $document->set_save_as_new_version(true);
        }
        
        return parent::update_content_object();
    }

    protected function check_document_form($fields)
    {
        // TODO: Do the errors need htmlentities()?
        $errors = array();
        
        $owner = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(), 
            (int) $this->get_owner_id());
        
        $calculator = new Calculator($owner);
        
        // Create an HTML-document
        $file['size'] = Filesystem::guess_disk_space($fields['html_content']);
        
        if (! $calculator->canUpload($file['size']))
        {
            $errors['upload_or_create'] = Translation::get('DiskQuotaExceeded', null, Utilities::COMMON_LIBRARIES);
        }
        else
        {
            if (! HTML_QuickForm_Rule_Required::validate($fields['html_content']))
            {
                $errors['upload_or_create'] = Translation::get('NoFileCreated');
            }
        }
        
        if (count($errors) == 0)
        {
            return true;
        }
        
        return $errors;
    }

    private static function get_upload_path()
    {
        return Path::getInstance()->getRepositoryPath();
    }
}
