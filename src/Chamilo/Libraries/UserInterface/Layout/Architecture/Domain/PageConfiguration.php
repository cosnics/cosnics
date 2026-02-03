<?php
namespace Chamilo\Libraries\UserInterface\Layout\Architecture\Domain;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PageConfiguration
{
    public const VIEW_MODE_FULL = 1;
    public const VIEW_MODE_HEADERLESS = 2;

    /**
     * @var string[]
     */
    protected array $htmlHeaders = [];

    private string $containerMode;

    private string $textDirection;

    private int $viewMode;

    public function __construct(
        int $viewMode = self::VIEW_MODE_FULL, string $containerMode = 'container-fluid', string $textDirection = 'ltr',
        array $htmlHeaders = []
    )
    {
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;
        $this->textDirection = $textDirection;
        $this->htmlHeaders = $htmlHeaders;
    }

    public function addCssFile(string $file, string $media = 'screen'): static
    {
        $header = '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $file . '" />';
        $this->addHtmlHeader($header);

        return $this;
    }

    public function addHtmlHeader(string $htmlHeader): static
    {
        $this->htmlHeaders[] = $htmlHeader;

        return $this;
    }

    public function addJavascriptFile(string $file): static
    {
        $header[] = '<script src="' . $file . '"></script>';
        $this->addHtmlHeader(implode(' ', $header));

        return $this;
    }

    public function addLink(string $url, ?string $rel = null, ?string $title = null, ?string $type = null): static
    {
        $type = $type ? ' type="' . $type . '"' : '';
        $title = $title ? ' title="' . htmlentities($title) . '"' : '';
        $rel = $rel ? ' rel="' . $rel . '"' : '';
        $href = ' href="' . $url . '"';
        $this->addHtmlHeader('<link' . $href . $rel . $title . $type . '/>');

        return $this;
    }

    public function getContainerMode(): string
    {
        return $this->containerMode;
    }

    public function setContainerMode(string $containerMode): static
    {
        $this->containerMode = $containerMode;

        return $this;
    }

    public function getHtmlHeaders(): array
    {
        return $this->htmlHeaders;
    }

    public function getTextDirection(): string
    {
        return $this->textDirection;
    }

    public function setTextDirection(string $textDirection): PageConfiguration
    {
        $this->textDirection = $textDirection;

        return $this;
    }

    public function getViewMode(): int
    {
        return $this->viewMode;
    }

    public function setViewMode(int $viewMode): PageConfiguration
    {
        $this->viewMode = $viewMode;

        return $this;
    }

    public function isFullPage(): bool
    {
        return $this->getViewMode() == self::VIEW_MODE_FULL;
    }

    public function isHeaderless(): bool
    {
        return $this->getViewMode() == self::VIEW_MODE_HEADERLESS;
    }
}