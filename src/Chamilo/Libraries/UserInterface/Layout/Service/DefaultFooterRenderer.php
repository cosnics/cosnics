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
     * @param string[] $administratorData
     * @param string[] $institutionData
     */
    public function __construct(
        protected BaseFooterRenderer $baseFooterRenderer, protected StringUtilities $stringUtilities,
        protected Translator $translator, protected SessionInterface $session, protected UrlGenerator $urlGenerator,
        protected array $administratorData, protected array $institutionData
    )
    {
    }

    public function render(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->baseFooterRenderer->renderHeader();
        $html[] = $this->getContainerHeader();
        $html[] = implode(' | ', $this->getLinks());
        $html[] = '&nbsp;&copy;&nbsp;' . date('Y');
        $html[] = $this->getContainerFooter();
        $html[] = $this->baseFooterRenderer->renderFooter();

        return implode(PHP_EOL, $html);
    }

    protected function getContainerFooter(): string
    {
        $html = [];

        $html[] = '</p>';
        $html[] = '</footer>';

        return implode(PHP_EOL, $html);
    }

    protected function getContainerHeader(): string
    {
        $html = [];

        $html[] = '<footer class="container py-5">';
        $html[] = '<p class="text-center text-body-secondary">';

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string[]
     */
    protected function getLinks(): array
    {
        $administratorEmail = $this->administratorData['email'];
        $administratorUri = $this->administratorData['uri'];
        $administratorName = $this->administratorData['name'];

        $links = [];

        $links[] =
            '<a href="' . $this->institutionData['uri'] . '" target="about:blank">' . $this->institutionData['name'] .
            '</a>';

        if (!empty($administratorEmail) && !empty($administratorUri)) {
            $email = $this->stringUtilities->encryptMailLink($administratorEmail, $administratorName);
            $links[] = $this->translator->trans(
                'ManagerContactWebsite', ['%Email%' => $email, '%Website%' => $administratorUri],
                StringUtilities::LIBRARIES
            );
        }
        else {
            if (!empty($administratorEmail)) {
                $links[] = $this->translator->trans('Manager', [], StringUtilities::LIBRARIES) . ': ' .
                    $this->stringUtilities->encryptMailLink(
                        $administratorEmail, $administratorName
                    );
            }

            if (!empty($administratorUri)) {
                $links[] = $this->translator->trans('Support', [], StringUtilities::LIBRARIES) . ': <a href="' .
                    $administratorUri . '">' . $administratorName . '</a>';
            }
        }

        return $links;
    }
}
