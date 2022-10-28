<?php
namespace Chamilo\Core\Home\Rights\Table\BlockTypeTargetEntity;

use ArrayIterator;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Service\BlockTypeRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Format\Table\ListTableRenderer;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Builds the table for the BlockTypeTargetEntity data class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class BlockTypeTargetEntityTableDataProvider extends RecordTableDataProvider
{

    /**
     *
     * @var BlockTypeRightsService
     */
    protected $blockTypeRightsService;

    /**
     * BlockTypeTargetEntityTableDataProvider constructor.
     *
     * @param ListTableRenderer $table
     */
    public function __construct(ListTableRenderer $table)
    {
        parent::__construct($table);

        $this->blockTypeRightsService = new BlockTypeRightsService(new RightsRepository(), new HomeRepository());
    }

    public function countData(?Condition $condition = null): int
    {
        return $this->retrieveData($condition, null, null)->count();
    }

    public function retrieveData(
        ?Condition $condition = null, ?int $offset = null, ?int $count = null, ?OrderBy $orderBy = null
    ): ArrayCollection
    {
        $blockTypes = $this->blockTypeRightsService->getBlockTypesWithTargetEntities();

        $compareModifier = !is_null($orderBy) && $orderBy->getFirst()->getDirection() != SORT_DESC ? 1 : - 1;

        usort(
            $blockTypes, function ($item1, $item2) use ($compareModifier) {
            return strcmp($item1['block_type'], $item2['block_type']) * $compareModifier;
        }
        );

        if (!is_null($offset) && !is_null($count))
        {
            $blockTypes = array_splice($blockTypes, $offset, $count);
        }

        return new ArrayIterator($blockTypes);
    }
}