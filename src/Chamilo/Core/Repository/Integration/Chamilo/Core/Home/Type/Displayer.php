<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Architecture\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block;
use Chamilo\Libraries\Translation\Translation;

class Displayer extends Block
    implements ConfigurableInterface, StaticBlockTitleInterface, ContentObjectPublicationBlockInterface
{

    public function __construct(
        HomeService $homeService, $source = self::SOURCE_DEFAULT, $defaultTitle = ''
    )
    {
        parent::__construct($homeService, $source, Translation::get('Displayer'));
    }

    public function displayContent()
    {
        $content_object = $this->getObject();

        $display = ContentObjectRenditionImplementation::factory(
            $content_object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION, $this
        );

        return $display->render();
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }
}
