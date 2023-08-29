<?php
namespace Chamilo\Configuration\Package\Action;

use Chamilo\Configuration\Package\Action;
use OutOfBoundsException;

/**
 * @package Chamilo\Configuration\Package\Action
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class PackageActionFactory
{
    /**
     * @var \Chamilo\Configuration\Package\Action[][]
     */
    protected array $packageActions = [];

    public function addPackageAction(string $type, Action $packageAction): void
    {
        $this->packageActions[$type][$packageAction->getContext()] = $packageAction;
    }

    public function addPackageActivator(Activator $packageActivator): void
    {
        $this->addPackageAction(Activator::class, $packageActivator);
    }

    public function addPackageDeactivator(Deactivator $packageDeactivator): void
    {
        $this->addPackageAction(Deactivator::class, $packageDeactivator);
    }

    public function addPackageInstaller(Installer $packageInstaller): void
    {
        $this->addPackageAction(Installer::class, $packageInstaller);
    }

    public function addPackageRemover(Remover $packageRemover): void
    {
        $this->addPackageAction(Remover::class, $packageRemover);
    }

    public function getPackageAction(string $type, string $context): Action
    {
        if (!array_key_exists($type, $this->packageActions))
        {
            throw new OutOfBoundsException($type . ' is not a valid package action type');
        }

        if (!array_key_exists($context, $this->packageActions[$type]))
        {
            throw new OutOfBoundsException($context . ' is not a valid ' . $type);
        }

        return $this->packageActions[$type][$context];
    }

    public function getPackageActivator(string $context): Action
    {
        return $this->getPackageAction(Activator::class, $context);
    }

    /**
     * @return \Chamilo\Configuration\Package\Action[]
     */
    public function getPackageActivators(): array
    {
        return $this->packageActions[Activator::class];
    }

    public function getPackageDeactivator(string $context): Action
    {
        return $this->getPackageAction(Deactivator::class, $context);
    }

    /**
     * @return \Chamilo\Configuration\Package\Action\Deactivator[]
     */
    public function getPackageDeactivators(): array
    {
        return $this->packageActions[Deactivator::class];
    }

    public function getPackageInstaller(string $context): Action
    {
        return $this->getPackageAction(Installer::class, $context);
    }

    /**
     * @return \Chamilo\Configuration\Package\Action[]
     */
    public function getPackageInstallers(): array
    {
        return $this->packageActions[Installer::class];
    }

    public function getPackageRemover(string $context): Action
    {
        return $this->getPackageAction(Remover::class, $context);
    }

    /**
     * @return \Chamilo\Configuration\Package\Action[]
     */
    public function getPackageRemovers(): array
    {
        return $this->packageActions[Remover::class];
    }

}
