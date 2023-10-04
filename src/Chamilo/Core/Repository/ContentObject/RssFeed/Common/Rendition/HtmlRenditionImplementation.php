<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition;

use Chamilo\Core\Repository\ContentObject\RssFeed\Common\RenditionImplementation;
use Chamilo\Core\Repository\ContentObject\RssFeed\Storage\DataClass\RssFeed;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Utilities\ResourceManager;

class HtmlRenditionImplementation extends RenditionImplementation
{
    use DependencyInjectionContainerTrait;

    /**
     * Renders RSS Feeds
     * 
     * @return string
     */
    protected function renderRssFeeds()
    {
        $this->initializeContainer();
        $twig = $this->getTwig();

        $object = $this->get_content_object();

        $ajaxUrlBuilder = new Redirect([
            Application::PARAM_CONTEXT => 'Chamilo\\Libraries\\Ajax',
            Application::PARAM_ACTION => 'FetchRssEntries'
        ]);

        $ajaxUrl = $ajaxUrlBuilder->getUrl();

        return $twig->render(
            'Chamilo\Core\Repository\ContentObject\RssFeed:RssFeedRenderer.html.twig',
            ['URL' => $object->get_url(), 'NUMBER_OF_ENTRIES' => $object->get_number_of_entries(), 'AJAX_URL' => $ajaxUrl]
        );
    }
}
