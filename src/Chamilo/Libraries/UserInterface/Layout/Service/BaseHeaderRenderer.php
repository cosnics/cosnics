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
    protected string $institutionName;

    protected string $siteName;

    protected Translator $translator;

    private PageHeaders $pageConfiguration;

    private ThemePathBuilder $themeWebPathBuilder;

    private WebPathBuilder $webPathBuilder;

    public function __construct(
        PageHeaders $pageConfiguration, WebPathBuilder $webPathBuilder, ThemePathBuilder $themeWebPathBuilder,
        Translator $translator, string $siteName, string $institutionName
    )
    {
        $this->pageConfiguration = $pageConfiguration;
        $this->webPathBuilder = $webPathBuilder;
        $this->themeWebPathBuilder = $themeWebPathBuilder;
        $this->translator = $translator;
        $this->siteName = $siteName;
        $this->institutionName = $institutionName;
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
        $pathBuilder = $this->getWebPathBuilder();
        $themeWebPathBuilder = $this->getThemeWebPathBuilder();
        $pageConfiguration = $this->getPageConfiguration();

        $pageConfiguration->addHtml('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
        $pageConfiguration->addHtml('<meta name="viewport" content="width=device-width, initial-scale=1">');
        $pageConfiguration->addHtml('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');

        $cssPath = $pathBuilder->getCssPath('Chamilo/Libraries');
        $javascriptPath = $pathBuilder->getJavascriptPath('Chamilo/Libraries');

        $pageConfiguration->addCss($cssPath . 'cosnics.vendor.bootstrap.min.css');
        $pageConfiguration->addCss($cssPath . 'cosnics.vendor.jquery.min.css');
        $pageConfiguration->addCss($cssPath . 'cosnics.vendor.min.css');
        $pageConfiguration->addCss($cssPath . 'cosnics.common.' . $themeWebPathBuilder->getTheme() . '.min.css');

        $pageConfiguration->addLink($pathBuilder->getBasePath(), 'top');
        $pageConfiguration->addLink($themeWebPathBuilder->getFavouriteIcon(), 'shortcut icon', null, 'image/x-icon');

        $pageConfiguration->addHtml(
            '<script>var rootWebPath="' . $pathBuilder->getBasePath() . '";</script>'
        );

        $pageConfiguration->addJavascript($javascriptPath . 'cosnics.vendor.jquery.min.js');
        $pageConfiguration->addJavascript($javascriptPath . 'cosnics.vendor.bootstrap.min.js');
        $pageConfiguration->addJavascript($javascriptPath . 'cosnics.vendor.angular.min.js');
        $pageConfiguration->addJavascript($javascriptPath . 'cosnics.vendor.min.js');
        $pageConfiguration->addJavascript($javascriptPath . 'cosnics.common.min.js');

        $pageConfiguration->addHtml('<title>' . $this->getPageTitle() . '</title>');
    }

    public function getInstitutionName(): string
    {
        return $this->institutionName;
    }

    public function getPageConfiguration(): PageHeaders
    {
        return $this->pageConfiguration;
    }

    protected function getPageTitle(): string
    {
        return $this->getInstitutionName() . ' - ' . $this->getSiteName();
    }

    public function getSiteName(): string
    {
        return $this->siteName;
    }

    public function getThemeWebPathBuilder(): ThemePathBuilder
    {
        return $this->themeWebPathBuilder;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getWebPathBuilder(): WebPathBuilder
    {
        return $this->webPathBuilder;
    }

    public function renderHeader(): string
    {
        $this->addDefaultHeaders();
        $pageConfiguration = $this->getPageConfiguration();
        $locale = $this->getTranslator()->getLocale();

        $html = [];

        $html[] = '<!DOCTYPE html>';
        $html[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $locale . '" lang="' . $locale . '">';
        $html[] = '<head>';

        foreach ($pageConfiguration->getHtmlHeaders() as $htmlHeader) {
            $html[] = $htmlHeader;
        }

        $html[] = '</head>';

        $html[] = '<body>';

        return implode(PHP_EOL, $html);
    }
}
