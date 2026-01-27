<?php
namespace Chamilo\Libraries\UserInterface\Layout\Architecture\Domain;

use Chamilo\Libraries\Architecture\Domain\Application;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PageConfiguration
{
    public const VIEW_MODE_FULL = 1;
    public const VIEW_MODE_HEADERLESS = 2;

    private ?Application $application;

    private string $containerMode;

    private array $htmlHeaders;

    private string $languageCode;

    private string $textDirection;

    private ?string $title;

    private int $viewMode;

    public function __construct(
        int $viewMode = self::VIEW_MODE_FULL, string $containerMode = 'container-fluid', string $textDirection = 'ltr',
        string $languageCode = 'en', ?string $title = null, array $htmlHeaders = [], ?Application $application = null
    )
    {
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;
        $this->textDirection = $textDirection;
        $this->languageCode = $languageCode;
        $this->title = $title;
        $this->htmlHeaders = $htmlHeaders;
        $this->application = $application;
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

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): static
    {
        $this->application = $application;

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

    public function setHtmlHeaders(array $htmlHeaders): PageConfiguration
    {
        $this->htmlHeaders = $htmlHeaders;

        return $this;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(string $languageCode): PageConfiguration
    {
        $this->languageCode = $languageCode;

        return $this;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): PageConfiguration
    {
        $this->title = $title;

        return $this;
    }

    public function getViewMode(): int
    {
        return $this->viewMode;
    }

    public function setViewMode(int $viewMode)
    {
        $this->viewMode = $viewMode;
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