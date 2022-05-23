<?php
namespace Chamilo\Core\Home\Rights\Table\BlockTypeTargetEntity;

use ArrayIterator;
use Chamilo\Core\Home\Repository\HomeRepository;
use Chamilo\Core\Home\Rights\Service\BlockTypeRightsService;
use Chamilo\Core\Home\Rights\Storage\Repository\RightsRepository;
use Chamilo\Libraries\Format\Table\Extension\RecordTable\RecordTableDataProvider;
use Chamilo\Libraries\Format\Table\Table;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

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
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        parent::__construct($table);

        $this->blockTypeRightsService = new BlockTypeRightsService(new RightsRepository(), new HomeRepository());
    }

    /**
     * Counts the data
     *
     * @param Condition $condition
     *
     * @return int
     */
    public function count_data($condition)
    {
        return $this->retrieve_data($condition, null, null)->count();
    }

    /**
     * Returns the data as a resultset
     *
     * @param Condition $condition
     * @param int $offset
     * @param int $count
     * @param \Chamilo\Libraries\Storage\Query\OrderBy $order_property
     *
     * @return \ArrayIterator
     */
    public function retrieve_data($condition, $offset, $count, $order_property = null)
    {
        $blockTypes = $this->blockTypeRightsService->getBlockTypesWithTargetEntities();

        $compareModifier =
            !is_null($order_property) && $order_property->getFirst()->getDirection() != SORT_DESC ? 1 : - 1;

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