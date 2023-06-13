<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Service\Home;

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
use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Core\Repository\Service\Home\BlockRenderer;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Symfony\Component\Translation\Translator;

class FeederBlockRenderer extends BlockRenderer
    implements ConfigurableBlockRendererInterface, StaticBlockTitleInterface, ContentObjectPublicationBlockInterface,
    AnonymousBlockInterface
{
    public const CONTEXT = RssFeed::CONTEXT;

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
            'select', self::CONFIGURATION_OBJECT_ID, $translator->trans('UseObject', [], RssFeed::CONTEXT),
            $this->getConnector()->get_rss_feed_objects(), ['class' => 'form-control']
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
        //        if ($this->getSource() == self::SOURCE_AJAX)
        //        {
        //            return $this->getTranslator()->trans(
        //                'PleaseRefreshPageToSeeChanges', [], RssFeed::CONTEXT
        //            );
        //        }

        /**
         * @var \Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed $rssFeed
         */
        $rssFeed = $this->getObject($block);

        $html = [];

        $target = 'target="_blank"';

        $html[] = '<rss-feed-renderer rss-feed-url="' . $rssFeed->get_url() . '" number-of-entries="' .
            $rssFeed->get_number_of_entries() . '">';
        $html[] = '<ul class="rss_feeds">';

        $html[] = '<li ng-repeat="entry in main.feedEntries" class="rss_feed_item">';

        $glyph = new NamespaceIdentGlyph(
            'Chamilo\Core\Repository\ContentObject\RssFeed', true, false, false, IdentGlyph::SIZE_MINI
        );

        $html[] = $glyph->render() . ' ' . '<a href="{{ entry.link }}" ' . $target . '>{{ entry.title }}</a>';
        $html[] = '</li>';

        $html[] = '</ul>';

        $html[] = '<span style="font-weight: bold;" ng-show="main.feedEntries.length == 0">' .
            $this->getTranslator()->trans('NoFeedsFound', [], RssFeed::CONTEXT) . '</span>';
        $html[] = '</rss-feed-renderer>';

        return implode(PHP_EOL, $html);
    }

    public function getConnector(): Connector
    {
        return $this->connector;
    }

    protected function getDefaultTitle(): string
    {
        return $this->getTranslator()->trans('Feeder', [], RssFeed::CONTEXT);
    }

    /**
     * Displays the title of the feed or the generic title if no object selected
     */
    public function getTitle(Element $block, ?User $user = null): string
    {
        $contentObject = $this->getObject($block);

        if ($contentObject)
        {
            return $contentObject->get_title();
        }

        return parent::getTitle($block, $user);
    }
}
