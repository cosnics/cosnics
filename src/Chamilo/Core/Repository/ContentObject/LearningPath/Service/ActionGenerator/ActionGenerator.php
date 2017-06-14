<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\ActionGenerator;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Platform\Translation;

/**
 * Base class to generate actions
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class ActionGenerator
{
    /**
     * @var Translation
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
     * @param Translation $translator
     * @param array $baseParameters
     */
    public function __construct(Translation $translator, array $baseParameters = array())
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
     * Returns a url for a given set of parameters and a given node. Caches the urls for faster access
     *
     * @param array $parameters
     * @param int $treeNodeId
     *
     * @return string
     */
    protected function getUrlForNode($parameters = array(), $treeNodeId = 0)
    {
        $nodePlaceholder = '__NODE__';

        $cacheKey = md5(serialize($parameters));
        if(!array_key_exists($cacheKey, $this->urlCache))
        {
            $parameters[Manager::PARAM_CHILD_ID] = $nodePlaceholder;
            $this->urlCache[$cacheKey] = $this->getUrl($parameters);
        }

        return str_replace($nodePlaceholder, $treeNodeId, $this->urlCache[$cacheKey]);
    }
}