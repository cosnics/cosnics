<?php
namespace Chamilo\Core\Admin\Architecture\Domain;

use Chamilo\Core\Admin\Service\Consulter\ConfigurationConsulter;
use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractActionProvider
{
    protected ConfigurationConsulter $configurationConsulter;

    protected Translator $translator;

    protected UrlGenerator $urlGenerator;

    public function __construct(
        UrlGenerator $urlGenerator, Translator $translator
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
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