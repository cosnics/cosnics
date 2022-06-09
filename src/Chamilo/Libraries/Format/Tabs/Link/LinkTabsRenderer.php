<?php
namespace Chamilo\Libraries\Format\Tabs\Link;

/**
 *
 * @package Chamilo\Libraries\Format\Tabs
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class LinkTabsRenderer
{

    private ?string $content;

    private string $name;

    /**
     *
     * @var \Chamilo\Libraries\Format\Tabs\Link\LinkTab[]
     */
    private array $tabs;

    public function __construct(string $name, ?string $content = null)
    {
        $this->name = $name;
        $this->tabs = [];
        $this->content = $content;
    }

    public function render(): string
    {
        $html = [];

        $html[] = $this->header();

        if ($this->getContent())
        {
            $html[] = $this->getContent();
        }

        $html[] = $this->footer();

        return implode(PHP_EOL, $html);
    }

    public function addTab(LinkTab $tab)
    {
        $tab->setIdentifier($this->name . '-' . $tab->getIdentifier());
        $this->tabs[] = $tab;
    }

    public function footer(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content)
    {
        $this->content = $content;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function set_name(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return \Chamilo\Libraries\Format\Tabs\Link\LinkTab[]
     */
    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function header(): string
    {
        $tabs = $this->getTabs();

        $html = [];

        $html[] = '<ul class="nav nav-tabs dynamic-visual-tabs">';

        foreach ($tabs as $tab)
        {
            $html[] = $tab->header();
        }

        $html[] = '</ul>';
        $html[] = '<div class="dynamic-visual-tab-content">';
        $html[] = '<div class="list-group-item">';

        return implode(PHP_EOL, $html);
    }

    public function size(): int
    {
        return count($this->tabs);
    }
}
