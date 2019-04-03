<?php

namespace Chamilo\Application\Lti\Domain\Outcome;

/**
 * Class ResultMessage
 *
 * @package Chamilo\Application\Lti\Domain\Outcome
 */
class ResultMessage
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $action;

    /**
     * Score between 0.0 and 1.0
     *
     * @var float
     */
    protected $score;

    /**
     * ResultMessage constructor.
     *
     * @param string $id
     * @param string $action
     * @param float $score
     */
    public function __construct(string $id, string $action, float $score)
    {
        $this->id = $id;
        $this->action = $action;
        $this->score = $score;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }
}