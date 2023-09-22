<?php
namespace Chamilo\Configuration\Package;

use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifier;
use Chamilo\Configuration\Package\Properties\Dependencies\DependencyVerifierRenderer;
use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\FilesystemTools;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Symfony\Component\Translation\Translator;

abstract class Action
{
    public const TYPE_CONFIRM = '2';
    public const TYPE_ERROR = '4';
    public const TYPE_NORMAL = '1';
    public const TYPE_WARNING = '3';

    protected ClassnameUtilities $classnameUtilities;

    protected ConfigurationService $configurationService;

    protected string $context;

    protected DependencyVerifier $dependencyVerifier;

    protected DependencyVerifierRenderer $dependencyVerifierRenderer;

    protected FilesystemTools $filesystemTools;

    protected PackageBundlesCacheService $packageBundlesCacheService;

    protected PackageFactory $packageFactory;

    protected RegistrationService $registrationService;

    protected StorageUnitRepository $storageUnitRepository;

    protected SystemPathBuilder $systemPathBuilder;

    protected Translator $translator;

    private array $message;

    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationService $configurationService,
        StorageUnitRepository $storageUnitRepository, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService, PackageFactory $packageFactory,
        RegistrationService $registrationService, SystemPathBuilder $systemPathBuilder,
        DependencyVerifier $dependencyVerifier, DependencyVerifierRenderer $dependencyVerifierRenderer, string $context
    )
    {
        $this->classnameUtilities = $classnameUtilities;
        $this->configurationService = $configurationService;
        $this->storageUnitRepository = $storageUnitRepository;
        $this->translator = $translator;
        $this->context = $context;
        $this->packageBundlesCacheService = $packageBundlesCacheService;
        $this->packageFactory = $packageFactory;
        $this->registrationService = $registrationService;
        $this->systemPathBuilder = $systemPathBuilder;
        $this->dependencyVerifier = $dependencyVerifier;
        $this->dependencyVerifierRenderer = $dependencyVerifierRenderer;

        $this->message = [];
    }

    public function add_message($type = self::TYPE_NORMAL, $message): void
    {
        switch ($type)
        {
            case self::TYPE_CONFIRM :
                $this->message[] = '<span style="color: green; font-weight: bold;">' . $message . '</span>';
                break;
            case self::TYPE_WARNING :
                $this->message[] = '<span style="color: orange; font-weight: bold;">' . $message . '</span>';
                break;
            case self::TYPE_ERROR :
                $this->message[] = '<span style="color: red; font-weight: bold;">' . $message . '</span>';
                break;
            default :
                $this->message[] = $message;
                break;
        }
    }

    public function failed($error_message): bool
    {
        $this->add_message(self::TYPE_ERROR, $error_message);
        $this->add_message(
            self::TYPE_ERROR, $this->getTranslator()->trans($this->getType() . 'Failed', [], 'Chamilo\Core\Install')
        );

        return false;
    }

    public function getClassnameUtilities(): ClassnameUtilities
    {
        return $this->classnameUtilities;
    }

    public function getConfigurationService(): ConfigurationService
    {
        return $this->configurationService;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getDependencyVerifier(): DependencyVerifier
    {
        return $this->dependencyVerifier;
    }

    public function getDependencyVerifierRenderer(): DependencyVerifierRenderer
    {
        return $this->dependencyVerifierRenderer;
    }

    protected function getFilesystemTools(): FilesystemTools
    {
        return $this->filesystemTools;
    }

    public function getPackageBundlesCacheService(): PackageBundlesCacheService
    {
        return $this->packageBundlesCacheService;
    }

    public function getPackageFactory(): PackageFactory
    {
        return $this->packageFactory;
    }

    public function getPath(): string
    {
        return $this->getSystemPathBuilder()->namespaceToFullPath($this->getContext());
    }

    public function getRegistrationService(): RegistrationService
    {
        return $this->registrationService;
    }

    public function getStorageUnitRepository(): StorageUnitRepository
    {
        return $this->storageUnitRepository;
    }

    public function getSystemPathBuilder(): SystemPathBuilder
    {
        return $this->systemPathBuilder;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getType(): string
    {
        return $this->getClassnameUtilities()->getClassnameFromObject($this);
    }

    /**
     * @return string[]
     */
    public function get_message(): array
    {
        return $this->message;
    }

    /**
     * @deprecated Use Action::getType() now
     */
    public function get_type(): string
    {
        return $this->getType();
    }

    /**
     * @return string
     */
    public function retrieve_message(): string
    {
        return implode('<br />' . PHP_EOL, $this->get_message());
    }

    public function successful(): bool
    {
        $this->add_message(
            self::TYPE_CONFIRM,
            $this->getTranslator()->trans($this->getType() . 'Successful', [], 'Chamilo\Core\Install')
        );

        return true;
    }
}
