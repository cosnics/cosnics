<?php
namespace Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Architecture\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;

class Feeder extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block implements ConfigurableInterface, 
    StaticBlockTitleInterface, ContentObjectPublicationBlockInterface
{

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param int $source
     * @param string $defaultTitle
     */
    public function __construct(Application $application, HomeService $homeService, Block $block, 
        $source = self::SOURCE_DEFAULT, $defaultTitle = '')
    {
        parent::__construct($application, $homeService, $block, $source, Translation::get('Feeder'));
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    /**
     * Displays the title of the feed or the generic title if no object selected
     * 
     * @return string
     */
    public function getTitle()
    {
        $content_object = $this->getObject();
        
        if ($content_object)
        {
            return $content_object->get_title();
        }
        
        return parent::getTitle();
    }

    /**
     * Returns the html to display when the block is configured.
     * 
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function displayContent()
    {
        if ($this->getSource() == self::SOURCE_AJAX)
        {
            return Translation::getInstance()->getTranslation(
                'PleaseRefreshPageToSeeChanges', 
                null, 
                'Chamilo\Core\Repository\ContentObject\RssFeed\Integration\Chamilo\Core\Home');
        }
        
        $content_object = $this->getObject();

        $ajaxUrlBuilder = new Redirect([
            Application::PARAM_CONTEXT => 'Chamilo\\Libraries\\Ajax',
            Application::PARAM_ACTION => 'FetchRssEntries'
        ]);

        return $this->getTwig()->render(
            'Chamilo\Core\Repository\ContentObject\RssFeed:RssFeedRenderer.html.twig',
            [
                'ELEMENT_ID' => $this->getBlock()->getId(),
                'URL' => $content_object->get_url(),
                'NUMBER_OF_ENTRIES' => $content_object->get_number_of_entries(),
                'TARGET' => $this->getLinkTarget(),
                'FETCH_RSS_ENTRIES_URL' => $ajaxUrlBuilder->getUrl()
            ]
        );
    }
}
