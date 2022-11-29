<?php

namespace Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model;

use JMS\Serializer\Annotation\Type;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @package Chamilo\Core\Repository\ContentObject\GradeBook\Display\Ajax\Model
 *
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class GradeBookCSVImportScoreJSONModel
{
    /**
     * @var int
     *
     * @Type("integer")
     */
    protected $id;

    /**
     * @var float|null
     *
     * @Type("float")
     */
    protected $score;

    /**
     * @var bool
     *
     * @Type("bool")
     */
    protected $authAbsent;

    /**
     * @var string|null
     *
     * @Type("string")
     */
    protected $comment;

    /**
     * GradeBookCSVImportScoreJSONModel constructor.
     *
     * @param int $id
     * @param float|null $score
     * @param bool $authAbsent
     * @param string|null $comment
     */
    public function __construct(int $id, ?float $score, bool $authAbsent, ?string $comment)
    {
        $this->id = $id;
        $this->score = $score;
        $this->authAbsent = $authAbsent;
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float|null
     */
    public function getScore(): ?float
    {
        return $this->score;
    }

    /**
     * @return bool
     */
    public function isAuthAbsent(): bool
    {
        return $this->authAbsent;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }
}