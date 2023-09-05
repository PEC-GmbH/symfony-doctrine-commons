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

/**
 * Service interface to export datatables into various formats
 */
interface ExporterInterface {

	/**
	 * Exports all tables into the given file
	 *
	 * @param string $filename
	 * @return int Number of exported rows
	 */
	public function exportToFilename(string $filename): int;

	/**
	 * Exports all tables into the given resource handle
	 *
	 * @param resource $resource
	 * @return int Number of exported rows
	 */
	public function export($resource): int;
}
