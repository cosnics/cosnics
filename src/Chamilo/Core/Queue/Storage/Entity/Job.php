<?php
namespace Chamilo\Core\Queue\Storage\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;

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
    public const STATUS_CREATED = 1;

    public const STATUS_FAILED_NO_LONGER_VALID = 5;

    public const STATUS_FAILED_RETRY = 6;

    public const STATUS_IN_PROGRESS = 3;

    public const STATUS_SENT_TO_QUEUE = 2;

    public const STATUS_SUCCESS = 4;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    protected $date;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var JobParameter[] | \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Chamilo\Core\Queue\Storage\Entity\JobParameter", mappedBy="job")
     */
    protected $jobParameters;

    /**
     * @var string
     *
     * @ORM\Column(name="processor_class", type="string")
     */
    protected $processorClass;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false, length=2)
     */
    protected $status;

    /**
     * Job constructor.
     */
    public function __construct()
    {
        $this->jobParameters = new ArrayCollection();
    }

    /**
     * @return \DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return \Chamilo\Core\Queue\Storage\Entity\Job
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $parameterName
     *
     * @return string
     */
    public function getParameter($parameterName)
    {
        foreach ($this->jobParameters as $parameter)
        {
            if ($parameter->getName() == $parameterName)
            {
                return $parameter->getValue();
            }
        }

        return null;
    }

    /**
     * @return \Chamilo\Core\Queue\Storage\Entity\JobParameter[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getParameters()
    {
        return $this->jobParameters;
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
     *
     * @return \Chamilo\Core\Queue\Storage\Entity\Job
     */
    public function setProcessorClass(string $processorClass)
    {
        $this->processorClass = $processorClass;

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
     * @param string $parameterName
     * @param string $value
     *
     * @return \Chamilo\Core\Queue\Storage\Entity\Job
     */
    public function setParameter($parameterName, $value)
    {
        if (empty($parameterName))
        {
            throw new InvalidArgumentException('The given parameter name can not be empty');
        }

        if (empty($value))
        {
            throw new InvalidArgumentException(
                sprintf('The given parameter value for parameter %s can not be empty', $parameterName)
            );
        }

        foreach ($this->jobParameters as $parameter)
        {
            if ($parameter->getName() == $parameterName)
            {
                $parameter->setValue($value);

                return $this;
            }
        }

        $parameter = new JobParameter();
        $parameter->setName($parameterName)->setJob($this)->setValue($value);

        $this->jobParameters->add($parameter);

        return $this;
    }

}