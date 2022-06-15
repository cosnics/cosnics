<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class HtmlInlineMediaRenditionImplementation extends HtmlInlineRenditionImplementation
{

    /**
     *
     * @return string
     */
    abstract public function getMediaElementType();

    /**
     *
     * @param string[] $parameters
     * @return string
     */
    public function getErrorMessage($show = true)
    {
        $html = [];
        
        $titleVariable = StringUtilities::getInstance()->createString($this->getMediaElementType())->upperCamelize() .
             'PlaybackNotSupportedTitle';
        
        $html[] = '<div class="alert alert-warning media-element-js-playback-error ' . ($show ? 'show' : 'hidden') . '">';
        $html[] = '<h4>' . Translation::get($titleVariable) . '</h4>';
        $html[] = Translation::get('PlaybackNotSupported');
        $html[] = '<br />';
        $html[] = $this->renderActions(['btn-warning']);
        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function getMediaUrl()
    {
        $object = $this->get_content_object();
        
        return $this->getDownloadUrl() . '&display=1';
    }
}
