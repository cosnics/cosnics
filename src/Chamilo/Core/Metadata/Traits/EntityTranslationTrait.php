<?php
namespace Chamilo\Core\Metadata\Traits;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Locale;

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
     * @return \Chamilo\Core\Metadata\Entity\DataClassEntityFactory
     * @throws \Exception
     */
    public function getDataClassEntityFactory()
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            DataClassEntityFactory::class
        );
    }

    /**
     *
     * @param string $isocode
     *
     * @return string
     * @throws \Exception
     */
    public function getTranslationByIsocode($isocode)
    {
        $translations = $this->getTranslations();
        $bestMatchIsoCode = Locale::lookup(array_keys($translations), $isocode, true);

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

    /**
     *
     * @return \Chamilo\Core\Metadata\Storage\DataClass\EntityTranslation[]
     * @throws \Exception
     */
    public function getTranslations()
    {
        if (!isset($this->translations))
        {
            $entity = $this->getDataClassEntityFactory()->getEntityFromDataClass($this);
            $entityTranslationService = new EntityTranslationService();
            $this->translations = $entityTranslationService->getEntityTranslationsIndexedByIsocode($entity);
        }

        return $this->translations;
    }
}
