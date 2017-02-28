<?php

namespace Chamilo\Core\Repository\Service\ContentObjectTemplate;

use Chamilo\Core\Repository\Service\ConfigurationCacheService;
use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Core\Repository\Storage\Repository\ContentObjectTemplateRepository;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectTemplateSynchronizer
{
    /**
     * @var ContentObjectTemplateLoader
     */
    protected $contentObjectTemplateLoader;

    /**
     * @var ContentObjectTemplateRepository
     */
    protected $contentObjectTemplateRepository;

    /**
     * @var ConfigurationCacheService
     */
    protected $configurationCacheService;

    /**
     * ContentObjectTemplateSynchronizer constructor.
     *
     * @param ContentObjectTemplateLoader $contentObjectTemplateLoader
     * @param ContentObjectTemplateRepository $contentObjectTemplateRepository
     * @param ConfigurationCacheService $configurationCacheService
     */
    public function __construct(
        ContentObjectTemplateLoader $contentObjectTemplateLoader,
        ContentObjectTemplateRepository $contentObjectTemplateRepository,
        ConfigurationCacheService $configurationCacheService
    )
    {
        $this->contentObjectTemplateLoader = $contentObjectTemplateLoader;
        $this->contentObjectTemplateRepository = $contentObjectTemplateRepository;
        $this->configurationCacheService = $configurationCacheService;
    }

    /**
     * Synchronizes the templates for a given content object (identified by it's namespace)
     *
     * @param $contentObjectNamespace
     */
    public function synchronize($contentObjectNamespace)
    {
        $templates = $this->contentObjectTemplateLoader->loadTemplates($contentObjectNamespace);

        foreach ($templates as $templateName => $template)
        {
            $templateRegistration =
                $this->contentObjectTemplateRepository->getTemplateRegistrationByContentObjectTypeAndTemplateName(
                    $contentObjectNamespace, $templateName
                );

            if(!$templateRegistration instanceof TemplateRegistration)
            {
                $templateRegistration = new TemplateRegistration();
                $templateRegistration->set_name($templateName);
                $templateRegistration->set_content_object_type($contentObjectNamespace);

                if ($templateName == 'Default')
                {
                    $templateRegistration->set_default(true);
                }
            }

            $templateRegistration->set_template($template);

            if(!$templateRegistration->save())
            {
                throw new \RuntimeException('Could not save the template ' . $templateName);
            }
        }

        $this->configurationCacheService->clearAndWarmUp();
    }
}