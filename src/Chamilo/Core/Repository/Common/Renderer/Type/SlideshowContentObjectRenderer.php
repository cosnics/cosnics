<?php
namespace Chamilo\Core\Repository\Common\Renderer\Type;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectService;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;

class SlideshowContentObjectRenderer extends ContentObjectRenderer
{
    const SLIDESHOW_INDEX = 'slideshow';
    const SLIDESHOW_AUTOPLAY = 'autoplay';

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

        $workspace = $this->get_repository_browser()->getWorkspace();
        $contentObjectService = new ContentObjectService(new ContentObjectRepository());

        $content_object = $contentObjectService->getContentObjectsForWorkspace(
            $workspace,
            ConditionFilterRenderer :: factory(FilterData :: get_instance($workspace), $workspace),
            1,
            $slideshow_index)->next_result();

        $content_object_count = $contentObjectService->countContentObjectsForWorkspace(
            $workspace,
            ConditionFilterRenderer :: factory(FilterData :: get_instance($workspace), $workspace));

        if ($content_object_count == 0)
        {
            $html[] = Display :: normal_message(Translation :: get('NoContentObjectsAvailable'), true);
            return implode(PHP_EOL, $html);
        }

        $is_first = ($slideshow_index == 0);
        $is_last = ($slideshow_index == $content_object_count - 1);

        $parameters = $this->get_parameters();

        $actionsToolBar = new Toolbar();
        $actionsToolBar->add_items($this->get_content_object_actions($content_object));
        if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
        {
            $parameters[self :: SLIDESHOW_INDEX] = Request :: get(self :: SLIDESHOW_INDEX);
            $parameters[self :: SLIDESHOW_AUTOPLAY] = null;

            $actionsToolBar->add_item(
                new ToolbarItem(
                    Translation :: get('Stop', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Stop'),
                    $this->get_url($parameters),
                    ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $parameters[self :: SLIDESHOW_INDEX] = Request :: get(self :: SLIDESHOW_INDEX);
            $parameters[self :: SLIDESHOW_AUTOPLAY] = 1;

            $actionsToolBar->add_item(
                new ToolbarItem(
                    Translation :: get('Play', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagePath('Action/Play'),
                    $this->get_url($parameters),
                    ToolbarItem :: DISPLAY_ICON));
        }

        $html = array();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12">';

        $html[] = '<div class="panel panel-default panel-slideshow">';

        $html[] = '<div class="panel-heading">';
        $html[] = '<h3 class="panel-title">' . htmlspecialchars($content_object->get_title()) . ' - ' .
             ($slideshow_index + 1) . '/' . $content_object_count . '</h3>';
        $html[] = '</div>';

        $html[] = '<div class="panel-body">';

        $html[] = '<table class="table-slideshow">';
        $html[] = '<tbody>';
        $html[] = '<tr>';

        $html[] = '<td class="control control-left">';

        $previousNavigation = array();

        if (! $is_first)
        {
            $parameters = $this->get_parameters();
            $parameters[self :: SLIDESHOW_INDEX] = 0;

            $previousNavigation[] = '<a href="' . $this->get_url($parameters) .
                 '"><span class="glyphicon glyphicon-step-backward"></span></a>';

            $parameters = $this->get_parameters();
            $parameters[self :: SLIDESHOW_INDEX] = $slideshow_index - 1;
            $previousNavigation[] = '<a href="' . $this->get_url($parameters) .
                 '"><span class="glyphicon glyphicon-triangle-left"></span></a>';
        }
        else
        {
            $previousNavigation[] = '<span class="glyphicon glyphicon-step-backward disabled"></span>';
            $previousNavigation[] = '<span class="glyphicon glyphicon-triangle-left disabled"></span>';
        }

        $html[] = implode('', $previousNavigation);

        $html[] = '</td>';

        $html[] = '<td class="thumbnail-container">';
        $html[] = ContentObjectRenditionImplementation :: factory(
            $content_object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_PREVIEW,
            $this->get_repository_browser())->render();
        $html[] = '</td>';

        $html[] = '<td class="control control-right">';

        $nextNavigation = array();

        if (! $is_last)
        {
            $parameters = $this->get_parameters();
            $parameters[self :: SLIDESHOW_INDEX] = $slideshow_index + 1;
            $nextNavigation[] = '<a href="' . $this->get_url($parameters) .
                 '"><span class="glyphicon glyphicon-triangle-right"></span></a>';

            $parameters = $this->get_parameters();
            $parameters[self :: SLIDESHOW_INDEX] = $content_object_count - 1;
            $nextNavigation[] = '<a href="' . $this->get_url($parameters) .
                 '"><span class="glyphicon glyphicon-step-forward"></span></a>';
        }
        else
        {
            $nextNavigation[] = '<span class="glyphicon glyphicon-triangle-right disabled"></span>';
            $nextNavigation[] = '<span class="glyphicon glyphicon-step-forward disabled"></span>';
        }

        $html[] = implode('', $nextNavigation);

        $html[] = '</td>';

        $html[] = '</tr>';
        $html[] = '</tbody>';
        $html[] = '</table>';

        $html[] = '<div class="row panel-slideshow-actions">';
        $html[] = '<div class="col-xs-12">';

        $actionsToolBarRenderer = new ButtonToolBarRenderer($actionsToolBar->convertToButtonToolBar(false));

        $html[] = $actionsToolBarRenderer->render();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        if (Request :: get(self :: SLIDESHOW_AUTOPLAY))
        {
            if (! $is_last)
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

        return implode(PHP_EOL, $html);
    }
}
