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

namespace Pec\DoctrineCommons\Utils;

use Pec\DoctrineCommons\AbstractORMGedmoTestCase;
use Pec\DoctrineCommons\Fixtures\ORM\AbstractBlog;
use Pec\DoctrineCommons\Fixtures\ORM\Blog;
use Pec\DoctrineCommons\Fixtures\ORM\BlogInterface;
use Pec\DoctrineCommons\Fixtures\ORM\BlogWithNiceConstructor;
use Pec\DoctrineCommons\Fixtures\ORM\IconifiedBlog;
use Pec\DoctrineCommons\Fixtures\ORM\ProxyBlog;
use Pec\DoctrineCommons\Fixtures\ORM\SoftdeletableCategory;
use Pec\TestBundle\PecTestBundle;
use Symfony\Component\HttpKernel\KernelInterface;

class DoctrineFunctionsTest extends AbstractORMGedmoTestCase {

	/**
	 *
	 * @return DoctrineFunctionsInterface
	 */
	protected function getDoctrineService($kernel = null) {
		$service = new DoctrineFunctions($this->getMockDoctrineRegistry(), $this->getTranslatorMock(), $kernel);
		return $service;
	}

	protected function mockKernel($bundles = array()) {
		$kernel = $this->getMockBuilder(KernelInterface::class)->setMethods(array(
			'getBundles'
		))->getMockForAbstractClass();
		$kernel->method('getBundles')->willReturn($bundles);
		return $kernel;
	}

	/**
	 *
	 * {@inheritdoc}
	 *
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp(): void {
		parent::setUp();
		$this->getMockSqliteEntityManager();
	}

	public function testUnproxify() {
		$category = new SoftdeletableCategory();
		$category->setTitle('NotDeleted');
		$this->em->persist($category);

		$blog = new Blog();
		$blog->setTitle('blog1');
		$blog->setCategory($category);
		$this->em->persist($blog);
		$this->em->flush();

		$blog2 = new Blog();
		$blog2->setTitle('blog2');
		$blog2->setCategory($category);
		$this->em->persist($blog2);
		$this->em->flush();
		$this->em->clear();

		$blog = $this->em->getRepository(Blog::class)->findOneBy(array(
			'title' => 'blog1'
		));
		$category = $blog->getCategory();

		$this->assertNotNull($category);
		$this->assertInstanceOf('\Doctrine\ORM\Proxy\Proxy', $category);

		$unproxyCategory = $this->getDoctrineService()->unproxifyFilter($category);
		$this->assertNotNull($unproxyCategory);
		$this->assertInstanceOf(SoftdeletableCategory::class, $unproxyCategory);

		$this->assertNotNull($category);
		$this->assertInstanceOf('\Doctrine\ORM\Proxy\Proxy', $category);

		$blog2 = $this->em->getRepository(Blog::class)->findOneBy(array(
			'title' => 'blog2'
		));
		$category = $blog2->getCategory();
		$this->assertNotNull($category);

		$this->assertNull($this->getDoctrineService()->unproxifyFilter(new ProxyBlog()));
	}

	protected function getUsedEntityFixtures() {
		return array(
			Blog::class,
			IconifiedBlog::class,
			SoftdeletableCategory::class,
			AbstractBlog::class
		);
	}

	public function testGetEntityIcon() {
		$blog = new Blog();

		$blogIcon = $this->getDoctrineService()->getEntityIcon($blog);
		$this->assertNull($blogIcon);
		$blogIcon = $this->getDoctrineService()->getEntityIcon(get_class($blog));
		$this->assertNull($blogIcon);
		$blogIcon = $this->getDoctrineService()->getEntityIcon($blog, "purpose");
		$this->assertNull($blogIcon);
		$blogIcon = $this->getDoctrineService()->getEntityIcon(get_class($blog), "purpose");
		$this->assertNull($blogIcon);

		$iconBlog = new IconifiedBlog();
		$iconBlogIcon = $this->getDoctrineService()->getEntityIcon($iconBlog);
		$this->assertNotNull($iconBlogIcon);
		$this->assertEquals($iconBlogIcon, 'icon');
		$iconBlogIcon = $this->getDoctrineService()->getEntityIcon(get_class($iconBlog));
		$this->assertNotNull($iconBlogIcon);
		$this->assertEquals($iconBlogIcon, 'icon');
		$iconBlogIcon = $this->getDoctrineService()->getEntityIcon($iconBlog, "purpose");
		$this->assertNotNull($iconBlogIcon);
		$this->assertEquals($iconBlogIcon, 'purpose');
		$iconBlogIcon = $this->getDoctrineService()->getEntityIcon(get_class($iconBlog), "purpose");
		$this->assertNotNull($iconBlogIcon);
		$this->assertEquals($iconBlogIcon, 'purpose');
	}

	/**
	 */
	public function testGetBundleNameWOKernel(): void {
		$this->expectException(\InvalidArgumentException::class);
		$this->getDoctrineService()->getBundleName('Test');
	}

	public function testGetBundleNameUnkownEntity(): void {
		$name = $this->getDoctrineService($this->mockKernel())->getBundleName('Test');
		$this->assertNull($name);
	}

	public function testGetBundleName() {
		$bundles = array(
			'PecTestBundle' => PecTestBundle::class
		);
		$name = $this->getDoctrineService($this->mockKernel($bundles))->getBundleName('Pec\\TestBundle\\Entity\\Test');
		$this->assertEquals('PecTestBundle', $name);
	}

	public function testGetBundleNameForObject() {
		$bundles = array(
			'PecTestBundle' => PecTestBundle::class
		);
		$name = $this->getDoctrineService($this->mockKernel($bundles))->getBundleName(new Blog());
		$this->assertNull($name);
	}

	public function testGetEntitiesByParent() {
		$this->assertEmpty($this->getDoctrineService()->getEntitiesByParent(SoftdeletableCategory::class));
		$this->assertContains(IconifiedBlog::class, $this->getDoctrineService()->getEntitiesByParent(Blog::class));
	}

	public function testGetEntitiesByInterface() {
		$result = $this->getDoctrineService()->getEntitiesByInterface(BlogInterface::class);
		$this->assertContains(IconifiedBlog::class, $result);
		$this->assertContains(Blog::class, $result);
	}

	public function testGetEntitiesByParentPrefixed() {
		$bundles = array(
			'PecTestBundle' => PecTestBundle::class
		);
		$this->assertEmpty($this->getDoctrineService($this->mockKernel($bundles))->getEntitiesByParent(SoftdeletableCategory::class, true));
		$result = $this->getDoctrineService($this->mockKernel($bundles))->getEntitiesByParent(Blog::class, true);
		$this->assertNotEmpty($result);
		$this->assertArrayHasKey('', $result);
		$result = $result[''];
		$this->assertCount(1, $result);
		$this->assertArrayHasKey(IconifiedBlog::class, $result);
		$this->assertEquals('Blog with Icon', $result[IconifiedBlog::class]);
	}

	public function testGetEntitiesByInterfacePrefixed() {
		$bundles = array(
			'PecTestBundle' => PecTestBundle::class
		);
		$result = $this->getDoctrineService($this->mockKernel($bundles))->getEntitiesByInterface(BlogInterface::class, true);
		$this->assertNotEmpty($result);
		$this->assertArrayHasKey('', $result);
		$result = $result[''];

		$this->assertCount(2, $result);
		$this->assertArrayHasKey(IconifiedBlog::class, $result);
		$this->assertEquals('Blog with Icon', $result[IconifiedBlog::class]);
		$this->assertArrayHasKey(Blog::class, $result);
		$this->assertEquals('Blog', $result[Blog::class]);
	}

	public function testGetHumanReadableEntityName() {
		$name = $this->getDoctrineService()->getHumanReadableEntityName(Blog::class);
		$this->assertEquals('Blog', $name);

		$name = $this->getDoctrineService()->getHumanReadableEntityName(IconifiedBlog::class);
		$this->assertEquals('Blog with Icon', $name);

		$name = $this->getDoctrineService()->getHumanReadableEntityName(SoftdeletableCategory::class);
		$this->assertEquals('DeleteableCategory', $name);

		$name = $this->getDoctrineService()->getHumanReadableEntityName(BlogInterface::class);
		$this->assertEquals('BlogInterface', $name);

		$name = $this->getDoctrineService()->getHumanReadableEntityName(23);
		$this->assertNull($name);

		$name = $this->getDoctrineService()->getHumanReadableEntityName(BlogWithNiceConstructor::class);
		$this->assertEquals('BlogWithNiceConstructor', $name);
	}
}
