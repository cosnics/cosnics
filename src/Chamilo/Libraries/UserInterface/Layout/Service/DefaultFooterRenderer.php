<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\UserInterface\Layout\Service
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DefaultFooterRenderer
{
    /**
     * @var string[]
     */
    protected array $administratorData;

    protected BaseFooterRenderer $baseFooterRenderer;

    /**
     * @var string[]
     */
    protected array $institutionData;

    private SessionInterface $session;

    private StringUtilities $stringUtilities;

    private Translator $translator;

    private UrlGenerator $urlGenerator;

    public function __construct(
        BaseFooterRenderer $baseFooterRenderer, StringUtilities $stringUtilities, Translator $translator,
        SessionInterface $session, UrlGenerator $urlGenerator, array $administratorData, array $institutionData
    )
    {
        $this->baseFooterRenderer = $baseFooterRenderer;
        $this->stringUtilities = $stringUtilities;
        $this->translator = $translator;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->administratorData = $administratorData;
        $this->institutionData = $institutionData;
    }

    public function render(): string
    {
        $html = [];

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';
        $html[] = '</div>';

        $html[] = $this->getBaseFooterRenderer()->renderHeader();
        $html[] = $this->getContainerHeader();
        $html[] = implode(' | ', $this->getLinks());
        $html[] = $this->getContainerFooter();
        $html[] = $this->getBaseFooterRenderer()->renderFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function getAdministratorData(): array
    {
        return $this->administratorData;
    }

    public function getBaseFooterRenderer(): BaseFooterRenderer
    {
        return $this->baseFooterRenderer;
    }

    protected function getContainerFooter(): string
    {
        $html = [];

        $html[] = '&nbsp;&copy;&nbsp;' . date('Y');

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div> <!-- end of .container-fluid" -->';
        $html[] = '</footer>';

        return implode(PHP_EOL, $html);
    }

    protected function getContainerHeader(): string
    {
        $html = [];

        $html[] = '<footer class="chamilo-footer">';
        $html[] = '<div class="container-fluid">';
        $html[] = '<div class="row footer">';
        $html[] = '<div class="col-xs-12">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function getInstitutionData(): array
    {
        return $this->institutionData;
    }

    /**
     * @return string[]
     */
    protected function getLinks(): array
    {
        $translator = $this->getTranslator();
        $stringUtilities = $this->getStringUtilities();

        $institutionData = $this->getInstitutionData();
        $administratorData = $this->getAdministratorData();

        $administratorEmail = $administratorData['email'];
        $administratorWebsite = $administratorData['website'];
        $administratorName = $administratorData['name'];

        $links = [];

        $links[] =
            '<a href="' . $institutionData['url'] . '" target="about:blank">' . $institutionData['name'] . '</a>';

        if (!empty($administratorEmail) && !empty($administratorWebsite)) {
            $email = $stringUtilities->encryptMailLink($administratorEmail, $administratorName);
            $links[] = $translator->trans(
                'ManagerContactWebsite', ['EMAIL' => $email, 'WEBSITE' => $administratorWebsite],
                StringUtilities::LIBRARIES
            );
        }
        else {
            if (!empty($administratorEmail)) {
                $links[] = $translator->trans('Manager', [], StringUtilities::LIBRARIES) . ': ' .
                    $stringUtilities->encryptMailLink(
                        $administratorEmail, $administratorName
                    );
            }

            if (!empty($administratorWebsite)) {
                $links[] = $translator->trans('Support', [], StringUtilities::LIBRARIES) . ': <a href="' .
                    $administratorWebsite . '">' . $administratorName . '</a>';
            }
        }

        return $links;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getStringUtilities(): StringUtilities
    {
        return $this->stringUtilities;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function getUrlGenerator(): UrlGenerator
    {
        return $this->urlGenerator;
    }
}
