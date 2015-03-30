<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\User\Integration\Chamilo\Core\Home\Block;
use Chamilo\Libraries\Platform\Translation;

/**
 * Block to display streaming media.
 * 
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class Streaming extends Block
{

    /**
     * Returns the list of type names that this block can map to.
     * 
     * @return array
     */
    public static function get_supported_types()
    {
        $result = array();
        
        $result[] = 'core\repository\content_object\matternhorn\Matterhorn';
        $result[] = 'core\repository\content_object\slideshare\slideshare';
        $result[] = 'core\repository\content_object\soundcloud\Soundcloud';
        $result[] = 'core\repository\content_object\vimeo\Vimeo';
        $result[] = 'core\repository\content_object\youtube\Youtube';
        
        return $result;
    }

    public function __construct($parent, $block_info, $configuration)
    {
        parent :: __construct($parent, $block_info, $configuration);
        $this->default_title = Translation :: get('Streaming');
    }

    public function is_visible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    /**
     * Returns the html to display when the block is configured.
     * 
     * @return string
     */
    public function display_content()
    {
        $content_object = $this->get_object();
        
        $rendition_implementation = ContentObjectRenditionImplementation :: factory(
            $content_object, 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_DESCRIPTION, 
            $this);
        $rendition = ContentObjectRendition :: factory($rendition_implementation);
        
        return $rendition->render();
    }
}
