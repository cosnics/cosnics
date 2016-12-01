<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\HtmlRenditionImplementation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlPreviewRenditionImplementation extends HtmlRenditionImplementation
{

    /**
     *
     * @return string
     */
    public function render()
    {
        $contentObject = $this->get_content_object();
        
        if ($contentObject->is_image())
        {
            $url = \Chamilo\Core\Repository\Manager::get_document_downloader_url(
                $contentObject->get_id(), 
                $contentObject->calculate_security_code());
            
            return '<img src="' . $url . '" alt="' . htmlentities($contentObject->get_title()) . '" class="thumbnail" />';
        }
        else
        {
            return ContentObjectRendition::launch($this);
        }
    }
}
