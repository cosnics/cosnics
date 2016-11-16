<?php
namespace Chamilo\Core\Repository\ContentObject\File\Form;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.content_object.file
 */

/**
 * A form to create/update a document.
 * A destinction is made between HTML documents and other documents. For HTML
 * documents an online HTML editor is used to edit the contents of the document.
 */
class FileForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        $description_options = array();
        $description_options['height'] = '100';
        $description_options['collapse_toolbar'] = true;
        parent::build_creation_form($description_options);

        $this->addElement('category', Translation::get('Properties', null, Utilities::COMMON_LIBRARIES));

        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                (int) $this->get_owner_id()));

        $this->addSingleFileDropzone(
            'file',
            array(
                'maxFilesize' => $calculator->getMaximumUploadSize(),
                'titleInputName' => ContentObject::PROPERTY_TITLE));

        $this->addRule('file', Translation::get('DiskQuotaExceeded', null, Utilities::COMMON_LIBRARIES), 'disk_quota');

        $calculator->addUploadWarningToForm($this);

        $this->addFormRule(array($this, 'check_document_form'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        $description_options = array();
        $description_options['height'] = '100';
        $description_options['collapse_toolbar'] = true;
        parent::build_editing_form($description_options);

        $this->addElement('category', Translation::get('Properties', null, Utilities::COMMON_LIBRARIES));

        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User::class_name(),
                (int) $this->get_owner_id()));

        /** @var File $content_object */
        $content_object = $this->get_content_object();

        $this->add_information_message(
            'current_selected_file',
            '',
            Translation::getInstance()->getTranslation(
                'CurrentlySelectedFile',
                array(
                    'FILENAME' => $content_object->get_filename(),
                    'FILESIZE' => Filesystem::format_file_size($content_object->get_filesize()))));

        $this->addSingleFileDropzone(
            'file',
            array(
                'maxFilesize' => $calculator->getMaximumUploadSize(),
                'titleInputName' => ContentObject::PROPERTY_TITLE));

        $javascriptHtml = array();

        $javascriptHtml[] = '<script type="text/javascript">';
        $javascriptHtml[] = '$(document).ready(function() {';
        $javascriptHtml[] = '$(\'button[type=submit]\').prop(\'disabled\', false)';
        $javascriptHtml[] = '});';
        $javascriptHtml[] = '</script>';

        $this->addElement('html', implode(PHP_EOL, $javascriptHtml));

        $this->addRule('file', Translation::get('DiskQuotaExceeded', null, Utilities::COMMON_LIBRARIES), 'disk_quota');

        $calculator->addUploadWarningToForm($this);

        $this->addElement('category');
    }

    public function setDefaults($defaults = array())
    {
        $object = $this->get_content_object();
        parent::setDefaults($defaults);
    }

    public function create_content_object()
    {
        $object = new File();

        if (isset($_FILES['file']) && strlen($_FILES['file']['name']) > 0)
        {
            $object->set_filename($_FILES['file']['name']);
            $object->set_temporary_file_path($_FILES['file']['tmp_name']);
        }
        else
        {
            $fileUploadData = json_decode($this->exportValue('file_upload_data'));
            $temporaryFilePath = Path::getInstance()->getTemporaryPath('Chamilo\Libraries\Ajax\Component') .
                 $fileUploadData->temporaryFileName;

            $object->set_filename($fileUploadData->name);
            $object->set_temporary_file_path($temporaryFilePath);
        }

        $this->set_content_object($object);

        $document = parent::create_content_object();

        $owner = $this->get_owner_id();
        $owner_path = $this->get_upload_path() . $owner;

        return $document;
    }

    public function update_content_object()
    {
        /** @var File $document */
        $document = $this->get_content_object();
        $values = $this->exportValues();

        if (isset($_FILES['file']) && strlen($_FILES['file']['name']) > 0)
        {
            $document->set_filename($_FILES['file']['name']);
            $document->set_temporary_file_path($_FILES['file']['tmp_name']);
        }
        else
        {
            $fileUploadData = json_decode($this->exportValue('file_upload_data'));

            if ($fileUploadData)
            {
                $temporaryFilePath = Path::getInstance()->getTemporaryPath('Chamilo\Libraries\Ajax\Component') .
                     $fileUploadData->temporaryFileName;

                $document->set_filename($fileUploadData->name);
                $document->set_temporary_file_path($temporaryFilePath);
            }
        }

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

        $owner_id = $this->get_owner_id();

        $owner = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User::class_name(),
            (int) $owner_id);

        $calculator = new Calculator($owner);

        if (isset($_FILES['file']) && isset($_FILES['file']['error']) && $_FILES['file']['error'] != 0 &&
             $_FILES['file']['error'] != 4)
        {
            switch ($_FILES['file']['error'])
            {
                case 1 : // uploaded file exceeds the upload_max_filesize directive in php.ini
                    $errors['file'] = Translation::get('FileTooBig');
                    break;
                case 2 : // uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                    $errors['file'] = Translation::get('FileTooBig');
                    break;
                case 3 : // uploaded file was only partially uploaded
                    $errors['file'] = Translation::get('UploadIncomplete');
                    break;
            }
        }
        elseif (isset($_FILES['file']) && strlen($_FILES['file']['name']) > 0)
        {
            $size = $_FILES['file']['size'];

            if (! $calculator->canUpload($size))
            {
                $errors['file'] = Translation::get('DiskQuotaExceeded', null, Utilities::COMMON_LIBRARIES);
            }

            $array = explode('.', $_FILES['file']['name']);
            $type = $array[count($array) - 1];

            if (isset($fields['uncompress']) && $type != 'zip')
            {
                $errors['file'] = Translation::get('UncompressNotAvailableForThisFile');
            }

            if (! $fields['uncompress'] && ! $this->allow_file_type($type))
            {
                if (Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'rename_instead_of_disallow')) ==
                     1)
                {
                    $name = $_FILES['file']['name'];
                    $_FILES['file']['name'] = $name . '.' .
                         Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'replacement_extension'));
                }
                else
                {
                    $errors['file'] = Translation::get('FileTypeNotAllowed');
                }
            }
        }
        elseif (isset($fields['file_upload_data']) && ! empty($fields['file_upload_data']))
        {
            $fileUploadData = json_decode($this->exportValue('file_upload_data'));
            $temporaryFilePath = Path::getInstance()->getTemporaryPath('Chamilo\Libraries\Ajax\Component') .
                 $fileUploadData->temporaryFileName;

            $size = filesize($temporaryFilePath);

            if (! $calculator->canUpload($size))
            {
                $errors['file_upload_data'] = Translation::get('DiskQuotaExceeded', null, Utilities::COMMON_LIBRARIES);
            }
        }
        else
        {
            $errors['file'] = Translation::get('NoFileSelected');
            $errors['file_upload_data'] = Translation::get('NoFileSelected');
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

    private function allow_file_type($type)
    {
        $filtering_type = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'type_of_filtering'));

        if ($filtering_type == 'blacklist')
        {
            $blacklist = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'blacklist'));
            $items = explode(',', $blacklist);

            if (in_array($type, $items))
            {
                return false;
            }

            return true;
        }
        else
        {
            $whitelist = Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'whitelist'));
            $items = explode(',', $whitelist);

            if (in_array($type, $items))
            {
                return true;
            }

            return false;
        }
    }
}
