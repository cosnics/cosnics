<?php
namespace Chamilo\Libraries\Format\Menu\TreeMenu;

/**
 * @package Chamilo\Libraries\Format\Menu\TreeMenu
 */
abstract class TreeMenuDataProvider
{

    private string $selectedTreeMenuItem;

    private string $url;

    public function __construct(string $url, string $selectedTreeMenuItem)
    {
        $this->setUrl($url);
        $this->setSelectedTreeMenuItem($selectedTreeMenuItem);
    }

    public function formatUrl(string $id): string
    {
        return $this->getUrl() . '&' . $this->getIdParameterName() . '=' . $id;
    }

    abstract public function getIdParameterName(): string;

    public function getSelectedTreeMenuItem(): string
    {
        return $this->selectedTreeMenuItem;
    }

    public function setSelectedTreeMenuItem(string $selectedTreeMenuItem): static
    {
        $this->selectedTreeMenuItem = $selectedTreeMenuItem;

        return $this;
    }

    public function getSelectedTreeMenuItemUrl(): string
    {
        return $this->formatUrl($this->getSelectedTreeMenuItem());
    }

    abstract public function getTreeMenuData(): TreeMenuItem;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }
}
