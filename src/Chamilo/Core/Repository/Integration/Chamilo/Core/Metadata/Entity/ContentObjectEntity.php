<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Entity;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ContentObjectEntity extends DataClassEntity
{

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getDataClassObject()
     */
    public function getDataClassObject($dataClassName, $dataClassIdentifier)
    {
        return DataManager::retrieve_by_id(ContentObject::class_name(), $dataClassIdentifier);
    }

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getType()
     */
    public function getType()
    {
        $dataClassName = $this->getDataClassName();
        return Translation::get('TypeName', null, $dataClassName::package());
    }

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getIcon()
     */
    public function getIcon($size = Theme::ICON_MINI)
    {
        $dataClass = $this->getDataClass();
        $dataClassName = $this->getDataClassName();
        
        if ($dataClass instanceof ContentObject)
        {
            return $dataClass->get_icon_image($size);
        }
        else
        {
            return Theme::getInstance()->getImage(
                'Logo/' . $size, 
                'png', 
                $this->getType(), 
                null, 
                ToolbarItem::DISPLAY_ICON, 
                false, 
                $dataClassName::package());
        }
    }

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getDisplayName()
     */
    public function getDisplayName()
    {
        return $this->getDataClass()->get_title();
    }
}