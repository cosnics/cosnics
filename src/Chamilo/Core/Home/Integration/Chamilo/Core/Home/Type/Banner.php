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
    public static function getSupportedTypes()
    {
        $result = array();
        $result[] = 'Chamilo\Core\Repository\ContentObject\Announcement\Storage\DataClass\Announcement';
        $result[] = 'Chamilo\Core\Repository\ContentObject\Description\Storage\DataClass\Description';
        $result[] = 'Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass\Note';

        return $result;
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    public function renderHeader()
    {
        $block_id = $this->getBlock()->getId();
        $icon_url = $this->getIcon();

        $title = $this->displayTitle();

        if ($this->getView() == self :: BLOCK_VIEW)
        { // i.e. in widget view it is the portal configuration that decides to show/hide
            $description_style = $this->getBlock()->isVisible() ? '' : ' style="display: none"';
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

    public function renderFooter()
    {
        $html = array();

        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function displayTitle()
    {
        $title = htmlspecialchars($this->getTitle());
        $actions = $this->displayActions();

        $html = array();
        $html[] = '<div class="title"><div style="float: left;" class="entry-title">' . $title . '</div>';
        $html[] = $actions;
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function displayContent()
    {
        return $this->getObject()->get_description();
    }
}
