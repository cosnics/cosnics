<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Libraries\Storage\DataManager\Doctrine\ORM\CommonEntityRepository;

/**
 * Class RubricRepository
 *
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Storage\Repository
 *
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class RubricDataRepository extends CommonEntityRepository
{

    /**
     * @param RubricData $rubricData
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function saveRubricData(RubricData $rubricData)
    {
        $this->saveEntity($rubricData, false);

        foreach($rubricData->getTreeNodes() as $treeNode)
        {
            $this->saveEntity($treeNode, false);
        }

        $this->flush();
    }

    /**
     * @param int $rubricDataId
     *
     * @return RubricData
     */
    public function findEntireRubricById(int $rubricDataId)
    {
        $qb = $this->createQueryBuilder('rd')
            ->addSelect('tn')
            ->addSelect('rn')
            ->addSelect('lv')
            ->addSelect('ch')
            ->join('rd.treeNodes', 'tn')
            ->join('rd.rootNode', 'rn')
            ->leftJoin('rd.levels', 'lv')
            ->leftJoin('rd.choices', 'ch')
            ->where('rd.id = :id')
            ->setParameter('id', $rubricDataId);

        return $qb->getQuery()->getResult()[0];
    }

}
