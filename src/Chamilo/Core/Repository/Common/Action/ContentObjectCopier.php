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
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package repository
 * @author  Hans De Bisschop
 */
class ContentObjectCopier
{
    public const TYPE_CONFIRM = 3;
    public const TYPE_ERROR = 1;
    public const TYPE_NORMAL = 4;
    public const TYPE_WARNING = 2;

    /**
     * @var int
     */
    private $contentObjectIdentifiers;

    /**
     * @var \Chamilo\Core\User\Storage\DataClass\User
     */
    private $currentUser;

    private $messages;

    /**
     * @var int
     */
    private $sourceUserIdentifier;

    private Workspace $sourceWorkspace;

    /**
     * @var int
     */
    private $targetCategory;

    /**
     * @var int
     */
    private $targetUserIdentifier;

    private Workspace $targetWorkspace;

    /**
     * @param \Chamilo\Core\User\Storage\DataClass\User $currentUser
     * @param int $contentObjectIdentifiers
     * @param Workspace $sourceWorkspace
     * @param int $sourceUserIdentifier
     * @param Workspace $targetWorkspace
     * @param int $targetUserIdentifier
     * @param int $targetCategory
     */
    public function __construct(
        User $currentUser, $contentObjectIdentifiers, Workspace $sourceWorkspace, $sourceUserIdentifier,
        Workspace $targetWorkspace, $targetUserIdentifier, $targetCategory = 0
    )
    {
        $this->currentUser = $currentUser;
        $this->contentObjectIdentifiers = $contentObjectIdentifiers;
        $this->sourceWorkspace = $sourceWorkspace;
        $this->sourceUserIdentifier = $sourceUserIdentifier;
        $this->targetWorkspace = $targetWorkspace;
        $this->targetUserIdentifier = $targetUserIdentifier;
        $this->targetCategory = $targetCategory;
    }

    /**
     * @return bool | array
     */
    public function run()
    {
        if (!$this->contentObjectIdentifiers)
        {
            $this->add_message(Translation::get('NoObjectSelected'), self::TYPE_ERROR);

            return false;
        }

        if (!is_array($this->contentObjectIdentifiers))
        {
            $this->contentObjectIdentifiers = [$this->contentObjectIdentifiers];
        }

        $exportParameters = new ExportParameters(
            $this->sourceWorkspace, $this->sourceUserIdentifier, ContentObjectExport::FORMAT_CPO,
            $this->contentObjectIdentifiers
        );

        $exporter = ContentObjectExportController::factory($exportParameters);

        $path = $exporter->run();

        $file = FileProperties::from_path($path);
        $pathInfo = pathinfo($path);
        $newPath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $file->get_name() . '.cpo';

        Filesystem::move_file($path, $newPath);
        $file = FileProperties::from_path($newPath);

        $targetUser = \Chamilo\Libraries\Storage\DataManager\DataManager::retrieve_by_id(
            User::class, $this->targetUserIdentifier
        );

        $parameters = ImportParameters::factory(
            ContentObjectImport::FORMAT_CPO, $this->targetUserIdentifier, $this->targetWorkspace, $this->targetCategory,
            $file
        );
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
     * @param int $type
     */
    public function add_message($message, $type)
    {
        if (!isset($this->messages[$type]))
        {
            $this->messages[$type] = [];
        }

        $this->messages[$type][] = $message;
    }

    /**
     * Changes the title of the duplicated content objects by adding a copy value to show which object is the copied
     * one.
     *
     * @param array $contentObjectIdentifiers
     */
    protected function changeContentObjectNames($contentObjectIdentifiers = [])
    {
        if (empty($contentObjectIdentifiers))
        {
            return;
        }

        $this->getDataClassRepositoryCache()->reset();

        $condition = new InCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID), $contentObjectIdentifiers
        );

        $parameters = new DataClassRetrievesParameters($condition);

        $content_objects = DataManager::retrieve_content_objects(ContentObject::class, $parameters);

        foreach ($content_objects as $content_object)
        {
            $content_object->set_title($content_object->get_title() . ' (' . Translation::get('Copy') . ')');
            $content_object->update();
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
     * @return \Chamilo\Libraries\Storage\Cache\DataClassRepositoryCache
     */
    protected function getDataClassRepositoryCache()
    {
        return $this->getService(
            DataClassRepositoryCache::class
        );
    }

    /**
     * @param string $serviceName
     *
     * @return object
     * @throws \Exception
     */
    protected function getService(string $serviceName)
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            $serviceName
        );
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
            return isset($this->messages[$type]) ? $this->messages[$type] : [];
        }
        else
        {
            return $this->messages;
        }
    }

    /**
     * @return string[]
     */
    public function get_messages_for_url()
    {
        $messages = [];
        $message_types = [];

        foreach ($this->get_messages() as $type => $type_messages)
        {
            foreach ($type_messages as $message)
            {
                $messages[] = $message;
                $message_types[] = $type;
            }
        }

        return [Application::PARAM_MESSAGE => $messages, Application::PARAM_MESSAGE_TYPE => $message_types];
    }

    /**
     * Checks wether the object has messages
     *
     * @return bool
     */
    public function has_messages($type)
    {
        return count($this->get_messages($type)) > 0;
    }
}
