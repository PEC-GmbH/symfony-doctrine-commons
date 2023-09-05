<?php

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
namespace Pec\DoctrineCommons;

use Doctrine\ORM\Configuration;
use Gedmo\SoftDeleteable\SoftDeleteableListener;

abstract class AbstractORMGedmoTestCase extends AbstractORMTestCase {

	const SOFT_DELETEABLE_FILTER_NAME = 'soft-deleteable';

	/**
	 *
	 * @var SoftDeleteableListener
	 */
	protected $softDeleteableListener;

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Pec\DoctrineCommons\AbstractORMTestCase::getEventManager()
	 */
	protected function getEventManager() {
		$evm = parent::getEventManager();
		$this->softDeleteableListener = new SoftDeleteableListener();
		$evm->addEventSubscriber($this->softDeleteableListener);
		return $evm;
	}

	/**
	 * Get annotation mapping configuration
	 *
	 * @return \Doctrine\ORM\Configuration
	 */
	protected function getMockAnnotatedConfig() {
		$config = parent::getMockAnnotatedConfig();
		$config->addFilter(self::SOFT_DELETEABLE_FILTER_NAME, 'Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter');
		return $config;
	}
}