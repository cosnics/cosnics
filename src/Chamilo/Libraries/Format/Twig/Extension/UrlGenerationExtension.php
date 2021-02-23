<?php
namespace Chamilo\Libraries\Format\Twig\Extension;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This class is an extension of twig to support url generation
 *
 * @package Chamilo\Libraries\Format\Twig\Extension
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UrlGenerationExtension extends AbstractExtension
{

    /**
     * The Chamilo Url Generator
     *
     * @var \Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator
     */
    private $urlGenerator;

    /**
     * Constructor
     *
     * @param \Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator $urlGenerator
     */
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->setUrlGenerator($urlGenerator);
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator $urlGenerator
     *
     * @throws \InvalidArgumentException
     */
    public function setUrlGenerator(UrlGenerator $urlGenerator)
    {
        if (! $urlGenerator instanceof UrlGenerator)
        {
            throw new \InvalidArgumentException(
                'The given url generator is not an instance of UrlGenerator, instead "' . get_class($urlGenerator) .
                     '" was given.');
        }
        $this->urlGenerator = $urlGenerator;
    }

    /**
     *
     * @see Twig_Extension::getFunctions()
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('url', array($this->urlGenerator, 'generateURL')),
            new TwigFunction('context_url', array($this->urlGenerator, 'generateContextURL')));
    }

    /**
     *
     * @see Twig_Extension::getName()
     */
    public function getName()
    {
        return 'url_generation';
    }
}
