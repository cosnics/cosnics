<?php

namespace Chamilo\Libraries\Format\Twig\Extension;

use Chamilo\Libraries\Architecture\Application\Routing\UrlGenerator;

/**
 * This class is an extension of twig to support url generation
 *
 * @package common\libraries
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UrlGenerationExtension extends \Twig_Extension
{
    /**
     * The Chamilo Url Generator
     *
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * Constructor
     *
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->setUrlGenerator($urlGenerator);
    }

    /**
     * @param UrlGenerator $urlGenerator
     *
     * @throws \InvalidArgumentException
     */
    public function setUrlGenerator(UrlGenerator $urlGenerator)
    {
        if(!$urlGenerator instanceof UrlGenerator)
        {
            throw new \InvalidArgumentException(
                'The given url generator is not an instance of UrlGenerator, instead "' .
                get_class($urlGenerator) . '" was given.'
            );
        }
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('url', array($this->urlGenerator, 'generateURL')),
            new \Twig_SimpleFunction('context_url', array($this->urlGenerator, 'generateContextURL'))
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'url_generation';
    }
}