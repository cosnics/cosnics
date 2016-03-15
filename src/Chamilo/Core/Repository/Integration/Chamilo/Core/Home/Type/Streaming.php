<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Platform\Translation;

/**
 * Block to display streaming media.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 */
class Streaming extends Block implements ConfigurableInterface, StaticBlockTitleInterface
{

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     * @param \Chamilo\Core\Home\Storage\DataClass\Block $block
     * @param string $defaultTitle
     */
    public function __construct(Application $application, HomeService $homeService, Block $block, $defaultTitle = '')
    {
        parent :: __construct($application, $homeService, $block, Translation :: get('Streaming'));
    }

    /**
     * Returns the list of type names that this block can map to.
     *
     * @return array
     */
    public static function getSupportedTypes()
    {
        $result = array();

        $result[] = 'Chamilo\Core\Repository\ContentObject\Matterhorn\Storage\DataClass\Matterhorn';
        $result[] = 'Chamilo\Core\Repository\ContentObject\Slideshare\Storage\DataClass\Slideshare';
        $result[] = 'Chamilo\Core\Repository\ContentObject\Soundcloud\Storage\DataClass\Soundcloud';
        $result[] = 'Chamilo\Core\Repository\ContentObject\Vimeo\Storage\DataClass\Vimeo';
        $result[] = 'Chamilo\Core\Repository\ContentObject\Youtube\Storage\DataClass\Youtube';

        return $result;
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    public function displayContent()
    {
        $contentObject = $this->getObject();

        $renditionImplementation = ContentObjectRenditionImplementation :: factory(
            $contentObject,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_DESCRIPTION,
            $this);

        return ContentObjectRendition :: factory($renditionImplementation)->render();
    }
}
