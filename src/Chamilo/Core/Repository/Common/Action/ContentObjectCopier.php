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
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Cache\DataClassCache;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;

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

    /**
     *
     * @var integer[]
     */
    private $contentObjectIdentifiers;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $sourceWorkspace;

    /**
     *
     * @var integer
     */
    private $sourceUserIdentifier;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $targetWorkspace;

    /**
     *
     * @var integer
     */
    private $targetUserIdentifier;

    /**
     *
     * @var integer
     */
    private $targetCategory;

    /**
     *
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $currentUser;

    /**
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param integer[] $contentObjectIdentifiers
     * @param WorkspaceInterface $sourceWorkspace
     * @param integer $sourceUserIdentifier
     * @param WorkspaceInterface $targetWorkspace
     * @param integer $targetUserIdentifier
     * @param integer $targetCategory
     */
    public function __construct(User $currentUser, $contentObjectIdentifiers, WorkspaceInterface $sourceWorkspace, 
        $sourceUserIdentifier, WorkspaceInterface $targetWorkspace, $targetUserIdentifier, $targetCategory = 0)
    {
        $this->currentUser = $currentUser;
        $this->contentObjectIdentifiers = $contentObjectIdentifiers;
        $this->sourceWorkspace = $sourceWorkspace;
        $this->sourceUserIdentifier = $sourceUserIdentifier;
        $this->targetWorkspace = $targetWorkspace;
        $this->targetUserIdentifier = $targetUserIdentifier;
        $this->targetCategory = $targetCategory;
    }

    public function run()
    {
        if (! $this->contentObjectIdentifiers)
        {
            $this->add_message(Translation::get('NoObjectSelected'), self::TYPE_ERROR);
            return false;
        }
        
        if (! is_array($this->contentObjectIdentifiers))
        {
            $this->contentObjectIdentifiers = array($this->contentObjectIdentifiers);
        }
        
        $exportableContentObjectIdentifiers = array();
        
        foreach ($this->contentObjectIdentifiers as $contentObjectIdentifier)
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                ContentObject::class_name(), 
                $contentObjectIdentifier);
            
            if (RightsService::getInstance()->canCopyContentObject($this->currentUser, $content_object))
            {
                $exportableContentObjectIdentifiers[] = $contentObjectIdentifier;
            }
        }
        
        if (! $exportableContentObjectIdentifiers)
        {
            $this->add_message(Translation::get('NoObjectSelected'), self::TYPE_ERROR);
            return false;
        }
        
        $exportParameters = new ExportParameters(
            $this->sourceWorkspace, 
            $this->sourceUserIdentifier, 
            ContentObjectExport::FORMAT_CPO, 
            $exportableContentObjectIdentifiers);
        
        $exporter = ContentObjectExportController::factory($exportParameters);
        
        $path = $exporter->run();
        
        $file = FileProperties::from_path($path);
        $pathInfo = pathinfo($path);
        $newPath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $file->get_name() . '.cpo';
        
        Filesystem::move_file($path, $newPath);
        $file = FileProperties::from_path($newPath);
        
        $targetUser = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
            User::class_name(), 
            $this->targetUserIdentifier);
        
        $parameters = ImportParameters::factory(
            ContentObjectImport::FORMAT_CPO, 
            $this->targetUserIdentifier, 
            $this->targetWorkspace, 
            $this->targetCategory, 
            $file);
        $controller = ContentObjectImportController::factory($parameters);
        $contentObjectIdentifiers = $controller->run();
        
        $this->messages = $controller->get_messages();
        
        if ($this->sourceUserIdentifier == $this->targetUserIdentifier)
        {
            $this->changeContentObjectNames($contentObjectIdentifiers);
        }
        
        Filesystem::remove($newPath);
        
        return $contentObjectIdentifiers;
    }

    /**
     * Adds a message to the message list
     * 
     * @param string $message
     * @param integer $type
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
     * @return boolean
     */
    public function has_messages($type)
    {
        return count($this->get_messages($type)) > 0;
    }

    /**
     * Retrieves the list of messages
     * 
     * @return string[]
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

    /**
     *
     * @return string[]
     */
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
        
        return array(Application::PARAM_MESSAGE => $messages, Application::PARAM_MESSAGE_TYPE => $message_types);
    }

    /**
     * Changes the title of the duplicated content objects by adding a copy value to show which object is the copied
     * one.
     * 
     * @param array $contentObjectIdentifiers
     */
    protected function changeContentObjectNames($contentObjectIdentifiers = array())
    {
        if(empty($contentObjectIdentifiers))
        {
            return;
        }

        DataClassCache::reset();
        
        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID), 
            $contentObjectIdentifiers);
        
        $parameters = new DataClassRetrievesParameters($condition);
        
        $content_objects = DataManager::retrieve_content_objects(ContentObject::class_name(), $parameters);
        
        while ($content_object = $content_objects->next_result())
        {
            $content_object->set_title($content_object->get_title() . ' (' . Translation::get('Copy') . ')');
            $content_object->update();
        }
    }
}
