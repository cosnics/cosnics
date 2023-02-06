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
     * @var GradeBookCSVImportScoreJSONModel[]
     *
     * @Type("array<Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model\GradeBookCSVImportScoreJSONModel>")
     */
    protected $results;

    /**
     * GradeBookCSVImportColumnJSONModel constructor.
     *
     * @param ?string $label
     * @param array $results
     */
    public function __construct(?string $label, array $results = array())
    {
        $this->label = $label;
        $this->results = $results;
    }

    /**
     * @return ?string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return GradeBookCSVImportScoreJSONModel[]
     */
    public function getResults(): array
    {
        return $this->results;
    }
}