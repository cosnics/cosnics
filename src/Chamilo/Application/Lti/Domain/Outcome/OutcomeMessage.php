<?php

namespace Chamilo\Application\Lti\Domain\Outcome;

/**
 * Class OutcomeMessage
 *
 * @package Chamilo\Application\Lti\Domain\Outcome
 */
class OutcomeMessage
{
    /**
     * @var string
     */
    protected $messageId;

    /**
     * @var string
     */
    protected $integrationClass;

    /**
     * @var string
     */
    protected $resultId;

    /**
     * @var string
     */
    protected $operation;

    /**
     * Score between 0.0 and 1.0
     *
     * @var float
     */
    protected $score;

    /**
     * OutcomeMessage constructor.
     *
     * @param string $messageId
     * @param string $integrationClass
     * @param string $resultId
     * @param string $operation
     * @param float $score
     */
    public function __construct(string $messageId, string $integrationClass, string $resultId, string $operation, float $score)
    {
        $this->messageId = $messageId;
        $this->resultId = $resultId;
        $this->operation = $operation;
        $this->score = $score;
        $this->integrationClass = $integrationClass;
    }

    /**
     * @return string
     */
    public function getIntegrationClass(): string
    {
        return $this->integrationClass;
    }

    /**
     * @return string
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * @return string
     */
    public function getResultId(): string
    {
        return $this->resultId;
    }

    /**
     * @return string
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @return bool
     */
    public function isValidScore()
    {
        return $this->score >= 0.0 && $this->score <= 1.0;
    }

    /**
     * @param bool $success
     * @param string $message
     *
     * @return array
     */
    public function getResponseParametersArray(bool $success, string $message)
    {
        return [
            'RESPONSE_MESSAGE_ID' => uniqid(), 'REQUEST_MESSAGE_ID' => $this->getMessageId(),
            'STATUS' => $success ? 'success' : 'failure', 'OPERATION' => $this->getOperation(),
            'MESSAGE' => $message
        ];
    }
}