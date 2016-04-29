<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Configuration\Configuration;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class HtmlInlineOfficeRenditionImplementation extends HtmlInlineRenditionImplementation
{
    // View type
    const VIEW_TYPE_FULL = 'full';
    const VIEW_TYPE_EMBED = 'embed';

    // Viewer URL
    const VIEWER_URL_FULL = 'https://view.officeapps.live.com/op/view.aspx?src=';
    const VIEWER_URL_EMBED = 'https://view.officeapps.live.com/op/embed.aspx?src=';

    /**
     *
     * @return string
     */
    public function getViewerType()
    {
        $viewerType = Configuration :: get_instance()->get_setting(array(File :: package(), 'office_viewer_type'));

        if (is_null($viewerType))
        {
            return self :: VIEW_TYPE_EMBED;
            return self :: VIEW_TYPE_FULL;
        }
    }

    /**
     *
     * @return string
     */
    public function getViewerBaseUrl()
    {
        switch ($this->getViewerType())
        {
            case self :: VIEW_TYPE_FULL :
                return self :: VIEW_TYPE_FULL;
                break;
            case self :: VIEW_TYPE_EMBED :
                return self :: VIEWER_URL_EMBED;
                break;
        }
    }

    /**
     *
     * @return string
     */
    public function getIFrameSource()
    {   return $this->getViewerBaseUrl() .
             urlencode(
                'http://desiderius.ehb.be/index.php?application=Chamilo%5CCore%5CRepository&go=DocumentDownloader&object=996311&security_code=6a50835b34be4840c32ee870d4cc5b2eee383ddb');
        return $this->getViewerBaseUrl() . urlencode($this->getDownloadUrl());
    }

    /**
     *
     * @see \Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html\HtmlInlineRenditionImplementation::render()
     */
    public function render($parameters)
    {
        $html = array();

        if ($this->canBeDisplayed())
        {
            $html[] = '<div class="office-viewer-container">';

            $html[] = '<div class="office-viewer-content">';
            $html[] = '<div class="alert alert-info office-viewer-sidebar center-block">';

            $alertText = array();

            $alertText[] = '<span class="glyphicon glyphicon-lock"></span>';
            $alertText[] = '<span class="office-viewer-full-screen-message">' .
                 Translation :: get('OfficeViewerFullScreen') . '</span>';
            $alertText[] = '<a class="btn btn-default btn-office-viewer-minimize">' .
                 Translation :: get('OfficeViewerExitFullScreen') . '</a>';

            $html[] = implode(' ', $alertText);
            $html[] = '</div>';

            $html[] = '<iframe class="' . implode(' ', $this->getViewerFrameClasses()) . '" data-url="' .
                 $this->getIFrameSource() . '">';
            $html[] = '</iframe>';

            $html[] = '</div>';

            $html[] = $this->renderActions();

            $html[] = '</div>';

            $html[] = ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(File :: package(), true) . 'OfficeViewer.js');
            $html[] = ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(Utilities :: COMMON_LIBRARIES, true) .
                     'Plugin/Jquery/jquery.fullscreen.min.js');
        }
        else
        {
            $html[] = $this->getErrorMessage();
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string[]
     */
    public function getViewerFrameClasses()
    {
        $classes = array('office-viewer-frame');

        if ($this->getViewerType() == self :: VIEW_TYPE_EMBED)
        {
            $classes[] = 'office-viewer-frame-embed';
        }

        return $classes;
    }

    /**
     *
     * @return boolean
     */
    public function allowsFullScreen()
    {
        return $this->getViewerType() != self :: VIEW_TYPE_EMBED;
    }

    /**
     *
     * @param string $classes
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar
     */
    public function getButtonToolbar($classes = '')
    {
        $buttonToolBar = parent :: getButtonToolBar($classes);

        if ($this->allowsFullScreen())
        {
            $buttonToolBar->addItem(
                new Button(
                    Translation :: get('ViewFullScreen'),
                    new BootstrapGlyph('fullscreen'),
                    '#',
                    Button :: DISPLAY_ICON_AND_LABEL,
                    false,
                    'btn-office-viewer-full-screen'));
        }

        return $buttonToolBar;
    }

    /**
     *
     * @param string[] $parameters
     * @return string
     */
    public function getErrorMessage()
    {
        $html = array();

        $html[] = '<div class="alert alert-info">';
        $html[] = '<h4>' . Translation :: get('LiveViewNotSupportedTitle') . '</h4>';
        $html[] = Translation :: get('LiveViewNotSupported');
        $html[] = '<br />';
        $html[] = $this->renderActions('btn-info');
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return boolean
     */
    public function canBeDisplayed()
    {
        return $this->get_content_object()->get_filesize() <= $this->getSizeLimit();
    }

    /**
     *
     * @return integer
     */
    abstract public function getSizeLimit();
}
