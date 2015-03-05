<?php
namespace Chamilo\Core\Repository\ContentObject\File\Form;

use Chamilo\Core\Repository\ContentObject\File\Filter\FilterData;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
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
        parent :: build_creation_form($description_options);
        
        $this->addElement('category', Translation :: get('Properties', null, Utilities :: COMMON_LIBRARIES));
        
        $post_max_size = Filesystem :: interpret_file_size(ini_get('post_max_size'));
        $upload_max_filesize = Filesystem :: interpret_file_size(ini_get('upload_max_filesize'));
        
        $maximum_server_size = $post_max_size < $upload_max_filesize ? $upload_max_filesize : $post_max_size;
        
        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(), 
                (int) $this->get_owner_id()));
        
        if ($calculator->get_available_user_disk_quota() < $maximum_server_size)
        {
            $maximum_size = $calculator->get_available_user_disk_quota();
            
            $url = Redirect :: get_url(
                array(
                    \Chamilo\Libraries\Architecture\Application\Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Manager :: context(), 
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_QUOTA, 
                    FilterData :: FILTER_CATEGORY => null, 
                    \Chamilo\Core\Repository\Quota\Manager :: PARAM_ACTION => null));
            
            $allow_upgrade = PlatformSetting :: get(
                'allow_upgrade', 
                \Chamilo\Core\Repository\Quota\Manager :: context());
            
            $allow_request = PlatformSetting :: get(
                'allow_request', 
                \Chamilo\Core\Repository\Quota\Manager :: context());
            
            $translation = ($allow_upgrade || $allow_request) ? 'MaximumFileSizeUser' : 'MaximumFileSizeUserNoUpgrade';
            
            $message = Translation :: get(
                $translation, 
                array(
                    'SERVER' => Filesystem :: format_file_size($maximum_server_size), 
                    'USER' => Filesystem :: format_file_size($maximum_size), 
                    'URL' => $url));
            
            if ($maximum_size < 5242880)
            {
                $this->add_error_message('max_size', null, $message);
            }
            else
            {
                $this->add_warning_message('max_size', null, $message);
            }
        }
        else
        {
            $maximum_size = $maximum_server_size;
            $message = Translation :: get(
                'MaximumFileSizeServer', 
                array('FILESIZE' => Filesystem :: format_file_size($maximum_size)));
            $this->add_warning_message('max_size', null, $message);
        }
        
        $this->addElement('file', 'file', sprintf(Translation :: get('FileName')));
        $this->addRule(
            'file', 
            Translation :: get('DiskQuotaExceeded', null, Utilities :: COMMON_LIBRARIES), 
            'disk_quota');
        
        $this->addFormRule(array($this, 'check_document_form'));
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        $description_options = array();
        $description_options['height'] = '100';
        $description_options['collapse_toolbar'] = true;
        parent :: build_editing_form($description_options);
        
        $this->addElement('category', Translation :: get('Properties', null, Utilities :: COMMON_LIBRARIES));
        
        $post_max_size = Filesystem :: interpret_file_size(ini_get('post_max_size'));
        $upload_max_filesize = Filesystem :: interpret_file_size(ini_get('upload_max_filesize'));
        
        $maximum_server_size = $post_max_size < $upload_max_filesize ? $upload_max_filesize : $post_max_size;
        
        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                \Chamilo\Core\User\Storage\DataClass\User :: class_name(), 
                (int) $this->get_owner_id()));
        
        if ($calculator->get_available_user_disk_quota() < $maximum_server_size)
        {
            $maximum_size = $calculator->get_available_user_disk_quota();
            
            $url = Redirect :: get_url(
                array(
                    \Chamilo\Libraries\Architecture\Application\Application :: PARAM_CONTEXT => \Chamilo\Core\Repository\Manager :: context(), 
                    \Chamilo\Core\Repository\Manager :: PARAM_ACTION => \Chamilo\Core\Repository\Manager :: ACTION_QUOTA, 
                    FilterData :: FILTER_CATEGORY => null, 
                    \Chamilo\Core\Repository\Quota\Manager :: PARAM_ACTION => null));
            
            $allow_upgrade = PlatformSetting :: get(
                'allow_upgrade', 
                \Chamilo\Core\Repository\Quota\Manager :: context());
            
            $allow_request = PlatformSetting :: get(
                'allow_request', 
                \Chamilo\Core\Repository\Quota\Manager :: context());
            
            $translation = ($allow_upgrade || $allow_request) ? 'MaximumFileSizeUser' : 'MaximumFileSizeUserNoUpgrade';
            
            $message = Translation :: get(
                $translation, 
                array(
                    'SERVER' => Filesystem :: format_file_size($maximum_server_size), 
                    'USER' => Filesystem :: format_file_size($maximum_size), 
                    'URL' => $url));
            
            if ($maximum_size < 5242880)
            {
                $this->add_error_message('max_size', null, $message);
            }
            else
            {
                $this->add_warning_message('max_size', null, $message);
            }
        }
        else
        {
            $maximum_size = $maximum_server_size;
            $message = Translation :: get(
                'MaximumFileSizeServer', 
                array('FILESIZE' => Filesystem :: format_file_size($maximum_size)));
            $this->add_warning_message('max_size', null, $message);
        }
        $object = $this->get_content_object();
        
        $this->addElement('file', 'file', sprintf(Translation :: get('FileName'), $post_max_size));
        $this->addRule(
            'file', 
            Translation :: get('DiskQuotaExceeded', null, Utilities :: COMMON_LIBRARIES), 
            'disk_quota');
        
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
        $object->set_filename($_FILES['file']['name']);
        $object->set_temporary_file_path($_FILES['file']['tmp_name']);
        
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
        
        $quotamanager = new Calculator($owner);
        
        if (isset($_FILES['file']) && isset($_FILES['file']['error']) && $_FILES['file']['error'] != 0)
        {
            switch ($_FILES['file']['error'])
            {
                case 1 : // uploaded file exceeds the upload_max_filesize directive in php.ini
                    $errors['upload_or_create'] = Translation :: get('FileTooBig');
                    break;
                case 2 : // uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                    $errors['upload_or_create'] = Translation :: get('FileTooBig');
                    break;
                case 3 : // uploaded file was only partially uploaded
                    $errors['upload_or_create'] = Translation :: get('UploadIncomplete');
                    break;
                case 4 : // no file was uploaded
                    $errors['upload_or_create'] = Translation :: get('NoFileSelected');
                    break;
            }
        }
        elseif (isset($_FILES['file']) && strlen($_FILES['file']['name']) > 0)
        {
            $size = $_FILES['file']['size'];
            $available_disk_space = $quotamanager->get_available_user_disk_quota();
            
            if ($size > $available_disk_space)
            {
                $errors['upload_or_create'] = Translation :: get(
                    'DiskQuotaExceeded', 
                    null, 
                    Utilities :: COMMON_LIBRARIES);
            }
            
            $array = explode('.', $_FILES['file']['name']);
            $type = $array[count($array) - 1];
            
            if (isset($fields['uncompress']) && $type != 'zip')
            {
                $errors['upload_or_create'] = Translation :: get('UncompressNotAvailableForThisFile');
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
                    $errors['upload_or_create'] = Translation :: get('FileTypeNotAllowed');
                }
            }
        }
        else
        {
            $errors['upload_or_create'] = Translation :: get('NoFileSelected');
        }
        
        if (count($errors) == 0)
        {
            return true;
        }
        
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
