<?php
namespace Chamilo\Core\Repository\Service\Home;

use Chamilo\Core\Home\Architecture\Interfaces\AnonymousBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Translation\Translation;

class DisplayerBlock extends BlockRenderer
    implements ConfigurableBlockInterface, StaticBlockTitleInterface, ContentObjectPublicationBlockInterface,
    AnonymousBlockInterface
{

    public function __construct(HomeService $homeService, $defaultTitle = '')
    {
        parent::__construct($homeService, Translation::get('Displayer'));
    }

    public function displayRepositoryContent(): string
    {
        $content_object = $this->getObject();

        $display = ContentObjectRenditionImplementation::factory(
            $content_object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION, $this
        );

        return $display->render();
    }
}
