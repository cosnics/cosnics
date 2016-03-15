<?php
namespace Chamilo\Core\Repository\ContentObject\Link\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;

class Linker extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block implements ConfigurableInterface,
    StaticBlockTitleInterface
{

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param string $defaultTitle
     */
    public function __construct(Application $application, HomeService $homeService, Block $block, $defaultTitle = '')
    {
        parent :: __construct($application, $homeService, $block, Translation :: get('Linker'));
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    public function displayContent()
    {
        $content_object = $this->getObject();
        $url = htmlentities($content_object->get_url());

        $html = array();
        $html[] = $content_object->get_description();
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . $url . '" target="_blank" >' . $url .
             '</a></div>';

        return implode(PHP_EOL, $html);
    }
}
