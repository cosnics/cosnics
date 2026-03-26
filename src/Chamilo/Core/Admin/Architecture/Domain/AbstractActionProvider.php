<?php
namespace Chamilo\Core\Admin\Architecture\Domain;

use Chamilo\Libraries\Service\Routing\UrlGenerator;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Admin\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class AbstractActionProvider
{
    public function __construct(
        protected readonly UrlGenerator $urlGenerator, protected readonly Translator $translator
    )
    {
    }
}