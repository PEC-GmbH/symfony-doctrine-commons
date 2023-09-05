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
namespace Pec\DoctrineCommons\Fixtures\ORM;

use Doctrine\Common\Proxy\Proxy;

class ProxyBlog implements Proxy {

	public function __load() {
	}

	public function __isInitialized() {
	}

	public function __setInitialized($initialized) {
	}

	public function __setInitializer(\Closure $initializer = null) {
	}

	public function __getInitializer() {
	}

	public function __setCloner(\Closure $cloner = null) {
	}

	public function __getCloner() {
	}

	public function __getLazyProperties() {
	}

	public function getId(): ?int {
		return 0;
	}
}
