<?php
namespace Chamilo\Application\Weblcms\Renderer\PublicationList\Type;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\NotificationMessage;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class SlideshowContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{
    const SLIDESHOW_INDEX = 'slideshow';
    const SLIDESHOW_AUTOPLAY = 'autoplay';

    public function __construct($tool_browser, $parameters = array())
    {
        parent :: __construct($tool_browser, $parameters);
        $this->addWarning();
    }

    public function as_html()
    {
        if (! Request :: get(self :: SLIDESHOW_INDEX))
        {
            $slideshow_index = 0;
        }
        else
        {
            $slideshow_index = Request :: get(self :: SLIDESHOW_INDEX);
        }

        $publications = $this->get_publications($slideshow_index, 1);
        $publication = $publications[0];
        $publication_count = $this->get_publication_count();
        if ($publication_count == 0)
        {
            $html[] = Display :: normal_message(
                Translation :: get('NoPublications', null, Utilities :: COMMON_LIBRARIES),
                true);
            return implode(PHP_EOL, $html);
        }

        $first = ($slideshow_index == 0);
        $last = ($slideshow_index == $publication_count - 1);

        $content_object = $this->get_content_object_from_publication($publication);

        $play_toolbar = $this->get_publication_actions($publication, false);
        if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
        {
            $play_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Stop', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Stop'),
                    $this->get_url(
                        array(
                            self :: SLIDESHOW_INDEX => Request :: get(self :: SLIDESHOW_INDEX),
                            self :: SLIDESHOW_AUTOPLAY => null)),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $play_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Play', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Play'),
                    $this->get_url(
                        array(
                            self :: SLIDESHOW_INDEX => Request :: get(self :: SLIDESHOW_INDEX),
                            self :: SLIDESHOW_AUTOPLAY => 1)),
                    ToolbarItem :: DISPLAY_ICON));
        }

        $navigation_toolbar = new Toolbar();

        if (! $first)
        {
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('First', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/First'),
                    $this->get_url(array(self :: SLIDESHOW_INDEX => 0)),
                    ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Prev'),
                    $this->get_url(array(self :: SLIDESHOW_INDEX => $slideshow_index - 1)),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('First', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/FirstNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/PrevNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        if (! $last)
        {
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Next'),
                    $this->get_url(array(self :: SLIDESHOW_INDEX => $slideshow_index + 1)),
                    ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Last', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Last'),
                    $this->get_url(array(self :: SLIDESHOW_INDEX => $publication_count - 1)),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/NextNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation :: get('Last', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/LastNa'),
                    null,
                    ToolbarItem :: DISPLAY_ICON));
        }

        $table = array();
        $table[] = '<table id="slideshow" class="table table-striped table-bordered table-hover table-responsive">';
        $table[] = '<thead>';
        $table[] = '<tr>';
        $table[] = '<th class="actions" style="width: 25%; text-align: left;">';
        $table[] = $play_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '<th style="text-align: center;">' . htmlspecialchars($content_object->get_title()) . ' - ' .
             ($slideshow_index + 1) . '/' . $publication_count . '</th>';
        $table[] = '<th class="navigation" style="width: 25%; text-align: right;">';
        $table[] = $navigation_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '</tr>';
        $table[] = '</thead>';
        $table[] = '<tbody>';
        $table[] = '<tr><td colspan="3" style="background-color: #f9f9f9; text-align: center;">';
        $table[] = ContentObjectRenditionImplementation :: launch(
            $content_object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_PREVIEW,
            $this);
        // $table[] = '<a href="' . $download_url . '" target="about:blank"><img
        // src="' . $view_url . '" alt="" style="max-width: 800px; border: 1px
        // solid #f0f0f0;' . $additional_styles . '"/></a>';
        $table[] = '</td></tr>';
        $table[] = '<tr><td class="header" colspan="3">' .
             Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES) . '</td></tr>';
        $table[] = '<tr><td colspan="3">' . $content_object->get_description() . '</td></tr>';
        $table[] = '</tbody>';
        $table[] = '</table>';

        if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
        {
            if (! $last)
            {
                $autoplay_url = $this->get_url(
                    array(self :: SLIDESHOW_AUTOPLAY => 1, self :: SLIDESHOW_INDEX => $slideshow_index + 1));
            }
            else
            {
                $autoplay_url = $this->get_url(array(self :: SLIDESHOW_AUTOPLAY => 1, self :: SLIDESHOW_INDEX => 0));
            }

            $html[] = '<meta http-equiv="Refresh" content="10; url=' . $autoplay_url . '" />';
        }

        $html[] = implode(PHP_EOL, $table);
        return implode(PHP_EOL, $html);
    }

    public function addWarning()
    {
        $messages = Session :: retrieve(Application :: PARAM_MESSAGES);
        $messages[Application :: PARAM_MESSAGE_TYPE][] = NotificationMessage :: TYPE_WARNING;
        $messages[Application :: PARAM_MESSAGE][] = Translation :: get('BrowserWarningPreview');

        Session :: register(Application :: PARAM_MESSAGES, $messages);
    }
}
