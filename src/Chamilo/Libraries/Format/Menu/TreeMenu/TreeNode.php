<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

/**
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeNode
{
    /**
     * @var string[]
     */
    public array $anchorAttributes = [];

    /**
     * @var \Chamilo\Libraries\Format\Menu\TreeMenu\TreeNode[]
     */
    public array $childNodes = [];

    public bool $hasChildNodes;

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
        string $identifier, string $text, ?string $icon = null
    )
    {
        $this->identifier = $identifier;
        $this->text = $text;
        $this->icon = $icon;
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
     * @return \Chamilo\Libraries\Format\Menu\TreeMenu\TreeNode[]
     */
    public function getChildNodes(): array
    {
        return $this->childNodes;
    }

    /**
     * @param \Chamilo\Libraries\Format\Menu\TreeMenu\TreeNode[] $childNodes
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
        $this->hasChildNodes = $hasChildNodes;

        if (!$hasChildNodes)
        {
            $this->childNodes = [];
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

    public function setState(
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

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): TreeNode
    {
        $this->text = $text;

        return $this;
    }
}
