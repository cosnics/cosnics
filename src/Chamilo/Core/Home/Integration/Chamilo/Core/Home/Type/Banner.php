<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type;

/**
 * A "Static" block.
 * I.e. a block that display the title and description of an object. Usefull to display free html
 * text.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 * @package home.block
 */
class Banner extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block
{

    /**
     * Returns the list of type names that this block can map to.
     *
     * @return array
     */
    public static function get_supported_types()
    {
        $result = array();
        $result[] = \Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement :: get_type_name();
        $result[] = \Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass\Description :: get_type_name();
        $result[] = \Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass\Note :: get_type_name();

        return $result;
    }

    public function is_visible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    public function render_header()
    {
        $block_id = $this->get_block_info()->get_id();
        $icon_url = $this->get_icon();

        $title = $this->display_title();
        if ($this->get_view() == self :: BLOCK_VIEW)
        { // i.e. in widget view it is the portal configuration that decides to show/hide
            $description_style = $this->get_block_info()->is_visible() ? '' : ' style="display: none"';
        }
        else
        {
            $description_style = '';
        }

        $html = array();
        $html[] = '<div class="portal-block portal_banner" id="portal_block_' . $block_id .
             '" style="background-image: url(' . $icon_url . ');">';
        $html[] = $title;
        $html[] = '<div class="entry-content description"' . $description_style . '">';

        return implode(PHP_EOL, $html);
    }

    public function render_footer()
    {
        $html = array();

        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function display_title()
    {
        $title = htmlspecialchars($this->get_title());
        $actions = $this->display_actions();

        $html = array();
        $html[] = '<div class="title"><div style="float: left;" class="entry-title">' . $title . '</div>';
        $html[] = $actions;
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function display_content()
    {
        return $this->get_object()->get_description();
    }
}
