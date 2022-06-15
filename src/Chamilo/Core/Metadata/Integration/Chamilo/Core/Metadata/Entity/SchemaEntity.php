<?php
namespace Chamilo\Core\Metadata\Integration\Chamilo\Core\Metadata\Entity;

use Chamilo\Core\Metadata\Entity\DataClassEntity;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
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
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getDisplayName()
     */
    public function getDisplayName()
    {
        return $this->getDataClass()->getTranslationByIsocode(Translation::getInstance()->getLanguageIsocode());
    }

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getIcon()
     */
    public function getIcon($size = IdentGlyph::SIZE_MINI)
    {
        $glyph = new NamespaceIdentGlyph(
            $this->getType(), true, false, false, $size
        );

        return $glyph->render();
    }

    /**
     *
     * @see \Chamilo\Core\Metadata\Entity\DataClassEntity::getType()
     */
    public function getType()
    {
        return Translation::get('Schema', null, 'Chamilo\Core\Metadata');
    }
}