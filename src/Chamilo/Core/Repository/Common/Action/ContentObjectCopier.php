<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;

/**
 *
 * @package repository
 * @author Hans De Bisschop
 */
class ContentObjectCopier
{
    const TYPE_ERROR = 1;
    const TYPE_WARNING = 2;
    const TYPE_CONFIRM = 3;
    const TYPE_NORMAL = 4;

    private $messages;

    private $content_object_ids;

    private $source_user_id;

    private $target_user_id;

    private $target_category;

    public function __construct($content_object_ids, $source_user_id, $target_user_id, $target_category = 0)
    {
        $this->content_object_ids = $content_object_ids;
        $this->source_user_id = $source_user_id;
        $this->target_user_id = $target_user_id;
        $this->target_category = $target_category;
    }

    public function run()
    {
        if (! $this->content_object_ids)
        {
            $this->add_message(Translation :: get('NoObjectSelected'), self :: TYPE_ERROR);
            return false;
        }

        if (! is_array($this->content_object_ids))
        {
            $this->content_object_ids = array($this->content_object_ids);
        }

        $export_content_object_ids = array();
        foreach ($this->content_object_ids as $content_object_id)
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
                ContentObject :: class_name(),
                $content_object_id);

            $export_content_object_ids[] = $content_object_id;
        }

        if (! $export_content_object_ids)
        {
            $this->add_message(Translation :: get('NoObjectSelected'), self :: TYPE_ERROR);
            return false;
        }

        $export_parameters = new ExportParameters(
            $this->source_user_id,
            ContentObjectExport :: FORMAT_CPO,
            $export_content_object_ids);

        $exporter = ContentObjectExportController :: factory($export_parameters);

        $path = $exporter->run();

        $file = FileProperties :: from_path($path);
        $path_info = pathinfo($path);
        $new_path = $path_info['dirname'] . DIRECTORY_SEPARATOR . $file->get_name() . '.cpo';

        Filesystem :: move_file($path, $new_path);
        $file = FileProperties :: from_path($new_path);

        $targetUser = \Chamilo\Libraries\Storage\DataManager\DataManager :: retrieve_by_id(
            User :: class_name(),
            $this->target_user_id);

        $parameters = ImportParameters :: factory(
            ContentObjectImport :: FORMAT_CPO,
            $this->target_user_id,
            new PersonalWorkspace($targetUser),
            $this->target_category,
            $file);
        $controller = ContentObjectImportController :: factory($parameters);
        $content_object_ids = $controller->run();

        $this->messages = $controller->get_messages();

        if ($this->source_user_id == $this->target_user_id)
        {
            $this->change_content_object_names($content_object_ids);
        }

        Filesystem :: remove($new_path);

        return $content_object_ids;
    }

    /**
     * Adds a message to the message list
     *
     * @param String $message
     * @param int $type
     */
    public function add_message($message, $type)
    {
        if (! isset($this->messages[$type]))
        {
            $this->messages[$type] = array();
        }

        $this->messages[$type][] = $message;
    }

    /**
     * Checks wether the object has messages
     *
     * @return booleans
     */
    public function has_messages($type)
    {
        return count($this->get_messages($type)) > 0;
    }

    /**
     * Retrieves the list of messages
     *
     * @return Array
     */
    public function get_messages($type = null)
    {
        if ($type)
        {
            return isset($this->messages[$type]) ? $this->messages[$type] : array();
        }
        else
        {
            return $this->messages;
        }
    }

    /**
     * Clears the errors
     */
    public function clear_messages($type)
    {
        unset($this->messages[$type]);
    }

    public function get_messages_for_url()
    {
        $messages = array();
        $message_types = array();

        foreach ($this->get_messages() as $type => $type_messages)
        {
            foreach ($type_messages as $message)
            {
                $messages[] = $message;
                $message_types[] = $type;
            }
        }

        return array(Application :: PARAM_MESSAGE => $messages, Application :: PARAM_MESSAGE_TYPE => $message_types);
    }

    /**
     * Changes the title of the duplicated content objects by adding a copy value to show which object is the copied
     * one.
     *
     * @param array $content_object_ids
     */
    protected function change_content_object_names(array $content_object_ids)
    {
        DataClassCache :: reset();

        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            $content_object_ids);

        $parameters = new DataClassRetrievesParameters($condition);

        $content_objects = DataManager :: retrieve_content_objects(ContentObject :: class_name(), $parameters);

        while ($content_object = $content_objects->next_result())
        {
            $content_object->set_title($content_object->get_title() . ' (' . Translation :: get('Copy') . ')');
            $content_object->update();
        }
    }
}
