<?php
namespace Chamilo\Core\Metadata\Traits;

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

    public function getTranslations()
    {
        if (! isset($this->translations))
        {
            $entityTranslationService = new EntityTranslationService($this);
            $this->translations = $entityTranslationService->getEntityTranslationsIndexedByIsocode();
        }

        return $this->translations;
    }
}
