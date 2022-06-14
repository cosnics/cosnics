<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Page
{
    const VIEW_MODE_FULL = 1;
    const VIEW_MODE_HEADERLESS = 2;

    protected static ?Page $instance = null;

    private Application $application;

    private string $containerMode;

    private FooterInterface $footer;

    private HeaderInterface $header;

    private int $viewMode;

    public function __construct(
        int $viewMode = self::VIEW_MODE_FULL, string $containerMode = 'container-fluid', HeaderInterface $header,
        FooterInterface $footer
    )
    {
        $this->viewMode = $viewMode;
        $this->containerMode = $containerMode;
        $this->header = $header;
        $this->footer = $footer;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application)
    {
        $this->application = $application;

        $this->getHeader()->setApplication($this->application);
    }

    public function getContainerMode(): string
    {
        return $this->containerMode;
    }

    public function setContainerMode(string $containerMode)
    {
        $this->containerMode = $containerMode;

        $this->getHeader()->setContainerMode($this->containerMode);
        $this->getFooter()->setContainerMode($this->containerMode);
    }

    public function getFooter(): FooterInterface
    {
        return $this->footer;
    }

    public function setFooter(FooterInterface $footer)
    {
        $this->footer = $footer;
    }

    public function getHeader(): HeaderInterface
    {
        return $this->header;
    }

    public function setHeader(HeaderInterface $header)
    {
        $this->header = $header;
    }
    
    static public function getInstance(): Page
    {
        if (is_null(static::$instance))
        {
            $header = new Header(
                self::VIEW_MODE_FULL, 'container-fluid', Translation::getInstance()->getLanguageIsocode(), 'ltr'
            );
            $footer = new Footer(self::VIEW_MODE_FULL);

            self::$instance = new static(self::VIEW_MODE_FULL, 'container-fluid', $header, $footer);
        }

        return static::$instance;
    }

    public function getViewMode(): int
    {
        return $this->viewMode;
    }

    public function setViewMode(int $viewMode)
    {
        $this->viewMode = $viewMode;

        $this->getHeader()->setViewMode($this->viewMode);
        $this->getFooter()->setViewMode($this->viewMode);
    }

    public function isFullPage(): bool
    {
        return $this->getViewMode() == self::VIEW_MODE_FULL;
    }

    public function isHeaderless(): bool
    {
        return $this->getViewMode() == self::VIEW_MODE_HEADERLESS;
    }

    public function setTitle(string $title)
    {
        $this->getHeader()->setTitle($title);
    }
}