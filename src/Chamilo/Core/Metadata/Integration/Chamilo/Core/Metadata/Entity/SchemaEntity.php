<?php
namespace Chamilo\Core\Metadata\Integration\Chamilo\Core\Metadata\Entity;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\File\Integration\Chamilo\Core\Metadata\Entity
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class SchemaEntity extends DataClassEntity
{

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getType()
     */
    public function getType()
    {
        return Translation::get('Schema', null, 'Chamilo\Core\Metadata');
    }

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getIcon()
     */
    public function getIcon($size = Theme::ICON_MINI)
    {
        return Theme::getInstance()->getImage('Logo/' . $size, 'png', $this->getType());
    }

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getDisplayName()
     */
    public function getDisplayName()
    {
        return $this->getDataClass()->getTranslationByIsocode(Translation::getInstance()->getLanguageIsocode());
    }
}