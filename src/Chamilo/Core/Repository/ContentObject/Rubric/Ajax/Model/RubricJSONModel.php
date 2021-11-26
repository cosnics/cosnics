<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax\Model;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\Level;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RubricJSONModel
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $id;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $useScores = true;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $useRelativeWeights = true;

    /**
     * RubricJSONModel constructor.
     *
     * @param int $id
     * @param bool $useScores
     */
    public function __construct(int $id, bool $useScores, bool $useRelativeWeights)
    {
        $this->id = $id;
        $this->useScores = $useScores;
        $this->useRelativeWeights = $useRelativeWeights;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function useScores(): ?bool
    {
        return $this->useScores;
    }

    /**
     * @param RubricData $rubricData
     *
     * @return RubricData
     */
    public function updateRubricData(RubricData $rubricData)
    {
        $rubricData->setUseScores($this->useScores());

        return $rubricData;
    }

    /**
     * @param RubricData $rubricData
     *
     * @return RubricJSONModel
     */
    public static function fromRubricData(RubricData $rubricData)
    {
        return new self(
            $rubricData->getId(), $rubricData->useScores(), $rubricData->useRelativeWeights()
        );
    }
}
