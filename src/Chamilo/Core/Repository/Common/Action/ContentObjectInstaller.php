<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Common\Template\Template;
use Chamilo\Core\Repository\Service\ConfigurationCacheService;
use Chamilo\Core\Repository\Service\ContentObjectTemplate\ContentObjectTemplateLoader;
use Chamilo\Core\Repository\Service\ContentObjectTemplate\ContentObjectTemplateSynchronizer;
use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Storage\Repository\ContentObjectTemplateRepository;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\PathBuilder;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

/**
 * Extension of the generic installer for content objects
 * 
 * @author Hans De Bisschop
 */
abstract class ContentObjectInstaller extends \Chamilo\Configuration\Package\Action\Installer
{

    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Perform additional installation steps
     * 
     * @return boolean
     */
    public function extra()
    {
        if (! $this->register_templates())
        {
            return false;
        }
        
        if (! $this->import_content_object())
        {
            return false;
        }
        
        return true;
    }

    /**
     * Import a sample content object (if available)
     * 
     * @return boolean
     */
    public function import_content_object()
    {
        $context = ClassnameUtilities::getInstance()->getNamespaceFromObject($this);
        $exampleFolderPath = Path::getInstance()->getResourcesPath($context) . 'Example/';
        
        $examplePaths = Filesystem::get_directory_content($exampleFolderPath);
        
        foreach ($examplePaths as $examplePath)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(User::class_name(), User::PROPERTY_PLATFORMADMIN), 
                new StaticConditionVariable(1));
            $user = \Chamilo\Core\User\Storage\DataManager::retrieves(
                User::class_name(), 
                new DataClassRetrievesParameters($condition))->next_result();
            
            \Chamilo\Libraries\Platform\Session\Session::register('_uid', $user->get_id());
            
            $parameters = ImportParameters::factory(
                ContentObjectImport::FORMAT_CPO, 
                $user->get_id(), 
                0, 
                FileProperties::from_path($examplePath));
            $import = ContentObjectImportController::factory($parameters);
            $import->run();
            
            \Chamilo\Libraries\Platform\Session\Session::unregister('_uid');
            
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
        $contentObjectTemplateSynchronizer = new ContentObjectTemplateSynchronizer(
            new ContentObjectTemplateLoader(PathBuilder::getInstance()),
            new ContentObjectTemplateRepository(),
            new ConfigurationCacheService()
        );
        
        try
        {
            $contentObjectTemplateSynchronizer->synchronize(static::package());
            
            return true;
        }
        catch (\Exception $exception)
        {
            return true;
        }
    }
}
