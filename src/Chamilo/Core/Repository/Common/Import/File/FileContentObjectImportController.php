<?php
namespace Chamilo\Core\Repository\Common\Import\File;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class FileContentObjectImportController extends ContentObjectImportController
{
    const FORMAT = 'Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File';

    public function run()
    {
        if (self :: is_available())
        {
            if ($this->get_parameters()->get_document_type() == FileContentObjectImportForm :: DOCUMENT_UPLOAD)
            {
                $file = $this->get_parameters()->get_file();
                $calculator = new Calculator(
                    \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                        \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                        (int) $this->get_parameters()->get_user()));

                if ($calculator->get_available_user_disk_quota() < $file->get_size())
                {
                    $this->add_message(Translation :: get('InsufficientDiskQuota'), self :: TYPE_ERROR);
                    return array();
                }
            }
            else
            {
                $file = FileProperties :: from_url($this->get_parameters()->get_link());

                if ($file->get_path() && $file->get_name() && $file->get_extension())
                {
                    $calculator = new Calculator(
                        \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                            (int) $this->get_parameters()->get_user()));

                    if ($calculator->get_available_user_disk_quota() > $file->get_size())
                    {
                        $temp_path = Path :: getInstance()->getTemporaryPath() . 'repository/import/file/' .
                             $file->get_name_extension();

                        if (file_exists($temp_path))
                        {
                            $this->add_message(Translation :: get('ObjectNotImported'), self :: TYPE_ERROR);
                            return array();
                        }
                        else
                        {
                            $destination_dir = dirname($temp_path);
                            if (Filesystem :: create_dir($destination_dir))
                            {
                                if (copy($file->get_path(), $temp_path))
                                {
                                    $file = FileProperties :: from_path($temp_path);
                                }
                                else
                                {
                                    $this->add_message(Translation :: get('ObjectNotImported'), self :: TYPE_ERROR);
                                    return array();
                                }
                            }
                        }
                    }
                    else
                    {
                        $this->add_message(Translation :: get('InsufficientDiskQuota'), self :: TYPE_ERROR);
                        return array();
                    }
                }
                else
                {
                    $this->add_message(Translation :: get('InvalidDocumentLink'), self :: TYPE_ERROR);
                    return array();
                }
            }

            $document = new File();
            $document->set_title($file->get_name());
            $document->set_description($file->get_name());
            $document->set_owner_id($this->get_parameters()->get_user());
            $document->set_parent_id($this->get_parameters()->get_category());
            $document->set_filename($file->get_name_extension());

            $hash = md5_file($file->get_path());
            $conditions = array();
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_OWNER_ID),
                new StaticConditionVariable($this->get_parameters()->get_user()));
            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_CONTENT_HASH),
                new StaticConditionVariable($hash));
            $condition = new AndCondition($conditions);
            $parameters = new DataClassRetrievesParameters($condition);

            $content_objects = DataManager :: retrieve_active_content_objects(File :: class_name(), $parameters);

            if ($content_objects->size() > 0)
            {
                if ($content_objects->size() == 1)
                {
                    $content_object = $content_objects->next_result();

                    $redirect = new Redirect(
                        array(
                            Application :: PARAM_ACTION => Manager :: ACTION_VIEW_CONTENT_OBJECTS,
                            Manager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));

                    $this->add_message(
                        Translation :: get('ObjectAlreadyExists', array('LINK' => $redirect->getUrl())),
                        self :: TYPE_ERROR);
                    return array();
                }
                else
                {
                    $this->add_message(Translation :: get('ObjectAlreadyExistsMultipleTimes'), self :: TYPE_ERROR);
                    return array();
                }
            }
            else
            {
                $document->set_temporary_file_path($file->get_path());

                if ($document->create())
                {
                    $this->add_message(Translation :: get('ObjectImported'), self :: TYPE_CONFIRM);
                    return array($document->get_id());
                }
                else
                {
                    $this->add_message(Translation :: get('ObjectNotImported'), self :: TYPE_ERROR);
                    return array();
                }
            }
        }
        else
        {
            $this->add_message(Translation :: get('DocumentObjectNotAvailable'), self :: TYPE_WARNING);
            return array();
        }
    }

    public static function is_available()
    {
        return in_array(self :: FORMAT, DataManager :: get_registered_types(true));
    }
}
