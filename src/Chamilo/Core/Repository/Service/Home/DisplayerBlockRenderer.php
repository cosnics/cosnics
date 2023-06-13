<?php
namespace Chamilo\Core\Repository\Service\Home;

use Chamilo\Configuration\Service\Consulter\ConfigurationConsulter;
use Chamilo\Core\Home\Architecture\Interfaces\AnonymousBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockRendererInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Form\ConfigurationForm;
use Chamilo\Core\Home\Form\ConfigurationFormFactory;
use Chamilo\Core\Home\Rights\Service\ElementRightsService;
use Chamilo\Core\Home\Service\ContentObjectPublicationService;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

class DisplayerBlockRenderer extends BlockRenderer
    implements ConfigurableBlockRendererInterface, StaticBlockTitleInterface, ContentObjectPublicationBlockInterface,
    AnonymousBlockInterface
{
    public const CONTEXT = Manager::CONTEXT;

    protected Connector $connector;

    public function __construct(
        HomeService $homeService, UrlGenerator $urlGenerator, Translator $translator,
        ConfigurationConsulter $configurationConsulter,
        ContentObjectPublicationService $contentObjectPublicationService, ElementRightsService $elementRightsService,
        ConfigurationFormFactory $configurationFormFactory, Connector $connector
    )
    {
        parent::__construct(
            $homeService, $urlGenerator, $translator, $configurationConsulter, $contentObjectPublicationService,
            $elementRightsService, $configurationFormFactory
        );

        $this->connector = $connector;
    }

    /**
     * @throws \QuickformException
     */
    public function addConfigurationFieldsToForm(ConfigurationForm $configurationForm, Element $block): void
    {
        $translator = $this->getTranslator();

        $configurationForm->addElement(
            'select', BlockRenderer::CONFIGURATION_OBJECT_ID, $translator->trans('UseObject', [], Manager::CONTEXT),
            $this->getConnector()->getDisplayerObjects()
        );

        $contentObjectPublication =
            $this->getContentObjectPublicationService()->getFirstContentObjectPublicationForElement(
                $block
            );

        if ($contentObjectPublication)
        {
            $configurationForm->setDefaults(
                [self::CONFIGURATION_OBJECT_ID => $contentObjectPublication->get_content_object_id()]
            );
        }
    }

    public function displayRepositoryContent(Element $block): string
    {
        $content_object = $this->getObject($block);

        $display = ContentObjectRenditionImplementation::factory(
            $content_object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION
        );

        return $display->render();
    }

    public function getConnector(): Connector
    {
        return $this->connector;
    }

    protected function getDefaultTitle(): string
    {
        return $this->getTranslator()->trans('Displayer', [], Manager::CONTEXT);
    }

    public function get_content_object_display_attachment_url(ContentObject $attachment): ?string
    {
        return null;
    }
}
