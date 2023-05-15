<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Service\ContentObjectTemplate\ContentObjectTemplateSynchronizer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package Chamilo\Core\Repository\Common\Action
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class ContentObjectInstaller extends Installer
{

    /**
     * Perform additional installation steps
     *
     * @return bool
     */
    public function extra()
    {
        if (!$this->register_templates())
        {
            return false;
        }

        if (!$this->import_content_object())
        {
            return false;
        }

        return true;
    }

    /**
     * @return \Chamilo\Core\Repository\Service\ContentObjectTemplate\ContentObjectTemplateSynchronizer
     * @throws \Exception
     */
    public function getContentObjectTemplateSynchronizer()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ContentObjectTemplateSynchronizer::class
        );
    }

    /**
     * Import a sample content object (if available)
     *
     * @return bool
     */
    public function import_content_object()
    {
        $context = ClassnameUtilities::getInstance()->getNamespaceFromObject($this);
        $exampleFolderPath = Path::getInstance()->getResourcesPath($context) . 'Example/';

        $examplePaths = Filesystem::get_directory_content($exampleFolderPath);

        foreach ($examplePaths as $examplePath)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_PLATFORMADMIN), new StaticConditionVariable(1)
            );
            $user = DataManager::retrieves(
                User::class, new DataClassRetrievesParameters($condition)
            )->current();

            Session::register('_uid', $user->get_id());

            $parameters = ImportParameters::factory(
                ContentObjectImport::FORMAT_CPO, $user->get_id(), 0, FileProperties::from_path($examplePath)
            );
            $import = ContentObjectImportController::factory($parameters);
            $import->run();

            Session::unregister('_uid');

            if ($import->has_messages(ContentObjectImportController::TYPE_ERROR))
            {
                $message = Translation::get('ContentObjectImportFailed');
                $this->failed($message);

                return false;
            }
            else
            {
                $this->add_message(self::TYPE_NORMAL, Translation::get('ImportSuccessfull'));
            }
        }

        return true;
    }

    public function register_templates()
    {
        try
        {
            $this->getContentObjectTemplateSynchronizer()->synchronize(static::CONTEXT);

            return true;
        }
        catch (Exception $exception)
        {
            return true;
        }
    }
}
