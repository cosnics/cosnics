<?php
namespace Chamilo\Core\Home\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Home\Architecture\ConfigurableInterface;
use Chamilo\Core\Home\Interfaces\StaticBlockTitleInterface;

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
class StaticContent extends \Chamilo\Core\Repository\Integration\Chamilo\Core\Home\Block implements
    ConfigurableInterface, StaticBlockTitleInterface
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
        $result[] = 'Chamilo\Core\Repository\ContentObject\Note\Storage\DataClass\Note';

        return $result;
    }

    public function isVisible()
    {
        return true; // i.e.display on homepage when anonymous
    }

    /**
     * Determines whether the current user may view the object.
     *
     * @param ContentObject $object The content object to be tested.
     * @return boolean Whether the current user may view the content onject.
     */
    public function isViewAttachmentAllowed($object)
    {
        // Is the current user the owner?
        return $object->get_owner_id() == $this->getUserId();
    }
}
