<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Architecture\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;

class Displayer extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block implements ConfigurableInterface,
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
    public function __construct(
        Application $application, HomeService $homeService, Block $block, $source = self::SOURCE_DEFAULT,
        $defaultTitle = ''
    )
    {
        parent:: __construct($application, $homeService, $block, $source, Translation:: get('Displayer'));
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    public function displayContent()
    {
        $content_object = $this->getObject();

        $display = ContentObjectRenditionImplementation:: factory(
            $content_object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_DESCRIPTION,
            $this
        );

        return $display->render();
    }
}
