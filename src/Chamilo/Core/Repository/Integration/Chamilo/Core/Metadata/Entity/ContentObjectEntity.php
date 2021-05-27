<?php
namespace Chamilo\Core\Repository\Integration\Chamilo\Core\Metadata\Entity;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
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
        return DataManager::retrieve_by_id(ContentObject::class, $dataClassIdentifier);
    }

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getDisplayName()
     */
    public function getDisplayName()
    {
        return $this->getDataClass()->get_title();
    }

    /**F
     *
     * @param integer $size
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function getIcon($size = IdentGlyph::SIZE_MINI)
    {
        $dataClass = $this->getDataClass();
        $dataClassName = $this->getDataClassName();

        if ($dataClass instanceof ContentObject)
        {
            return $dataClass->get_icon_image($size);
        }
        else
        {
            $glyph = new NamespaceIdentGlyph(
                $dataClassName::package(), true, false, false, $size, []
            );

            return $glyph->render();
        }
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
}