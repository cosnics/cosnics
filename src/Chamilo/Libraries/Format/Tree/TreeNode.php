<?php
namespace Chamilo\Libraries\Format\Tree;

/**
 * @package Chamilo\Libraries\Format\Tree
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeNode
{
    /**
     * @var string[]
     */
    public array $anchorAttributes = [];

    /**
     * @var \Chamilo\Libraries\Format\Tree\TreeNode[]
     */
    public array $childNodes = [];

    public bool $hasChildNodes = false;

    public ?string $icon;

    public string $identifier;

    public array $listAttributes;

    public array $state = [
        'opened' => false,
        'disabled' => false,
        'selected' => false,
    ];

    public string $text;

    public function __construct(
        string $identifier, string $text, ?string $icon = null, array $listAttributes = [],
        array $anchorAttributes = [], array $state = [], array $childNodes = [], bool $hasChildNodes = false
    )
    {
        $this->setIdentifier($identifier);
        $this->setText($text);
        $this->setIcon($icon);
        $this->setListAttributes($listAttributes);
        $this->setAnchorAttributes($anchorAttributes);
        $this->setState($state);
        $this->setChildNodes($childNodes);
        $this->setHasChildNodes($hasChildNodes);
    }

    public function addChildNode(TreeNode $child): static
    {
        $this->childNodes[] = $child;
        $this->hasChildNodes = true;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAnchorAttributes(): array
    {
        return $this->anchorAttributes;
    }

    public function setAnchorAttributes(array $attributes): static
    {
        $this->anchorAttributes = $attributes;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\Format\Tree\TreeNode[]
     */
    public function getChildNodes(): array
    {
        return $this->childNodes;
    }

    /**
     * @param \Chamilo\Libraries\Format\Tree\TreeNode[] $childNodes
     */
    public function setChildNodes(array $childNodes): TreeNode
    {
        $this->childNodes = $childNodes;

        if (count($childNodes) > 0)
        {
            $this->hasChildNodes = true;
        }

        return $this;
    }

    public function getHasChildNodes(): bool
    {
        return $this->hasChildNodes;
    }

    public function setHasChildNodes(bool $hasChildNodes): TreeNode
    {
        if (!$hasChildNodes)
        {
            if (count($this->getChildNodes()) > 0)
            {
                $this->hasChildNodes = true;
            }
            else
            {
                $this->hasChildNodes = false;
                $this->childNodes = [];
            }
        }
        else
        {
            $this->hasChildNodes = true;
        }

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): TreeNode
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): TreeNode
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getListAttributes(): array
    {
        return $this->listAttributes;
    }

    public function setListAttributes(array $attributes): static
    {
        $this->listAttributes = $attributes;

        return $this;
    }

    /**
     * @return bool[]
     */
    public function getState(): array
    {
        return $this->state;
    }

    /**
     * @param bool[] $state
     */
    public function setState(array $state): TreeNode
    {
        $this->state = $state;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): TreeNode
    {
        $this->text = $text;

        return $this;
    }

    public function setStateByParameters(
        bool $isOpened = false, bool $isDisabled = false, bool $isSelected = false
    ): static
    {
        $this->state = [
            'opened' => $isOpened,
            'disabled' => $isDisabled,
            'selected' => $isSelected,
        ];

        return $this;
    }
}
