<?php

namespace Chamilo\Core\Repository\ContentObject\Rubric\Ajax;

use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CategoryNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\ClusterNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\CriteriumNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricData;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\RubricNode;
use Chamilo\Core\Repository\ContentObject\Rubric\Storage\Entity\TreeNode;
use Hogent\Integration\Panopto\Domain\Exception\ValueNotInArrayException;
use JMS\Serializer\Serializer;
use OutOfRangeException;
use function is_subclass_of;

/**
 * @package Chamilo\Core\Repository\ContentObject\Rubric\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class TreeNodeJSONModel
{
    const TYPE_CRITERIUM = 'Criterium';
    const TYPE_CATEGORY = 'Category';
    const TYPE_CLUSTER = 'Cluster';
    const TYPE_RUBRIC = 'Rubric';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $allowedTypes = [self::TYPE_RUBRIC, self::TYPE_CLUSTER, self::TYPE_CATEGORY, self::TYPE_CRITERIUM];

    /**
     * @var string
     */
    protected $color;

    /**
     * @var int
     */
    protected $weight = 100;

    /**
     * TreeNodeJSONModel constructor.
     *
     * @param int $id
     * @param string $title
     * @param int $parentId
     * @param string $type
     * @param string $color
     * @param string $weight
     *
     * @throws \Exception
     */
    public function __construct(
        int $id, string $title, string $type, int $parentId, string $color = null, int $weight = null
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->parentId = $parentId;
        $this->type = $type;
        $this->color = $color;
        $this->weight = $weight;

        $this->validate();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @return int
     */
    public function getWeight(): ?int
    {
        return $this->weight;
    }

    /**
     * @throws \Exception
     */
    public function validate()
    {
        if (!in_array($this->type, $this->allowedTypes))
        {
            throw new ValueNotInArrayException('type', $this->type, $this->allowedTypes);
        }

        if (empty($this->title))
        {
            throw new \InvalidArgumentException('Title can not be empty');
        }

        if (!empty($this->weight) && ($this->weight < 0 || $this->weight > 100))
        {
            throw new OutOfRangeException('Weight must be between 0 and 100');
        }
    }

    /**
     * @param RubricData $rubricData
     *
     * @return CategoryNode|ClusterNode|CriteriumNode
     * @throws \Exception
     */
    public function toTreeNode(RubricData $rubricData): TreeNode
    {
        $this->validate();

        $types = [
            self::TYPE_CRITERIUM => CriteriumNode::class, self::TYPE_RUBRIC => RubricNode::class,
            self::TYPE_CATEGORY => CategoryNode::class, self::TYPE_CLUSTER => ClusterNode::class
        ];

        $class = $types[$this->getType()];
        return $class::fromJSON($this, $rubricData);
    }

    /**
     * @param Serializer $serializer
     *
     * @return mixed|string
     */
    public function toJSON(Serializer $serializer)
    {
        return $serializer->serialize($this, 'json');
    }
}
