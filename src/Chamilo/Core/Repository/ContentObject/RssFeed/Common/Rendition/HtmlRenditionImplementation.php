<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Common\Rendition;

use Chamilo\Core\Repository\ContentObject\RssFeed\Common\RenditionImplementation;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Traits\DependencyInjectionContainerTrait;
use Chamilo\Libraries\File\Redirect;

class HtmlRenditionImplementation extends RenditionImplementation
{
    use DependencyInjectionContainerTrait;

    /**
     * Renders RSS Feeds
     * 
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function renderRssFeeds()
    {
        $this->initializeContainer();

        $object = $this->get_content_object();

        $ajaxUrlBuilder = new Redirect([
            Application::PARAM_CONTEXT => 'Chamilo\\Libraries\\Ajax',
            Application::PARAM_ACTION => 'FetchRssEntries'
        ]);

        $html = [
            '<div class="px-3 pt-1 pb-3">',
            $this->getTwig()->render(
                'Chamilo\Core\Repository\ContentObject\RssFeed:RssFeedRenderer.html.twig',
                [
                    'ELEMENT_ID' => 'preview',
                    'URL' => $object->get_url(),
                    'NUMBER_OF_ENTRIES' => $object->get_number_of_entries(),
                    'SHOW_ENTRIES_CONTENT' => true,
                    'FETCH_RSS_ENTRIES_URL' => $ajaxUrlBuilder->getUrl()
                ]
            ),
            '</div>'
        ];
        return implode(PHP_EOL, $html);
    }

    /**
     * @param $url
     *
     * @return string
     */
    protected function getSanitizedUrl($url)
    {
        if(strpos($url, 'http') !== 0) {
            $url = 'http://' . $url;
        }

        return htmlentities($url);
    }
}
