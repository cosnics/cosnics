<?php

namespace Chamilo\Core\Repository\Publication\Table;

use Chamilo\Core\Repository\Publication\Service\PublicationAggregator;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Table\Extension\DataClassTable\DataClassTableDataProvider;

class PublicationTableDataProvider extends DataClassTableDataProvider
{

    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        return $this->getPublicationAggregator()->getContentObjectPublicationsAttributes(
            PublicationAggregator::ATTRIBUTES_TYPE_USER, $this->get_component()->getUser()->getId(), $condition, $count,
            $offset, $order_property
        );
    }

    public function count_data($condition)
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
}
