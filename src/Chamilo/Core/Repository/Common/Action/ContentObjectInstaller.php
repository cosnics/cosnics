<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifierRenderer;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Core\Repository\Common\Import\ContentObjectImport;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Service\ContentObjectTemplate\ContentObjectTemplateSynchronizer;
use Chamilo\Core\User\Manager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Core\User\Storage\DataManager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Repository\Common\Action
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ContentObjectInstaller extends Installer
{

    protected ContentObjectTemplateSynchronizer $contentObjectTemplateSynchronizer;

    protected SessionInterface $session;

    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationService $configurationService,
        StorageUnitRepository $storageUnitRepository, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService, PackageFactory $packageFactory,
        RegistrationService $registrationService, SystemPathBuilder $systemPathBuilder,
        DependencyVerifier $dependencyVerifier, DependencyVerifierRenderer $dependencyVerifierRenderer, string $context,
        ContentObjectTemplateSynchronizer $contentObjectTemplateSynchronizer, SessionInterface $session
    )
    {
        parent::__construct(
            $classnameUtilities, $configurationService, $storageUnitRepository, $translator,
            $packageBundlesCacheService, $packageFactory, $registrationService, $systemPathBuilder, $dependencyVerifier,
            $dependencyVerifierRenderer, $context
        );

        $this->contentObjectTemplateSynchronizer = $contentObjectTemplateSynchronizer;
        $this->session = $session;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \ReflectionException
     */
    public function extra(array $formValues): bool
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

    public function getContentObjectTemplateSynchronizer(): ContentObjectTemplateSynchronizer
    {
        return $this->contentObjectTemplateSynchronizer;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * Import a sample content object (if available)
     *
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \Exception
     */
    public function import_content_object(): bool
    {
        $translator = $this->getTranslator();
        $exampleFolderPath = $this->getSystemPathBuilder()->getResourcesPath($this->getContext()) . 'Example/';
        $examplePaths = $this->getFilesystemTools()->getDirectoryContent($exampleFolderPath);

        foreach ($examplePaths as $examplePath)
        {
            $condition = new EqualityCondition(
                new PropertyConditionVariable(User::class, User::PROPERTY_PLATFORMADMIN), new StaticConditionVariable(1)
            );
            $user = DataManager::retrieves(
                User::class, new DataClassRetrievesParameters($condition)
            )->current();

            $this->getSession()->set(Manager::SESSION_USER_ID, $user->get_id());

            $parameters = ImportParameters::factory(
                ContentObjectImport::FORMAT_CPO, $user->get_id(), 0, FileProperties::from_path($examplePath)
            );
            $import = ContentObjectImportController::factory($parameters);
            $import->run();

            $this->getSession()->remove(Manager::SESSION_USER_ID);

            if ($import->has_messages(ContentObjectImportController::TYPE_ERROR))
            {
                $message =
                    $translator->trans('ContentObjectImportFailed', [], \Chamilo\Core\Repository\Manager::CONTEXT);
                $this->failed($message);

                return false;
            }
            else
            {
                $this->add_message(
                    self::TYPE_NORMAL,
                    $translator->trans('ImportSuccessfull', [], \Chamilo\Core\Repository\Manager::CONTEXT)
                );
            }
        }

        return true;
    }

    public function register_templates(): bool
    {
        try
        {
            $this->getContentObjectTemplateSynchronizer()->synchronize($this->getContext());

            return true;
        }
        catch (Exception)
        {
            return false;
        }
    }
}
