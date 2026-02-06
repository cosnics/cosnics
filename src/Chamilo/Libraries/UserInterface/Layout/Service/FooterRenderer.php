<?php
namespace Chamilo\Libraries\UserInterface\Layout\Service;

use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Chamilo\Libraries\UserInterface\Layout\Architecture\Domain\PageConfiguration;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class FooterRenderer extends AbstractFooterRenderer
{
    /**
     * @var string[]
     */
    protected array $administratorData;

    /**
     * @var string[]
     */
    protected array $institutionData;

    private SessionInterface $session;

    private StringUtilities $stringUtilities;

    private Translator $translator;

    private UrlGenerator $urlGenerator;

    /**
     * @param string[] $administratorData
     * @param string[] $institutionData
     */
    public function __construct(
        PageConfiguration $pageConfiguration, StringUtilities $stringUtilities, Translator $translator,
        SessionInterface $session, UrlGenerator $urlGenerator, array $administratorData, array $institutionData
    )
    {
        parent::__construct($pageConfiguration);

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

        $html[] = $this->getHeader();

        if ($this->getPageConfiguration()->getViewMode() != PageConfiguration::VIEW_MODE_HEADERLESS) {
            $html[] = $this->getContainerHeader();
            $html[] = implode(' | ', $this->getLinks());
            $html[] = $this->getContainerFooter();
        }

        $html[] = $this->getFooter();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    public function getAdministratorData(): array
    {
        return $this->administratorData;
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
