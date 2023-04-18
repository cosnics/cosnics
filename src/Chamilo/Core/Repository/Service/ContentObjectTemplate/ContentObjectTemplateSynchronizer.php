<?php

namespace Chamilo\Core\Repository\Service\ContentObjectTemplate;

use Chamilo\Core\Repository\Service\TemplateRegistrationCacheDataLoader;
use Chamilo\Core\Repository\Storage\DataClass\TemplateRegistration;
use Chamilo\Core\Repository\Storage\Repository\ContentObjectTemplateRepository;
use RuntimeException;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ContentObjectTemplateSynchronizer
{
    /**
     * @var \Chamilo\Core\Repository\Service\ContentObjectTemplate\ContentObjectTemplateLoader
     */
    protected $contentObjectTemplateLoader;

    /**
     * @var \Chamilo\Core\Repository\Storage\Repository\ContentObjectTemplateRepository
     */
    protected $contentObjectTemplateRepository;

    /**
     * @var \Chamilo\Core\Repository\Service\TemplateRegistrationCacheDataLoader
     */
    protected $templateRegistrationLoader;

    /**
     * ContentObjectTemplateSynchronizer constructor.
     *
     * @param ContentObjectTemplateLoader $contentObjectTemplateLoader
     * @param ContentObjectTemplateRepository $contentObjectTemplateRepository
     * @param TemplateRegistrationCacheDataLoader $templateRegistrationLoader
     */
    public function __construct(
        ContentObjectTemplateLoader $contentObjectTemplateLoader,
        ContentObjectTemplateRepository $contentObjectTemplateRepository,
        TemplateRegistrationCacheDataLoader $templateRegistrationLoader
    )
    {
        $this->contentObjectTemplateLoader = $contentObjectTemplateLoader;
        $this->contentObjectTemplateRepository = $contentObjectTemplateRepository;
        $this->templateRegistrationLoader = $templateRegistrationLoader;
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

            if (!$templateRegistration instanceof TemplateRegistration)
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

            if (!$templateRegistration->save())
            {
                throw new RuntimeException('Could not save the template ' . $templateName);
            }
        }

        $this->templateRegistrationLoader->clearAndWarmUp();
    }
}