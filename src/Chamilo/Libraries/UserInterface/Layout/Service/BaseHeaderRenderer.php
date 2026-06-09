<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageHeaders;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BaseHeaderRenderer
{
    public function __construct(
        protected PageHeaders $pageConfiguration, protected WebPathBuilder $webPathBuilder,
        protected ThemePathBuilder $themeWebPathBuilder, protected Translator $translator, protected string $siteName,
        protected string $institutionName, protected string $theme
    )
    {
    }

    public function render(): string
    {
        $html = [];

        $html[] = $this->renderHeader();
        $html[] = '<div class="container-headerless">';

        return implode(PHP_EOL, $html);
    }

    protected function addDefaultHeaders(): void
    {
        $this->pageConfiguration->addHtml('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
        $this->pageConfiguration->addHtml('<meta name="viewport" content="width=device-width, initial-scale=1">');
        $this->pageConfiguration->addHtml('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');

        $cssPath = $this->webPathBuilder->getCssPath('Chamilo/Libraries');
        $javascriptPath = $this->webPathBuilder->getJavascriptPath('Chamilo/Libraries');

        $this->pageConfiguration->addCss($cssPath . 'cosnics.vendor.bootstrap.min.css');
        $this->pageConfiguration->addCss($cssPath . 'cosnics.vendor.jquery.min.css');
        $this->pageConfiguration->addCss($cssPath . 'cosnics.vendor.min.css');
        $this->pageConfiguration->addCss(
            $cssPath . 'cosnics.common.' . $this->theme . '.min.css'
        );

        $this->pageConfiguration->addLink($this->webPathBuilder->getBasePath(), 'top');
        $this->pageConfiguration->addLink(
            $this->themeWebPathBuilder->getFavouriteIcon(), 'shortcut icon', null, 'image/x-icon'
        );

        $this->pageConfiguration->addHtml(
            '<script>var rootWebPath="' . $this->webPathBuilder->getBasePath() . '";</script>'
        );

        $this->pageConfiguration->addJavascript($javascriptPath . 'cosnics.vendor.jquery.min.js');
        $this->pageConfiguration->addJavascript($javascriptPath . 'cosnics.vendor.bootstrap.min.js');
        $this->pageConfiguration->addJavascript($javascriptPath . 'cosnics.vendor.angular.min.js');
        $this->pageConfiguration->addJavascript($javascriptPath . 'cosnics.vendor.min.js');
        $this->pageConfiguration->addJavascript($javascriptPath . 'cosnics.common.min.js');

        $this->pageConfiguration->addHtml('<title>' . $this->getPageTitle() . '</title>');
    }

    protected function getPageTitle(): string
    {
        return $this->institutionName . ' - ' . $this->siteName;
    }

    public function renderHeader(): string
    {
        $this->addDefaultHeaders();
        $locale = $this->translator->getLocale();

        $html = [];

        $html[] = '<!DOCTYPE html>';
        $html[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $locale . '" lang="' . $locale .
            '" data-bs-theme="cosnics">';
        $html[] = '<head>';

        foreach ($this->pageConfiguration->getHtmlHeaders() as $htmlHeader) {
            $html[] = $htmlHeader;
        }

        $html[] = '</head>';

        $html[] = '<body>';

        return implode(PHP_EOL, $html);
    }
}
