<?php
namespace Chamilo\Libraries\Format\Breadcrumb;

use Chamilo\Libraries\Format\Structure\Breadcrumb;

/**
 * @package Chamilo\Libraries\Format\Structure
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class BreadcrumbTrail
{
    /**
     * @var \Chamilo\Libraries\Format\Structure\Breadcrumb[]
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
     * @return \Chamilo\Libraries\Format\Structure\Breadcrumb[]
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    public function getContainerMode(): string
    {
        return $this->containerMode;
    }

    public function getLast(): Breadcrumb
    {
        $breadcrumbtrail = $this->breadcrumbs;
        $last_key = count($breadcrumbtrail) - 1;

        return $breadcrumbtrail[$last_key];
    }

    public function merge(BreadcrumbTrail $trail): void
    {
        $this->breadcrumbs = array_merge($this->breadcrumbs, $trail->getBreadcrumbs());
    }

    public function remove(int $breadcrumbIndex): void
    {
        if ($breadcrumbIndex < 0)
        {
            $breadcrumbIndex = count($this->breadcrumbs) + $breadcrumbIndex;
        }

        unset($this->breadcrumbs[$breadcrumbIndex]);
        $this->breadcrumbs = array_values($this->breadcrumbs);
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\Breadcrumb[] $breadcrumbs
     */
    public function set(array $breadcrumbs): void
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\Breadcrumb[] $breadcrumbs
     */
    public function setBreadcrumbs(array $breadcrumbs): void
    {
        $this->breadcrumbs = $breadcrumbs;
    }

    public function setContainerMode(string $containerMode): void
    {
        $this->containerMode = $containerMode;
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
