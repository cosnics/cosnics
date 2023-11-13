<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\Format\Theme;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\Extension
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
class HtmlInlineHtmlRenditionImplementation extends HtmlInlineRenditionImplementation
{

    /**
     * @param array $parameters
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render($parameters = [])
    {
        /** @var File $object */
        $object = $this->get_content_object();
        $url = \Chamilo\Core\Repository\Manager::get_document_downloader_url(
            $object->get_id(), 
            $object->calculate_security_code()) . '&display=1';
        
        $html = array();

//        if(!$this->isIframeAllowed($object))
//        {
//            return $this->renderThumbnail($object);
//        }

        $html[] = '<div class="text-container">';
        $html[] = '<iframe class="text-frame" src="' . $url .
             '" sandbox="allow-forms allow-pointer-lock allow-same-origin allow-scripts">';
        $html[] = '</iframe>';
        $html[] = $this->renderActions();
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     * @param File $contentObject
     *
     * @return bool
     */
    protected function isIframeAllowed(File $contentObject)
    {
        $owner = $contentObject->get_owner();
        if(!$owner->is_teacher())
        {
            return false;
        }

        return true;
    }
}
