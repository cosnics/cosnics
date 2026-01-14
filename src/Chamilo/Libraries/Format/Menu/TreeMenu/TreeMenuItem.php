<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;

/**
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TreeMenuItem
{

    private ?string $class = null;

    private bool $collapsed = false;

    private ?string $id = null;

    private ?string $title = null;

    /**
     * @var \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem[]
     */
    private array $treeMenuItems = [];

    private ?string $url = null;

    public function __construct(
        ?string $title = null, ?string $url = null, ?string $id = null, ?string $class = null, bool $collapsed = false
    )
    {
        $this->setTitle($title);
        $this->setUrl($url);
        $this->setId($id);

        if (is_null($class))
        {
            $glyph = new FontAwesomeGlyph('folder', [], null, 'fas');
            $this->setClass($glyph->getClassNamesString());
        }
        else
        {
            $this->setClass($class);
        }

        $this->setTreeMenuItems([]);
        $this->setCollapsed($collapsed);
    }

    public function addChild(TreeMenuItem $treeMenuItem): static
    {
        $this->treeMenuItems[] = $treeMenuItem;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(?string $class = null): static
    {
        $this->class = $class;

        return $this;
    }

    public function getCollapsed(): bool
    {
        return $this->collapsed;
    }

    public function setCollapsed(bool $collapsed): static
    {
        $this->collapsed = $collapsed;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id = null): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title = null): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem[]
     */
    public function getTreeMenuItems(): array
    {
        return $this->treeMenuItems;
    }

    /**
     * @param \Chamilo\Libraries\Format\Menu\TreeMenu\TreeMenuItem[] $treeMenuItems
     */
    public function setTreeMenuItems(array $treeMenuItems): static
    {
        $this->treeMenuItems = $treeMenuItems;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url = null): static
    {
        $this->url = $url;

        return $this;
    }

    public function hasChildren(): bool
    {
        if ($this->getTreeMenuItems())
        {
            return true;
        }

        return false;
    }

    public function removeChild(TreeMenuItem $treeMenuItem): static
    {
        foreach ($this->treeMenuItems as $key => $value)
        {
            if ($value === $treeMenuItem)
            {
                unset($this->treeMenuItems[$key]);
            }
        }

        $this->treeMenuItems = array_values($this->treeMenuItems);

        return $this;
    }

    /**
     * @return string[][]
     */
    public function toArray(): array
    {
        $array = [];
        $array['title'] = $this->getTitle();
        $array['url'] = $this->getUrl();
        $array['id'] = $this->getId();
        $array['class'] = $this->getClass();
        $array['collapsed'] = $this->getCollapsed();

        $children = [];

        if ($this->hasChildren())
        {
            foreach ($this->getTreeMenuItems() as $child)
            {
                $children[] = $child->toArray();
            }

            $array['sub'] = $children;
        }

        return $array;
    }
}
