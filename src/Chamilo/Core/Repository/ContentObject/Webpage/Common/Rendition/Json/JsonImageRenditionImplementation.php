<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Common\Rendition\Json;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\Json\Type\JsonImageContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\Webpage\Common\Rendition\JsonRenditionImplementation;
use Chamilo\Core\Repository\Manager;

class JsonImageRenditionImplementation extends JsonRenditionImplementation
{

    public function render()
    {
        $object = $this->get_content_object();

        if ($object->is_image())
        {
            $url = $this->getWebPathBuilder()->getBasePath() . Manager::get_document_downloader_url(
                    $object->get_id(), $object->calculate_security_code()
                );

            return [JsonImageContentObjectRendition::PROPERTY_URL => $url];
        }
        else
        {
            return ContentObjectRendition::launch($this);
        }
    }
}
