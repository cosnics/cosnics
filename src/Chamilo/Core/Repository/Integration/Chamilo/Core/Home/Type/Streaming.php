<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block;
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

        $result[] = 'Chamilo\Core\Repository\ContentObject\Matterhorn\Storage\DataClass\Matterhorn';
        $result[] = 'Chamilo\Core\Repository\ContentObject\Slideshare\Storage\DataClass\Slideshare';
        $result[] = 'Chamilo\Core\Repository\ContentObject\Soundcloud\Storage\DataClass\Soundcloud';
        $result[] = 'Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass\Vimeo';
        $result[] = 'Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube';

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
        $contentObject = $this->get_object();

        $renditionImplementation = ContentObjectRenditionImplementation :: factory(
            $contentObject,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_DESCRIPTION,
            $this);

        return ContentObjectRendition :: factory($renditionImplementation)->render();
    }
}
