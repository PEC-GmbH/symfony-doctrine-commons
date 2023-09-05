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
 * @ORM\Entity
 */
class Blog implements BlogInterface {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private ?int $id;

	/**
	 * @ORM\Column(name="title", type="string", length=128)
	 */
	private ?string $title;

	/**
	 * @ORM\ManyToOne(targetEntity="SoftdeletableCategory", inversedBy="blogs")
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
	 */
	private ?SoftdeletableCategory $category;

	public function getId(): ?int {
		return $this->id;
	}

	public function setTitle(?string $title): void {
		$this->title = $title;
	}

	public function getTitle(): ?string {
		return $this->title;
	}

	public function getCategory(): ?SoftdeletableCategory {
		return $this->category;
	}

	public function setCategory(?SoftdeletableCategory $category): self {
		$this->category = $category;
		return $this;
	}
}
