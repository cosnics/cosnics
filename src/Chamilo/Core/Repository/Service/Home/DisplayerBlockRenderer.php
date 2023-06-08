<?php
namespace Chamilo\Core\Repository\Service\Home;

use Chamilo\Core\Home\Architecture\Interfaces\AnonymousBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ConfigurableBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\ContentObjectPublicationBlockInterface;
use Chamilo\Core\Home\Architecture\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;

class DisplayerBlockRenderer extends BlockRenderer
    implements ConfigurableBlockInterface, StaticBlockTitleInterface, ContentObjectPublicationBlockInterface,
    AnonymousBlockInterface
{

    public function displayRepositoryContent(Element $block): string
    {
        $content_object = $this->getObject($block);

        $display = ContentObjectRenditionImplementation::factory(
            $content_object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION
        );

        return $display->render();
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
