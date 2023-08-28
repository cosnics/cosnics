<?php
namespace Chamilo\Configuration\Package;

use Chamilo\Configuration\Package\Service\PackageBundlesCacheService;
use Chamilo\Configuration\Package\Service\PackageFactory;
use Chamilo\Configuration\Service\ConfigurationService;
use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Configuration\Service\RegistrationService;
use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Core\Admin\Language\Manager;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\SystemPathBuilder;
use Chamilo\Libraries\Storage\DataManager\Repository\StorageUnitRepository;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Configuration\Package
 */
class Installer extends Action\Installer
{
    public const CONTEXT = 'Chamilo\Configuration';

    protected LanguageConsulter $languageConsulter;

    public function __construct(
        ClassnameUtilities $classnameUtilities, ConfigurationService $configurationService,
        StorageUnitRepository $storageUnitRepository, Translator $translator,
        PackageBundlesCacheService $packageBundlesCacheService, PackageFactory $packageFactory,
        RegistrationService $registrationService, SystemPathBuilder $systemPathBuilder, string $context,
        LanguageConsulter $languageConsulter
    )
    {
        parent::__construct(
            $classnameUtilities, $configurationService, $storageUnitRepository, $translator,
            $packageBundlesCacheService, $packageFactory, $registrationService, $systemPathBuilder, $context
        );

        $this->languageConsulter = $languageConsulter;
    }

    public function createLanguages(): bool
    {
        $translator = $this->getTranslator();

        $languages = $this->getLanguageConsulter()->getLanguagesFromFilesystem();

        foreach ($languages as $language)
        {
            $language = new Language($language);
            $language->set_available('1');

            if ($language->create())
            {
                $this->add_message(
                    self::TYPE_NORMAL, $translator->trans(
                        'ObjectAdded', ['OBJECT' => $translator->trans('Language', [], Manager::class)],
                        StringUtilities::LIBRARIES
                    ) . ' ' . $language->get_english_name()
                );
            }
            else
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Runs the install-script.
     */
    public function extra(): bool
    {
        $translator = $this->getTranslator();

        // Add the default language entries in the database
        if (!$this->createLanguages())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $translator->trans(
                'ObjectsAdded', ['OBJECTS' => $translator->trans('Languages', [], Manager::class)],
                StringUtilities::LIBRARIES
            )
            );
        }

        return true;
    }

    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->languageConsulter;
    }
}
