<?php
namespace Chamilo\Core\Repository\ContentObject\File\Common\Rendition\Html;

use Chamilo\Core\Repository\ContentObject\File\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\Format\Structure\ActionBar\BootstrapGlyph;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Platform\Translation;

class HtmlInlineRenditionImplementation extends HtmlRenditionImplementation
{
    const DEFAULT_HEIGHT = 768;
    const DEFAULT_WIDTH = 1024;
    const PARAM_WIDTH = 'width';
    const PARAM_HEIGHT = 'height';
    const PARAM_BORDER = 'border';
    const PARAM_MARGIN_HORIZONTAL = 'margin-horizontal';
    const PARAM_MARGIN_VERTICAL = 'margin-vertical';
    const PARAM_ALIGN = 'align';
    const PARAM_ALT = 'alt';
    const PARAM_STYLE = 'style';

    public function render($parameters)
    {
        $object = $this->get_content_object();
        
        $class = __NAMESPACE__ . '\Extension\HtmlInline' .
             (string) StringUtilities :: getInstance()->createString($object->get_extension())->upperCamelize() .
             'RenditionImplementation';
        
        if (! class_exists($class))
        {
            $document_type = $object->determine_type();
            $class = __NAMESPACE__ . '\Type\HtmlInline' .
                 (string) StringUtilities :: getInstance()->createString($document_type)->upperCamelize() .
                 'RenditionImplementation';
        }
        
        $rendition = new $class($this->get_context(), $this->get_content_object());
        return $rendition->render($parameters);
    }

    /**
     *
     * @param string $classes
     * @return string
     */
    public function renderActions($classes = '')
    {
        $buttonToolBarRenderer = new ButtonToolBarRenderer($this->getButtonToolBar($classes));
        return $buttonToolBarRenderer->render();
    }

    public function getButtonToolBar($classes = '')
    {
        $object = $this->get_content_object();
        $name = $object->get_filename();
        
        $label = '<small>(' . Filesystem :: format_file_size($object->get_filesize()) . ')</small>';
        
        $buttonToolBar = new ButtonToolBar();
        
        $buttonToolBar->addItem(
            new Button(
                Translation :: get('DownloadFile', array('LABEL' => $label)), 
                new BootstrapGlyph('download'), 
                $this->getDownloadUrl(), 
                Button :: DISPLAY_ICON_AND_LABEL, 
                false, 
                $classes, 
                '_blank'));
        
        return $buttonToolBar;
    }

    public function getDownloadUrl()
    {
        $object = $this->get_content_object();
        
        return \Chamilo\Core\Repository\Manager :: get_document_downloader_url(
            $object->get_id(), 
            $object->calculate_security_code());
    }
}
