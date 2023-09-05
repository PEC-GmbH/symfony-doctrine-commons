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

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class AbstractBlog {
}
