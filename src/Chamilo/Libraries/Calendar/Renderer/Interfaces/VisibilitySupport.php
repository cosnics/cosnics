<?php
namespace Chamilo\Libraries\Calendar\Renderer\Interfaces;

/**
 * An interface which forces the implementing Application to provide a given set of methods
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface VisibilitySupport
{

    public function getVisibilityContext(): string;

    /**
     * @return string[]
     */
    public function getVisibilityData(): array;
    
    public function isSourceVisible(string $source, ?int $userIdentifier = null): bool;
}