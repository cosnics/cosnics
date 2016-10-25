<?php
namespace Chamilo\Core\Metadata\Traits;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Service\EntityTranslationService;

/**
 *
 * @package Chamilo\Core\Metadata\Traits
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
trait EntityTranslationTrait
{

    /**
     *
     * @var \Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation[]
     */
    private $translations;

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation[]
     */
    public function getTranslations()
    {
        if (! isset($this->translations))
        {
            $entity = DataClassEntityFactory :: getInstance()->getEntityFromDataClass($this);
            $entityTranslationService = new EntityTranslationService($entity);
            $this->translations = $entityTranslationService->getEntityTranslationsIndexedByIsocode();
        }

        return $this->translations;
    }

    /**
     *
     * @param string $isocode
     * @return string
     */
    public function getTranslationByIsocode($isocode)
    {
        $translations = $this->getTranslations();
        $bestMatchIsoCode = \Locale :: lookup(array_keys($translations), $isocode, true);

        if ($bestMatchIsoCode)
        {
            return $translations[$bestMatchIsoCode]->get_value();
        }
        else
        {
            return $this->getTranslationFallback();
        }
    }

    /**
     *
     * @return string
     */
    abstract public function getTranslationFallback();
}
