<?php
namespace Chamilo\Core\Repository\Service\Home;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Architecture\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Interfaces\AnonymousBlockRendererInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Translation\Translation;

class DisplayerBlockRenderer extends BlockRenderer
    implements ConfigurableInterface, StaticBlockTitleInterface, ContentObjectPublicationBlockInterface,
    AnonymousBlockRendererInterface
{

    public function __construct(HomeService $homeService, $defaultTitle = '')
    {
        parent::__construct($homeService, Translation::get('Displayer'));
    }

    public function displayContent()
    {
        $content_object = $this->getObject();

        $display = ContentObjectRenditionImplementation::factory(
            $content_object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION, $this
        );

        return $display->render();
    }
}
