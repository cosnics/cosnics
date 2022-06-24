<?php
namespace Chamilo\Libraries\Format\Tabs;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

/**
 * @package Chamilo\Libraries\Format\Tabs
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-implements Collection<TKey,T>
 * @template-implements Selectable<TKey,T>
 * @psalm-consistent-constructor
 */
class Actions extends ArrayCollection
{

    private string $context;

    private ?string $searchUrl;

    public function __construct(string $context, array $actions = [], ?string $searchUrl = null)
    {
        parent::__construct($actions);

        $this->context = $context;
        $this->searchUrl = $searchUrl;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function setContext(string $context)
    {
        $this->context = $context;
    }

    public function getSearchUrl(): ?string
    {
        return $this->searchUrl;
    }

    public function setSearchUrl(?string $search)
    {
        $this->searchUrl = $search;
    }
}
