<?php
namespace Chamilo\Configuration\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Query\DataClassParameters;
use Chamilo\Libraries\Storage\Query\RetrieveProperties;
use Chamilo\Libraries\Storage\Query\Variable\PropertiesConditionVariable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Chamilo\Configuration\Repository
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class LanguageRepository
{

    private DataClassRepository $dataClassRepository;

    public function __construct(DataClassRepository $dataClassRepository)
    {
        $this->dataClassRepository = $dataClassRepository;
    }

    public function clearLanguageCache(): bool
    {
        return $this->getDataClassRepository()->getDataClassRepositoryCache()->truncateClass(Language::class);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Storage\DataClass\Language>
     */
    public function findLanguagesAsRecords(): ArrayCollection
    {
        return $this->getDataClassRepository()->records(
            Language::class, new DataClassParameters(
                retrieveProperties: new RetrieveProperties([new PropertiesConditionVariable(Language::class)])
            )
        );
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }
}