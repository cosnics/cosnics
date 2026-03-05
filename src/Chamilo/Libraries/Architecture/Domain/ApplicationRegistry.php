<?php
namespace Chamilo\Libraries\Architecture\Domain;

use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OutOfBoundsException;

/**
 * @package Chamilo\Libraries\Architecture\Domain
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ApplicationRegistry extends ArrayCollection
{
    public function addApplication(ApplicationInterface $application): void
    {
        $this->set(get_class($application), $application);
    }

    public function getApplication(string $applicationType): ApplicationInterface
    {
        if (!$this->containsKey($applicationType)) {
            throw new OutOfBoundsException($applicationType . ' is not a valid Application');
        }

        return $this->get($applicationType);
    }
}