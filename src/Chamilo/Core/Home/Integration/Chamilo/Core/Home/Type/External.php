<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link;
use Chamilo\Libraries\Platform\Translation;

/**
 * An "External" block.
 * I.e. a block that displays a page's content in an iFrame. Usefull to integrate external pages.
 * 
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent.opprecht@unige.ch
 * @package home.block
 */
class External extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block
{

    /**
     * Returns the list of type names that this block can map to.
     * 
     * @return array
     */
    public static function get_supported_types()
    {
        $result = array();
        $result[] = Link :: get_type_name();
        return $result;
    }

    public function __construct($parent, $block_info, $configuration)
    {
        $default_title = Translation :: get('External');
        parent :: __construct($parent, $block_info, $configuration, $default_title);
    }

    public function is_visible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    public function get_min_height()
    {
        return 300;
    }

    public function get_height()
    {
        $result = $this->get('height', '300');
        $resut = (int) $result;
        $result = max($this->get_min_height(), $result);
        return $result;
    }

    public function display_content()
    {
        $height = '300px';
        $frameborder = '0';
        $scrolling = $this->get('scrolling', 'no');
        $src = $this->get_object() ? $this->get_object()->get_url() : '';
        $height = $this->get_height();
        
        $result = <<<EOT

        <iframe src="$src" width="100%" height="$height" frameborder="$frameborder" scrolling="$scrolling">
            <p>Your browser does not support iframes.</p>
        </iframe>

EOT;
        return $result;
    }
}
