<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Filesystem\Service\WebPathBuilder;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageConfiguration;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Interface\HeaderRendererInterface;
use Chamilo\Libraries\UserInterface\Theme\Service\ThemePathBuilder;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class HeaderRenderer implements HeaderRendererInterface
{
    protected string $institutionName;

    protected string $siteName;

    protected Translator $translator;

    private BannerRenderer $bannerRenderer;

    private PageConfiguration $pageConfiguration;

    private ThemePathBuilder $themeWebPathBuilder;

    private WebPathBuilder $webPathBuilder;

    public function __construct(
        PageConfiguration $pageConfiguration, WebPathBuilder $webPathBuilder, ThemePathBuilder $themeWebPathBuilder,
        BannerRenderer $bannerRenderer, Translator $translator, string $siteName, string $institutionName
    )
    {
        $this->pageConfiguration = $pageConfiguration;
        $this->webPathBuilder = $webPathBuilder;
        $this->themeWebPathBuilder = $themeWebPathBuilder;
        $this->bannerRenderer = $bannerRenderer;
        $this->translator = $translator;
        $this->siteName = $siteName;
        $this->institutionName = $institutionName;
    }

    /**
     * @throws \Exception
     */
    public function render(?User $user = null): string
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

        $html[] = '<body dir="' . $pageConfiguration->getTextDirection() . '">';

        if ($pageConfiguration->getViewMode() != PageConfiguration::VIEW_MODE_HEADERLESS) {
            $html[] = $this->getBannerRenderer()->render($user);
        }

        $classes = $pageConfiguration->getContainerMode();

        if ($pageConfiguration->getViewMode() == PageConfiguration::VIEW_MODE_HEADERLESS) {
            $classes .= ' container-headerless';
        }

        $html[] = '<div class="' . $classes . '">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @throws \Exception
     */
    protected function addDefaultHeaders(): void
    {
        $pathBuilder = $this->getWebPathBuilder();
        $themeWebPathBuilder = $this->getThemeWebPathBuilder();
        $pageConfiguration = $this->getPageConfiguration();

        $pageConfiguration->addHtmlHeader('<meta http-equiv="X-UA-Compatible" content="IE=edge">');
        $pageConfiguration->addHtmlHeader('<meta name="viewport" content="width=device-width, initial-scale=1">');
        $pageConfiguration->addHtmlHeader('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');

        $cssPath = $pathBuilder->getCssPath('Chamilo/Libraries');
        $javascriptPath = $pathBuilder->getJavascriptPath('Chamilo/Libraries');

        $pageConfiguration->addCssFile($cssPath . 'cosnics.vendor.bootstrap.min.css');
        $pageConfiguration->addCssFile($cssPath . 'cosnics.vendor.jquery.min.css');
        $pageConfiguration->addCssFile($cssPath . 'cosnics.vendor.min.css');
        $pageConfiguration->addCssFile($cssPath . 'cosnics.common.' . $themeWebPathBuilder->getTheme() . '.min.css');

        $pageConfiguration->addLink($pathBuilder->getBasePath(), 'top');
        $pageConfiguration->addLink($themeWebPathBuilder->getFavouriteIcon(), 'shortcut icon', null, 'image/x-icon');

        $pageConfiguration->addHtmlHeader(
            '<script>var rootWebPath="' . $pathBuilder->getBasePath() . '";</script>'
        );

        $pageConfiguration->addJavascriptFile($javascriptPath . 'cosnics.vendor.jquery.min.js');
        $pageConfiguration->addJavascriptFile($javascriptPath . 'cosnics.vendor.bootstrap.min.js');
        $pageConfiguration->addJavascriptFile($javascriptPath . 'cosnics.vendor.angular.min.js');
        $pageConfiguration->addJavascriptFile($javascriptPath . 'cosnics.vendor.min.js');
        $pageConfiguration->addJavascriptFile($javascriptPath . 'cosnics.common.min.js');

        $pageConfiguration->addHtmlHeader('<title>' . $this->getPageTitle() . '</title>');
    }

    public function getBannerRenderer(): BannerRenderer
    {
        return $this->bannerRenderer;
    }

    public function getInstitutionName(): string
    {
        return $this->institutionName;
    }

    public function getPageConfiguration(): PageConfiguration
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
}
