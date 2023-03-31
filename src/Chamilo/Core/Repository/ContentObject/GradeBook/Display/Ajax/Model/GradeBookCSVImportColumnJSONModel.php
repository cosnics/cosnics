<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model;

use JMS\Serializer\Annotation\Type;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookCSVImportColumnJSONModel
{
    /**
     * @var string|null
     *
     * @Type("string")
     */
    protected $label;

    /**
     * @var float|null
     *
     * @Type("float")
     */
    protected $maxScore;

    /**
     * @var GradeBookCSVImportScoreJSONModel[]
     *
     * @Type("array<Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCSVImportScoreJSONModel>")
     */
    protected $results;

    /**
     * GradeBookCSVImportColumnJSONModel constructor.
     *
     * @param string|null $label
     * @param float|null $maxScore
     * @param array $results
     */
    public function __construct(?string $label, ?float $maxScore, array $results = array())
    {
        $this->label = $label;
        $this->maxScore = $maxScore;
        $this->results = $results;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return float|null
     */
    public function getMaxScore(): ?float
    {
        return $this->maxScore;
    }

    /**
     * @return GradeBookCSVImportScoreJSONModel[]
     */
    public function getResults(): array
    {
        return $this->results;
    }
}