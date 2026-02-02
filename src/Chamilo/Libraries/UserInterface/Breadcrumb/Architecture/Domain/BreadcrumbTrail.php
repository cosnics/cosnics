<?php
namespace Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain;

/**
 * @package Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BreadcrumbTrail
{
    /**
     * @var \Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb[]
     */
    protected array $breadcrumbs;

    protected string $containerMode;

    public function __construct(string $containerMode = 'container-fluid')
    {
        $this->breadcrumbs = [];
        $this->containerMode = $containerMode;
    }

    public function add(Breadcrumb $breadcrumb): void
    {
        $this->breadcrumbs[] = $breadcrumb;
    }

    /**
     * @return \Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb[]
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb[] $breadcrumbs
     */
    public function setBreadcrumbs(array $breadcrumbs): void
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function getContainerMode(): string
    {
        return $this->containerMode;
    }

    public function setContainerMode(string $containerMode): void
    {
        $this->containerMode = $containerMode;
    }

    public function getLast(): Breadcrumb
    {
        $breadcrumbtrail = $this->breadcrumbs;
        $lastKey = count($breadcrumbtrail) - 1;

        return $breadcrumbtrail[$lastKey];
    }

    public function merge(BreadcrumbTrail $trail): void
    {
        $this->breadcrumbs = array_merge($this->breadcrumbs, $trail->getBreadcrumbs());
    }

    public function remove(int $breadcrumbIndex): void
    {
        if ($breadcrumbIndex < 0) {
            $breadcrumbIndex = count($this->breadcrumbs) + $breadcrumbIndex;
        }

        unset($this->breadcrumbs[$breadcrumbIndex]);
        $this->breadcrumbs = array_values($this->breadcrumbs);
    }

    /**
     * @param \Chamilo\Libraries\UserInterface\Breadcrumb\Architecture\Domain\Breadcrumb[] $breadcrumbs
     */
    public function set(array $breadcrumbs): void
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function size(): int
    {
        return count($this->breadcrumbs);
    }

    public function truncate(): void
    {
        $this->breadcrumbs = [];
    }
}
