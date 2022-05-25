<?php
namespace Chamilo\Libraries\Storage\Iterator;

use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @package Chamilo\Libraries\Storage\Iterator
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 *
 * @psalm-template TKey of array-key
 * @psalm-template T
 * @template-extends ArrayCollection<TKey,T>
 */
class DataClassCollection extends ArrayCollection
{

}
