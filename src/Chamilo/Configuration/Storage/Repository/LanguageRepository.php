<?php
namespace Chamilo\Configuration\Storage\Repository;

use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Libraries\Storage\DataManager\Repository\DataClassRepository;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
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
        return $this->getDataClassRepository()->getDataClassRepositoryCache()->truncate(Language::class);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<\Chamilo\Configuration\Storage\DataClass\Language>
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     */
    public function findLanguagesAsRecords(): ArrayCollection
    {
        return $this->getDataClassRepository()->records(
            Language::class, new RecordRetrievesParameters(
                new RetrieveProperties([new PropertiesConditionVariable(Language::class)])
            )
        );
    }

    protected function getDataClassRepository(): DataClassRepository
    {
        return $this->dataClassRepository;
    }
}