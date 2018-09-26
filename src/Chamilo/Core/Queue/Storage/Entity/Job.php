<?php

namespace Chamilo\Core\Queue\Storage\Entity;

use Chamilo\Core\Queue\Domain\JobParametersInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Serializer;

/**
 * @package Chamilo\Core\Queue\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @ORM\Entity(repositoryClass="Chamilo\Core\Queue\Storage\Repository\JobEntityRepository")
 * @ORM\Table(
 *     name="queue_job",
 *     indexes={
 *          @ORM\Index(name="qn_date", columns={"date"}),
 *          @ORM\Index(name="qn_status", columns={"status"})
 *     }
 * )
 */
class Job
{
    const STATUS_CREATED = 1;
    const STATUS_SENT_TO_QUEUE = 2;
    const STATUS_IN_PROGRESS = 3;
    const STATUS_SUCCESS = 4;
    const STATUS_FAILED = 5;
    const STATUS_RETRY = 6;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="processor_class", type="string")
     */
    protected $processorClass;

    /**
     * @var string
     *
     * @ORM\Column(name="parameters_class", type="string")
     */
    protected $parametersClass;

    /**
     * @var string
     *
     * @ORM\Column(name="parameters", type="text")
     */
    protected $parameters;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    protected $date;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, length=2)
     */
    protected $status;

    /**
     * @var JobParametersInterface
     */
    protected $jobParameters;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getProcessorClass(): string
    {
        return $this->processorClass;
    }

    /**
     * @param string $processorClass
     */
    public function setProcessorClass(string $processorClass)
    {
        $this->processorClass = $processorClass;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return \Chamilo\Core\Queue\Storage\Entity\Job
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return \Chamilo\Core\Queue\Storage\Entity\Job
     */
    public function setStatus(int $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param \Chamilo\Core\Queue\Domain\JobParametersInterface $jobParameters
     */
    public function setJobParameters(JobParametersInterface $jobParameters)
    {
        $this->jobParameters = $jobParameters;
    }

    /**
     * @return \Chamilo\Core\Queue\Domain\JobParametersInterface
     */
    public function getJobParameters()
    {
        return $this->jobParameters;
    }

    /**
     * @param \JMS\Serializer\Serializer $serializer
     */
    public function serializeParameters(Serializer $serializer)
    {
        $this->parametersClass = get_class($this->jobParameters);
        $this->parameters = $serializer->serialize($this->jobParameters, 'json');
    }

    /**
     * @param \JMS\Serializer\Serializer $serializer
     */
    public function deserializeParameters(Serializer $serializer)
    {
        if(!class_exists($this->parametersClass))
        {
            $this->jobParameters = null;
            return;
        }

        $this->jobParameters = $serializer->deserialize($this->parameters, $this->parametersClass, 'json');
    }

}