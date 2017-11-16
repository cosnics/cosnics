<?php

namespace Chamilo\Libraries\Format\DataAction;

use Chamilo\Libraries\File\Redirect;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Format\DataAction
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop - hans.de.bisschop@ehb.be
 */
abstract class DataActionsProvider
{
    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    /**
     * @var array
     */
    protected $baseParameters;

    /**
     * @var string[]
     */
    protected $urlCache;

    /**
     * NodeActionGenerator constructor.
     *
     * @param \Symfony\Component\Translation\Translator $translator
     * @param array $baseParameters
     */
    public function __construct(Translator $translator, array $baseParameters = array())
    {
        $this->translator = $translator;
        $this->baseParameters = $baseParameters;
    }

    /**
     * Generates a URL for the given parameters and filters, includes the base parameters given in this service
     *
     * @param array $parameters
     * @param array $filter
     * @param bool $encode_entities
     *
     * @return string
     */
    protected function getUrl($parameters = array(), $filter = array(), $encode_entities = false)
    {
        $parameters = (count($parameters) ? array_merge($this->baseParameters, $parameters) : $this->baseParameters);

        $redirect = new Redirect($parameters, $filter, $encode_entities);

        return $redirect->getUrl();
    }

    /**
     * @param string|int $identifier
     *
     * @return \Chamilo\Libraries\Format\DataAction\DataActions
     */
    abstract public function getDataActions($identifier);
}
