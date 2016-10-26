<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlShortRenditionImplementation extends HtmlRenditionImplementation
{

    /**
     *
     * @return string
     */
    public function render()
    {
        $object = $this->get_content_object();
        $url = \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $object->get_id(),
            $object->calculate_security_code());

        return '<span><a href="' . Utilities :: htmlentities($url) . '">' .
             Utilities :: htmlentities($object->get_title()) . '</a></span>';
    }
}
