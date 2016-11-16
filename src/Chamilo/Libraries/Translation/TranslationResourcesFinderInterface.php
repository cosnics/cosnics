<?php
namespace Chamilo\Libraries\Translation;

/**
 * An interface to describe a TranslationResourcesFinder to scan the project for translation resources
 * Interface TranslationResourcesFinder
 * 
 * @package Chamilo\Libraries\Translation
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface TranslationResourcesFinderInterface
{

    /**
     * Locates the translation resources and returns them per locale, per resource type and per domain
     * 
     * @example $resource['nl_NL']['ini']['domain'] = '/path/to/resource'
     * @return string[]
     */
    public function findTranslationResources();
}