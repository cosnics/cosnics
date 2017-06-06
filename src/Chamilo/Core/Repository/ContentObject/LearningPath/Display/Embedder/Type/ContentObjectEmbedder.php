<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Type;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder\Embedder;

class ContentObjectEmbedder extends Embedder
{

    /**
     *
     * @see \core\repository\content_object\learning_path\display\Embedder::render()
     */
    public function render()
    {
        $content_object_display = ContentObjectRenditionImplementation::launch(
            $this->treeNode->getContentObject(),
            ContentObjectRendition::FORMAT_HTML, 
            ContentObjectRendition::VIEW_FULL,
            $this->get_application());
        
        return $content_object_display;
    }
}