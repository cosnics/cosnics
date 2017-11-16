<?php
namespace Chamilo\Core\Repository\External\Renderer\Type;

use Chamilo\Core\Repository\External\ExternalObjectDisplay;
use Chamilo\Core\Repository\External\Renderer\Renderer;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

class SlideshowRenderer extends Renderer
{
    const SLIDESHOW_INDEX = 'slideshow';
    const SLIDESHOW_AUTOPLAY = 'autoplay';

    public function as_html()
    {
        if (! Request::get(self::SLIDESHOW_INDEX))
        {
            $slideshow_index = 0;
        }
        else
        {
            $slideshow_index = Request::get(self::SLIDESHOW_INDEX);
        }
        
        $external_repository_object = $this->retrieve_external_repository_objects(
            $this->get_condition(), 
            null, 
            $slideshow_index, 
            1)->next_result();
        $external_repository_object_count = $this->count_external_repository_objects($this->get_condition());
        if ($external_repository_object_count == 0)
        {
            $html[] = Display::normal_message(Translation::get('NoExternalObjectsAvailable'), true);
            return implode(PHP_EOL, $html);
        }
        
        $first = ($slideshow_index == 0);
        $last = ($slideshow_index == $external_repository_object_count - 1);
        
        $play_toolbar = new Toolbar();
        $play_toolbar->add_items($this->get_external_repository_object_actions($external_repository_object));
        if (Request::get(self::SLIDESHOW_AUTOPLAY))
        {
            $play_toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Stop'), 
                    Theme::getInstance()->getCommonImagePath('Action/Stop'), 
                    $this->get_url(
                        array(
                            self::SLIDESHOW_INDEX => Request::get(self::SLIDESHOW_INDEX), 
                            self::SLIDESHOW_AUTOPLAY => null)), 
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $play_toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Play'), 
                    Theme::getInstance()->getCommonImagePath('Action/Play'), 
                    $this->get_url(
                        array(
                            self::SLIDESHOW_INDEX => Request::get(self::SLIDESHOW_INDEX), 
                            self::SLIDESHOW_AUTOPLAY => 1)), 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        $navigation_toolbar = new Toolbar();
        if (! $first)
        {
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation::get('First'), 
                    Theme::getInstance()->getCommonImagePath('Action/First'), 
                    $this->get_url(array(self::SLIDESHOW_INDEX => 0)), 
                    ToolbarItem::DISPLAY_ICON));
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Previous'), 
                    Theme::getInstance()->getCommonImagePath('Action/Prev'), 
                    $this->get_url(array(self::SLIDESHOW_INDEX => $slideshow_index - 1)), 
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation::get('First'), 
                    Theme::getInstance()->getCommonImagePath('Action/FirstNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Previous'), 
                    Theme::getInstance()->getCommonImagePath('Action/PrevNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        if (! $last)
        {
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Next'), 
                    Theme::getInstance()->getCommonImagePath('Action/Next'), 
                    $this->get_url(array(self::SLIDESHOW_INDEX => $slideshow_index + 1)), 
                    ToolbarItem::DISPLAY_ICON));
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Last'), 
                    Theme::getInstance()->getCommonImagePath('Action/Last'), 
                    $this->get_url(array(self::SLIDESHOW_INDEX => $external_repository_object_count - 1)), 
                    ToolbarItem::DISPLAY_ICON));
        }
        else
        {
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Next'), 
                    Theme::getInstance()->getCommonImagePath('Action/NextNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
            $navigation_toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Last'), 
                    Theme::getInstance()->getCommonImagePath('Action/LastNa'), 
                    null, 
                    ToolbarItem::DISPLAY_ICON));
        }
        
        $table = array();
        $table[] = '<table id="slideshow" class="table table-striped table-bordered table-hover table-data">';
        $table[] = '<thead>';
        $table[] = '<tr>';
        $table[] = '<th class="actions" style="width: 25%; text-align: left;">';
        $table[] = $play_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '<th style="text-align: center;">' . htmlspecialchars($external_repository_object->get_title()) .
             ' - ' . ($slideshow_index + 1) . '/' . $external_repository_object_count . '</th>';
        $table[] = '<th class="navigation" style="width: 25%; text-align: right;">';
        $table[] = $navigation_toolbar->as_html();
        $table[] = '</th>';
        $table[] = '</tr>';
        $table[] = '</thead>';
        $table[] = '<tbody>';
        $table[] = '<tr><td colspan="3" style="background-color: #f9f9f9; text-align: center;">';
        $table[] = ExternalObjectDisplay::factory($external_repository_object)->get_preview();
        $table[] = '</td></tr>';
        
        $table[] = '</tbody>';
        $table[] = '</table>';
        
        $table[] = ExternalObjectDisplay::factory($external_repository_object)->get_properties_table();
        
        if (Request::get(self::SLIDESHOW_AUTOPLAY))
        {
            if (! $last)
            {
                $autoplay_url = $this->get_url(
                    array(self::SLIDESHOW_AUTOPLAY => 1, self::SLIDESHOW_INDEX => $slideshow_index + 1));
            }
            else
            {
                $autoplay_url = $this->get_url(array(self::SLIDESHOW_AUTOPLAY => 1, self::SLIDESHOW_INDEX => 0));
            }
            
            $html[] = '<meta http-equiv="Refresh" content="10; url=' . $autoplay_url . '" />';
        }
        
        $html[] = implode(PHP_EOL, $table);
        return implode(PHP_EOL, $html);
    }
}
