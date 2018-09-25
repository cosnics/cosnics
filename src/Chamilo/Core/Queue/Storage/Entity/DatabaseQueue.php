<?php

namespace Chamilo\Core\Queue\Storage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package Chamilo\Core\Queue\Storage\Entity
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 *
 * @deprecated ONLY FOR USE FOR DATABASE CREATION FOR THE DBAL QUEUE, DO NOT USE THIS ENTITY
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="queue_queue",
 *     indexes={
 *          @ORM\Index(name="qq_published_at", columns={"published_at"}),
 *          @ORM\Index(name="qq_queue", columns={"queue"}),
 *          @ORM\Index(name="qq_priority", columns={"priority"}),
 *          @ORM\Index(name="qq_delayed_until", columns={"delayed_until"})
 *     }
 * )
 */
class DatabaseQueue
{
    /**
     * @var string
     *
     * @ORM\Column(type="guid")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="bigint")
     */
    protected $published_at;

    /**
     * @var int
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    /**
     * @var int
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $headers;

    /**
     * @var int
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $properties;

    /**
     * @var int
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $redelivered;

    /**
     * @var int
     *
     * @ORM\Column(type="string")
     */
    protected $queue;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    protected $priority;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $delayed_until;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $time_to_live;

}