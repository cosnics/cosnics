<?php
namespace Chamilo\Libraries\UserInterface\Layout\Architecture\Domain;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class PageHeaders
{
    /**
     * @var string[]
     */
    protected array $htmlHeaders = [];

    public function __construct(array $htmlHeaders = [])
    {
        $this->htmlHeaders = $htmlHeaders;
    }

    public function addCss(string $filePath, string $media = 'screen'): static
    {
        $header = '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $filePath . '" />';
        $this->addHtml($header);

        return $this;
    }

    public function addHtml(string $htmlHeader): static
    {
        $this->htmlHeaders[] = $htmlHeader;

        return $this;
    }

    public function addJavascript(string $filePath): static
    {
        $header[] = '<script src="' . $filePath . '"></script>';
        $this->addHtml(implode(' ', $header));

        return $this;
    }

    public function addLink(string $url, ?string $rel = null, ?string $title = null, ?string $type = null): static
    {
        $type = $type ? ' type="' . $type . '"' : '';
        $title = $title ? ' title="' . htmlentities($title) . '"' : '';
        $rel = $rel ? ' rel="' . $rel . '"' : '';
        $href = ' href="' . $url . '"';
        $this->addHtml('<link' . $href . $rel . $title . $type . '/>');

        return $this;
    }

    public function getHtmlHeaders(): array
    {
        return $this->htmlHeaders;
    }
}