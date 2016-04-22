<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Platform\Translation;
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
        $html = array();

        $downloadAction = $this->renderDownloadAction();

        $titleVariable = StringUtilities :: getInstance()->createString($this->getMediaElementType())->upperCamelize() .
             'PlaybackNotSupportedTitle';

        $html[] = '<div class="alert alert-warning media-element-js-playback-error ' . ($show ? 'show' : 'hidden') . '">';
        $html[] = '<h4>' . Translation :: get($titleVariable) . '</h4>';
        $html[] = Translation :: get('PlaybackNotSupported');
        $html[] = '<br />';
        $html[] = $this->renderDownloadAction('btn-warning');
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

    public function getDownloadUrl()
    {
        $object = $this->get_content_object();

        return \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $object->get_id(),
            $object->calculate_security_code());
    }

    /**
     *
     * @param string $classes
     * @return string
     */
    public function renderDownloadAction($classes)
    {
        $object = $this->get_content_object();
        $name = $object->get_filename();

        $url = \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $object->get_id(),
            $object->calculate_security_code());

        $label = '<small>(' . Filesystem :: format_file_size($object->get_filesize()) . ')</small>';

        $buttonToolBar = new ButtonToolBar();
        $buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);

        $buttonToolBar->addItem(
            new Button(
                Translation :: get('DownloadFile', array('LABEL' => $label)),
                new BootstrapGlyph('download'),
                $this->getDownloadUrl(),
                Button :: DISPLAY_ICON_AND_LABEL,
                false,
                $classes,
                '_blank'));

        return $buttonToolBarRenderer->render();
    }
}
