<?php
declare(strict_types=1);

/*
 * This file is part of the PEC Doctrine Commons package.
 *
 * (c) PEC project engineers & consultants GmbH
 * (c) Oliver Kotte <oliver.kotte@project-engineers.de>
 * (c) Florian Meyer <florian.meyer@project-engineers.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pec\DoctrineCommons\Utils;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Exception;
use Iterator;

/**
 * When using the doctrine iterator method on a query, the root entity is hidden as the first entry of a result set.
 * This iterator will unbox the root entity.
 */
class UnboxingIterator implements Iterator {

	/**
	 *
	 * @var IterableResult
	 */
	protected $iterator;

	/**
	 *
	 * @var integer
	 */
	protected $key = -1;

	/**
	 *
	 * @var mixed
	 */
	protected $current;

	/**
	 *
	 * @var boolean
	 */
	protected $rewinded = false;

	/**
	 * Default constructor
	 *
	 * @param IterableResult $iterator
	 */
	public function __construct(IterableResult $iterator) {
		$this->iterator = $iterator;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see Iterator::next()
	 */
	public function next() {
		// fetch the next one
		$this->current = $this->iterator->next();

		// That's all folks, no more tests!
		if(!$this->current) {
			return $this->onEmpty();
		}

		// stupid iterator array..
		$this->current = $this->current[0];

		// More logical tests!!
		if($this->current) {
			$this->key++;
		} else {
			return $this->onEmpty();
		}
		return $this->current;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see Iterator::current()
	 */
	public function current() {
		return $this->current;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see Iterator::key()
	 */
	public function key() {
		return $this->key;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see Iterator::valid()
	 */
	public function valid(): bool {
		return ($this->current != false);
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @throws Exception
	 * @see Iterator::rewind()
	 */
	public function rewind(): void {
		if($this->rewinded === true) {
			throw new Exception("Can only iterate a Result once.");
		}

		$this->next();
		$this->rewinded = true;
	}

	/**
	 *
	 * @return mixed
	 */
	protected function onEmpty() {
		$this->current = false;
		return $this->current;
	}
}
