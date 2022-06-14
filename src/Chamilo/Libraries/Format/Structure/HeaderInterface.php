<?php
namespace Chamilo\Libraries\Format\Structure;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
interface HeaderInterface
{

    public function render(): string;

    public function setApplication(Application $application);

    public function setContainerMode(string $containerMode);

    public function setTextDirection(string $textDirection);

    public function setTitle(string $title);

    public function setViewMode(int $viewMode);
}