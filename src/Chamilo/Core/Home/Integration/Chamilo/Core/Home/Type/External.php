<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;
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
class External extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block implements ConfigurableInterface,
    StaticBlockTitleInterface
{
    const CONFIGURATION_SCROLLING = 'scrolling';
    const CONFIGURATION_HEIGHT = 'height';

    /**
     *
     * @see \Chamilo\Core\Home\Architecture\ConfigurableInterface::getConfigurationVariables()
     */
    public function getConfigurationVariables()
    {
        return array(self :: CONFIGURATION_OBJECT_ID, self :: CONFIGURATION_SCROLLING, self :: CONFIGURATION_HEIGHT);
    }

    /**
     * Returns the list of type names that this block can map to.
     *
     * @return array
     */
    public static function getSupportedTypes()
    {
        return array('Chamilo\Core\Repository\ContentObject\Link\Storage\DataClass\Link');
    }

    public function __construct($renderer, $block)
    {
        parent :: __construct($renderer, $block, Translation :: get('External'));
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    public function getMinHeight()
    {
        return 300;
    }

    public function getHeight()
    {
        $result = (int) $this->getBlock()->getSetting(self :: CONFIGURATION_HEIGHT, 300);
        $result = max($this->getMinHeight(), $result);
        return $result;
    }

    public function displayContent()
    {
        $height = '300px';
        $frameborder = '0';
        $scrolling = $this->getBlock()->getSetting(self :: CONFIGURATION_SCROLLING, 'no');
        $src = $this->getObject() ? $this->getObject()->get_url() : '';
        $height = $this->get_height();

        $result = <<<EOT

        <iframe src="$src" width="100%" height="$height" frameborder="$frameborder" scrolling="$scrolling">
            <p>Your browser does not support iframes.</p>
        </iframe>

EOT;
        return $result;
    }
}
