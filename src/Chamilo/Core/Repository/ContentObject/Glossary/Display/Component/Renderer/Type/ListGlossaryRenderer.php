<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\Type;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Glossary\Display\Component\Renderer\GlossaryRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Class to render the glossary as a list
 *
 * @package repository\content_object\glossary
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ListGlossaryRenderer extends GlossaryRenderer
{

    /**
     * Renders the glossary
     *
     * @return string
     */
    public function render()
    {
        $complex_content_object_items = $this->get_objects();

        if ($complex_content_object_items->count() == 0)
        {
            $html = [];

            $html[] = '<div class="title" style="background-color: #e6e6e6; border: 1px solid grey; padding: 5px;
                   font-weight: bold; color: #666666; text-align: center">';
            $html[] = Translation::get('NoSearchResults', null, Utilities::COMMON_LIBRARIES);
            $html[] = '</div>';
        }

        foreach($complex_content_object_items as $complex_content_object_item)
        {
            $html[] = $this->display_content_object(
                $complex_content_object_item->get_ref_object(), $complex_content_object_item
            );
        }

        return implode(PHP_EOL, $html);
    }

    /**
     * Displays a single glossary item
     *
     * @param GlossaryItem $content_object
     * @param ComplexcontentObjectItem $complex_content_object_item
     *
     * @return string
     */
    public function display_content_object($content_object, $complex_content_object_item)
    {
        $html = [];

        $html[] = '<div class="title" style="background-color: #e6e6e6; border: 1px solid grey; padding: 5px;
                   font-weight: bold; color: #666666">';
        $html[] = '<div style="padding-top: 1px; float: left">';
        $html[] = $content_object->get_title();
        $html[] = '</div>';
        $html[] = '<div style="float: right">';
        $html[] = $this->get_actions($complex_content_object_item);
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp</div>';
        $html[] = '</div>';
        $html[] = '<div class="description">';

        $html[] = ContentObjectRenditionImplementation::launch(
            $content_object, ContentObjectRendition::FORMAT_HTML, ContentObjectRendition::VIEW_DESCRIPTION,
            $this->get_component()
        );

        $html[] = '</div><br />';

        return implode(PHP_EOL, $html);
    }

    /**
     * Returns the actions for a single glossary item
     *
     * @param ComplexContentObjectItem $complex_content_object_item
     *
     * @return string
     */
    public function get_actions($complex_content_object_item)
    {
        $component = $this->get_component();

        $toolbar = new Toolbar();
        if ($component->is_allowed_to_edit_content_object(EDIT_RIGHT))
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Edit'), new FontAwesomeGlyph('pencil-alt'),
                    $component->get_complex_content_object_item_update_url($complex_content_object_item),
                    ToolbarItem::DISPLAY_ICON
                )
            );
        }

        if ($component->is_allowed_to_delete_child())
        {
            $toolbar->add_item(
                new ToolbarItem(
                    Translation::get('Delete'), new FontAwesomeGlyph('times'),
                    $component->get_complex_content_object_item_delete_url($complex_content_object_item),
                    ToolbarItem::DISPLAY_ICON, true
                )
            );
        }

        return $toolbar->as_html();
    }
}