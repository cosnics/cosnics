<?php
namespace Chamilo\Core\Repository\ContentObject\File\Form;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

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
        parent :: build_creation_form($description_options);

        $this->addElement('category', Translation :: get('Properties', null, Utilities :: COMMON_LIBRARIES));

        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                (int) $this->get_owner_id()));

        // $this->addElement('file', 'file', sprintf(Translation :: get('FileName')));
        // $this->addRule(
        // 'file',
        // Translation :: get('DiskQuotaExceeded', null, Utilities :: COMMON_LIBRARIES),
        // 'disk_quota');

        // $dropzoneHtml = array();

        // $dropzoneHtml[] = '<div id="file-upload">
        // <div class="files" id="previews">
        // <div id="template" class="thumbnail pull-left">
        // <span class="preview"><img data-dz-thumbnail /></span>
        // <div class="caption">
        // <h3 data-dz-name></h3>
        // <strong class="error text-danger" data-dz-errormessage></strong>
        // <p class="size" data-dz-size></p>
        // <div>
        // <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100"
        // aria-valuenow="0">
        // <div class="progress-bar progress-bar-success" style="width: 0%;" data-dz-uploadprogress></div>
        // </div>
        // </div>
        // <div>
        // <button data-dz-remove class="btn btn-warning cancel">
        // <i class="glyphicon glyphicon-ban-circle"></i> <span>Cancel</span>
        // </button>
        // <button data-dz-remove class="btn btn-danger delete">
        // <i class="glyphicon glyphicon-trash"></i> <span>Delete</span>
        // </button>
        // </div>
        // </div>
        // </div>
        // </div>';

        // $dropzoneHtml[] = '<div class="clearfix"></div>';
        // $dropzoneHtml[] = '<div class="panel panel-default">';
        // $dropzoneHtml[] = '<div class="panel-body">';
        // $dropzoneHtml[] = '<span class="actions"><span class="glyphicon glyphicon-upload"></span>&nbsp;<span
        // class="glyphicon glyphicon-plus-sign fileinput-button dz-clickable"></span></span>';

        // $dropzoneHtml[] = '</div>';
        // $dropzoneHtml[] = '<div class="panel-footer">';
        // $dropzoneHtml[] = Translation :: get('DropFileHereMessage');
        // $dropzoneHtml[] = '</div>';
        // $dropzoneHtml[] = '</div>';
        // $dropzoneHtml[] = '</div>';

        // $this->addElement('static', null, sprintf(Translation :: get('FileName')), implode(PHP_EOL, $dropzoneHtml));
        // $this->addElement('hidden', 'file_upload_data');

        // $calculator->addUploadWarningToForm($this);

        $this->addFileDropzone('file');

        $this->addFormRule(array($this, 'check_document_form'));
        $this->addElement('category');
//         $this->addElement(
//             'html',
//             ResourceManager :: get_instance()->get_resource_html(
//                 Path :: getInstance()->getJavascriptPath(File :: package(), true) . 'fileUpload.js'));
    }

    protected function build_editing_form()
    {
        $description_options = array();
        $description_options['height'] = '100';
        $description_options['collapse_toolbar'] = true;
        parent :: build_editing_form($description_options);

        $this->addElement('category', Translation :: get('Properties', null, Utilities :: COMMON_LIBRARIES));

        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                (int) $this->get_owner_id()));

        $postMaxSize = Filesystem :: interpret_file_size(ini_get('post_max_size'));

        $object = $this->get_content_object();

        $this->addElement('file', 'file', sprintf(Translation :: get('FileName'), $postMaxSize));

        $calculator->addUploadWarningToForm($this);

        $this->addElement('category');
    }

    public function setDefaults($defaults = array ())
    {
        $object = $this->get_content_object();
        parent :: setDefaults($defaults);
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
            $temporaryFilePath = Path :: getInstance()->getTemporaryPath('Chamilo\Libraries\Ajax\Component') .
                 $fileUploadData->temporaryFileName;

            $object->set_filename($fileUploadData->name);
            $object->set_temporary_file_path($temporaryFilePath);
        }

        $this->set_content_object($object);

        $document = parent :: create_content_object();

        $owner = $this->get_owner_id();
        $owner_path = $this->get_upload_path() . $owner;

        return $document;
    }

    public function update_content_object()
    {
        $document = $this->get_content_object();
        $values = $this->exportValues();

        if (StringUtilities :: getInstance()->hasValue($_FILES['file']['name']))
        {
            $document->set_filename($_FILES['file']['name']);
            $document->set_temporary_file_path($_FILES['file']['tmp_name']);
        }

        if ((isset($values['version']) && $values['version'] == 0) || ! isset($values['version']))
        {
            $document->set_save_as_new_version(false);
        }
        else
        {
            $document->set_save_as_new_version(true);
        }

        return parent :: update_content_object();
    }

    protected function check_document_form($fields)
    {
        // TODO: Do the errors need htmlentities()?
        $errors = array();

        $owner_id = $this->get_owner_id();

        $owner = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
            (int) $owner_id);

        $calculator = new Calculator($owner);

        if (isset($_FILES['file']) && isset($_FILES['file']['error']) && $_FILES['file']['error'] != 0)
        {
            switch ($_FILES['file']['error'])
            {
                case 1 : // uploaded file exceeds the upload_max_filesize directive in php.ini
                    $errors['file'] = Translation :: get('FileTooBig');
                    break;
                case 2 : // uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                    $errors['file'] = Translation :: get('FileTooBig');
                    break;
                case 3 : // uploaded file was only partially uploaded
                    $errors['file'] = Translation :: get('UploadIncomplete');
                    break;
            }
        }
        elseif (isset($_FILES['file']) && strlen($_FILES['file']['name']) > 0)
        {
            $size = $_FILES['file']['size'];

            if (! $calculator->canUpload($size))
            {
                $errors['file'] = Translation :: get('DiskQuotaExceeded', null, Utilities :: COMMON_LIBRARIES);
            }

            $array = explode('.', $_FILES['file']['name']);
            $type = $array[count($array) - 1];

            if (isset($fields['uncompress']) && $type != 'zip')
            {
                $errors['file'] = Translation :: get('UncompressNotAvailableForThisFile');
            }

            if (! $fields['uncompress'] && ! $this->allow_file_type($type))
            {
                if (PlatformSetting :: get('rename_instead_of_disallow') == 1)
                {
                    $name = $_FILES['file']['name'];
                    $_FILES['file']['name'] = $name . '.' . PlatformSetting :: get('replacement_extension');
                }
                else
                {
                    $errors['file'] = Translation :: get('FileTypeNotAllowed');
                }
            }
        }
        elseif (isset($fields['file_upload_data']))
        {
            $fileUploadData = json_decode($this->exportValue('file_upload_data'));
            $temporaryFilePath = Path :: getInstance()->getTemporaryPath('Chamilo\Libraries\Ajax\Component') .
                 $fileUploadData->temporaryFileName;

            $size = filesize($temporaryFilePath);

            if (! $calculator->canUpload($size))
            {
                $errors['file_upload_data'] = Translation :: get(
                    'DiskQuotaExceeded',
                    null,
                    Utilities :: COMMON_LIBRARIES);
            }
        }
        else
        {
            $errors['file'] = Translation :: get('NoFileSelected');
            $errors['file_upload_data'] = Translation :: get('NoFileSelected');
        }

        if (count($errors) == 0)
        {
            return true;
        }

        var_dump($errors);

        return $errors;
    }

    private static function get_upload_path()
    {
        return Path :: getInstance()->getRepositoryPath();
    }

    private function allow_file_type($type)
    {
        $filtering_type = PlatformSetting :: get('type_of_filtering');
        if ($filtering_type == 'blacklist')
        {
            $blacklist = PlatformSetting :: get('blacklist');
            $items = explode(',', $blacklist);
            if (in_array($type, $items))
            {
                return false;
            }

            return true;
        }
        else
        {
            $whitelist = PlatformSetting :: get('whitelist');
            $items = explode(',', $whitelist);
            if (in_array($type, $items))
            {
                return true;
            }

            return false;
        }
    }
}
