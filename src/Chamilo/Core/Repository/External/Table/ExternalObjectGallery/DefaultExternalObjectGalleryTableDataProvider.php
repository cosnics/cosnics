<?php
namespace Chamilo\Core\Repository\External\Table\ExternalObjectGallery;

use Chamilo\Libraries\Format\Table\Extension\GalleryTable\Extension\DataClassGalleryTable\DataClassGalleryTableDataProvider;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

class DefaultExternalObjectGalleryTableDataProvider extends DataClassGalleryTableDataProvider
{

    public function countData(?Condition $condition = null): int
    {
        return $this->get_component()->count_external_repository_objects($condition);
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        return $this->get_component()->retrieve_external_repository_objects(
            $condition, $orderBy, $offset, $count
        );
    }
}
