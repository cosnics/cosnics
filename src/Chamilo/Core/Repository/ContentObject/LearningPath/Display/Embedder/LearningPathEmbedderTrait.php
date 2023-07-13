<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder;

use Chamilo\Libraries\Platform\ChamiloRequest;

/**
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Display\Embedder
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
trait LearningPathEmbedderTrait
{
    public function getEmbeddedContentObjectIdentifier(): string
    {
        return $this->getRequest()->query->get(Embedder::PARAM_EMBEDDED_CONTENT_OBJECT_ID);
    }

    abstract public function getRequest(): ChamiloRequest;
}