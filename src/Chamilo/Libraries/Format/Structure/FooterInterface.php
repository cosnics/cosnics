<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface FooterInterface
{

    /**
     *
     * @return string
     */
    public function render();

    /**
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application);

    /**
     *
     * @param string $containerMode
     */
    public function setContainerMode($containerMode);

    /**
     *
     * @param string $viewMode
     */
    public function setViewMode($viewMode);
}