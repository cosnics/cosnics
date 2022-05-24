<?php

namespace Chamilo\Core\Repository\Publication\Table;

use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;

class PublicationTableDataProvider extends DataClassTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->getPublicationAggregator()->countPublicationAttributes(
            PublicationAggregator::ATTRIBUTES_TYPE_USER, $this->get_component()->getUser()->getId(), $condition
        );
    }

    /**
     * @return \Chamilo\Core\Repository\Publication\Service\PublicationAggregatorInterface
     */
    protected function getPublicationAggregator()
    {
        $dependencyInjectionContainer = DependencyInjectionContainerBuilder::getInstance()->createContainer();

        return $dependencyInjectionContainer->get(PublicationAggregator::class);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    )
    {
        return $this->getPublicationAggregator()->getContentObjectPublicationsAttributes(
            PublicationAggregator::ATTRIBUTES_TYPE_USER, $this->get_component()->getUser()->getId(), $condition, $count,
            $offset, $orderBy
        );
    }
}
