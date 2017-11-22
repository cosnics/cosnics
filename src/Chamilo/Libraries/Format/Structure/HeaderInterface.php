<?php
namespace Chamilo\Libraries\Format\Structure;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface HeaderInterface
{

    /**
     *
     * @param string $viewMode
     */
    public function setViewMode($viewMode);

    /**
     *
     * @param string $containerMode
     */
    public function setContainerMode($containerMode);

    /**
     * \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application);

    /**
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     *
     * @param string $textDirection
     */
    public function setTextDirection($textDirection);

    /**
     *
     * @return string
     */
    public function render();
}