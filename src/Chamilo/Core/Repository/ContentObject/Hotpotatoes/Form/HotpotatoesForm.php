<?php
namespace Chamilo\Core\Repository\ContentObject\Hotpotatoes\Form;

use Chamilo\Core\Repository\ContentObject\Assessment\Storage\DataClass\Assessment;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Core\Repository\Form\ContentObjectForm;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib.content_object.hotpotatoes
 */

/**
 * This class represents a form to create or update open questions
 */
class HotpotatoesForm extends ContentObjectForm
{

    protected function build_creation_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_creation_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->add_textfield(Assessment::PROPERTY_MAXIMUM_ATTEMPTS, Translation::get('MaximumAttempts'));
        $this->addElement('static', null, null, Translation::get('NoMaximumAttemptsFillIn0'));
        $this->addElement('file', 'file', Translation::get('UploadHotpotatoes'));
        $this->addRule('file', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required');
    }

    protected function build_editing_form($htmleditor_options = [], $in_tab = false)
    {
        parent::build_editing_form();
        $this->addElement('category', Translation::get('Properties'));
        $this->add_textfield(Hotpotatoes::PROPERTY_MAXIMUM_ATTEMPTS, Translation::get('MaximumAttempts'));
        $this->addElement('static', null, null, Translation::get('NoMaximumAttemptsFillIn0'));
        $this->addElement('file', 'file', Translation::get('ChangeHotpotatoes'));
        $this->addRule('file', Translation::get('ThisFieldIsRequired', null, StringUtilities::LIBRARIES), 'required');
    }

    // Inherited

    public function create_content_object()
    {
        $object = new Hotpotatoes();
        $values = $this->exportValues();

        if (!$this->upload_file($object))
        {
            return false;
        }

        $att = $values[Hotpotatoes::PROPERTY_MAXIMUM_ATTEMPTS];
        $object->set_maximum_attempts($att ?: 0);

        $this->set_content_object($object);
        // $object->add_javascript();
        $succes = parent::create_content_object();

        return $succes;
    }

    // Inherited

    public function setDefaults($defaults = [], $filter = null)
    {
        $object = $this->get_content_object();
        if ($object != null)
        {
            $defaults[Hotpotatoes::PROPERTY_MAXIMUM_ATTEMPTS] = $object->get_maximum_attempts();
        }
        else
        {
            $defaults[Hotpotatoes::PROPERTY_MAXIMUM_ATTEMPTS] = 0;
        }

        parent::setDefaults($defaults);
    }

    public function update_content_object()
    {
        $object = $this->get_content_object();
        $values = $this->exportValues();

        if (isset($_FILES['file']) && $_FILES['file']['name'] != '')
        {
            $object->delete_file();
            if (!$this->upload_file($object))
            {
                return false;
            }
        }

        $att = $values[Hotpotatoes::PROPERTY_MAXIMUM_ATTEMPTS];
        $object->set_maximum_attempts($att ?: 0);

        $this->set_content_object($object);

        $succes = parent::update_content_object();

        return $succes;
    }

    public function upload()
    {
        $owner = $this->get_owner_id();
        $filename = Filesystem::create_unique_name(
            $this->getSystemPathBuilder()->getPublicStoragePath(Hotpotatoes::CONTEXT) . $owner, $_FILES['file']['name']
        );

        $filename_split = explode('.', $filename);
        unset($filename_split[count($filename_split) - 1]);
        $file = implode('.', $filename_split);

        $hotpot_path = $this->getSystemPathBuilder()->getPublicStoragePath(Hotpotatoes::CONTEXT) . $owner . '/';
        $real_path = $hotpot_path . Filesystem::create_unique_name($hotpot_path, $file) . '/';

        if (!is_dir($real_path))
        {
            Filesystem::create_dir($real_path);
        }

        $full_path = $real_path . $filename;

        move_uploaded_file($_FILES['file']['tmp_name'], $full_path) or die('Failed to create "' . $full_path . '"');
        chmod($full_path, 0777);

        return substr($full_path, strlen($hotpot_path));
    }

    public function upload_file($object)
    {
        if ($_FILES['file']['error'] == '4')
        {
            return false;
        }

        $path = $this->upload();

        $filename = $_FILES['file']['name'];
        if (substr($filename, - 4) == '.zip')
        {
            $object->load_from_zip($path);
        }
        else
        {
            $object->set_path($path);
        }

        return true;
    }
}
