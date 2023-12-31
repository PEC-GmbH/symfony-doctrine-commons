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

namespace Pec\DoctrineCommons\Services;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * This service adds the bundle name as a prefix to every table name to avoid name collision between different bundles
 */
class TablePrefixService implements EventSubscriber {

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see \Doctrine\Common\EventSubscriber::getSubscribedEvents()
	 */
	public function getSubscribedEvents(): array {
		return array(
			'loadClassMetadata'
		);
	}

	/**
	 * Event listeners
	 *
	 * @param LoadClassMetadataEventArgs $args
	 */
	public function loadClassMetadata(LoadClassMetadataEventArgs $args): void {
		/**
		 * @var ClassMetadataInfo $classMetadata
		 */
		$classMetadata = $args->getClassMetadata();

		// Do not re-apply the prefix in an inheritance hierarchy.
		if($classMetadata->isInheritanceTypeSingleTable() && !$classMetadata->isRootEntity()) {
			return;
		}

		$prefix = $classMetadata->namespace;
		if(!preg_match('/([^\\\\]+)Bundle/i', $classMetadata->namespace, $prefix)) {
			return;
		}

		$prefix = strtolower($prefix[1]) . '_';

		$classMetadata->setPrimaryTable(array(
			'name' => $prefix . $classMetadata->getTableName()
		));

		if($args->getEntityManager()->getConnection()->getDatabasePlatform() instanceof SqlitePlatform && array_key_exists('indexes', $classMetadata->table)) {
			$indexes = array();
			foreach($classMetadata->table['indexes'] as $name => $params) {
				$indexes[$prefix . $classMetadata->getTableName() . '_' . $name] = $params;
			}
			$classMetadata->table['indexes'] = $indexes;
		}

		foreach($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
			if($mapping['type'] === ClassMetadataInfo::MANY_TO_MANY && isset($classMetadata->associationMappings[$fieldName]['joinTable']['name'])) {
				if(isset($classMetadata->associationMappings[$fieldName]['joinTable']['prefixed']) && $classMetadata->associationMappings[$fieldName]['joinTable']['prefixed']) {
					continue;
				}

				$mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
				$classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $prefix . $mappedTableName;
				$classMetadata->associationMappings[$fieldName]['joinTable']['prefixed'] = true;
			}
		}
	}
}
