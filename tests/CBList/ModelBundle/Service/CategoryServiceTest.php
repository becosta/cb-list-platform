<?php

/*
 * The MIT License
 *
 * Copyright (c) 2017 Benjamin Costa <benjamin.costa.75@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace tests\CBList\ModelBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

use CBList\Common\CBListKernelTestCase;
use CBList\ModelBundle\Entity\Category;
use CBList\ModelBundle\Repository\CategoryRepository;
use CBList\ModelBundle\Service\CategoryService;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class CategoryServiceTest extends \PHPUnit_Framework_TestCase
{

    public function testExistsTrue()
    {
        $category = $this->createCategoryMock();
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);

        $repository->expects($this->once())
                ->method('exists')
                ->with($this->equalTo($category))
                ->will($this->returnValue(true))
        ;

        $this->assertTrue($service->exists($category));
    }

    public function testExistsFalse()
    {
        $category = $this->createCategoryMock();
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);

        $repository->expects($this->once())
                ->method('exists')
                ->with($this->equalTo($category))
                ->will($this->returnValue(false))
        ;

        $this->assertFalse($service->exists($category));
    }

    public function testCreate()
    {
        $service = $this->createCategoryService();

        $expectedLabel = 'label';
        $expectedDescription = '';
        $category = $service->create($expectedLabel);
        $this->assertNotNull($category);
        $this->assertEquals($expectedLabel, $category->getLabel());
        $this->assertEquals($expectedDescription, $category->getDescription());

        $expectedLabel = 'label';
        $expectedDescription = '';
        $category = $service->create($expectedLabel, $expectedDescription);
        $this->assertNotNull($category);
        $this->assertEquals($expectedLabel, $category->getLabel());
        $this->assertEquals($expectedDescription, $category->getDescription());

        $expectedLabel = 'label';
        $expectedDescription = 'description';
        $category = $service->create($expectedLabel, $expectedDescription);
        $this->assertNotNull($category);
        $this->assertEquals($expectedLabel, $category->getLabel());
        $this->assertEquals($expectedDescription, $category->getDescription());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateEmptyStringLabel()
    {
        $this->assertEquals(null, $this->createCategoryService()->create(''));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateNullValueLabel()
    {
        $this->assertEquals(null, $this->createCategoryService()->create(null));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateStdClassLabel()
    {
        $this->assertEquals(null, $this->createCategoryService()->create(new \stdClass()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateNumericValueLabel()
    {
        $this->assertEquals(null, $this->createCategoryService()->create(1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateBooleanValueLabel()
    {
        $this->assertEquals(null, $this->createCategoryService()->create(false));
    }

    public function testAdd()
    {
        $manager = $this->createEntityManagerMock();
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService($manager, $repository);

        $repository->expects($this->once())
                ->method('existsLabel')
                ->will($this->returnValue(false))
        ;
        $manager->expects($this->once())->method('persist');
        $manager->expects($this->once())->method('flush');

        $service->add(new Category(array('label' => 'label')));

    }

    public function testAddExistingCategory()
    {
        $manager = $this->createEntityManagerMock();
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService($manager, $repository);

        $repository->expects($this->once())
                ->method('existsLabel')
                ->will($this->returnValue(true))
        ;
        $manager->expects($this->never())->method('persist');
        $manager->expects($this->never())->method('flush');

        $service->add(new Category(array('label' => 'label')));
    }

    public function testUpdate()
    {
        $description = 'description';
        $oldLabel = 'label';
        $newLabel = 'LABEL';

        $manager = $this->createEntityManagerMock();
        $service = $this->createCategoryService($manager);

        $category = $this->createCategoryMock();
        $category->expects($this->never())->method('getId');
        $category->expects($this->never())->method('setId');
        $category->expects($this->never())->method('getReports');
        $category->expects($this->never())->method('setReports');
        $category->expects($this->never())->method('setDescription');
        $category->expects($this->once())
                ->method('getLabel')
                ->will($this->returnValue($oldLabel))
        ;
        $category->expects($this->once())
                ->method('setLabel')
                ->with($this->equalTo($newLabel))
                ->will($this->returnValue($category))
        ;
        $category->expects($this->once())
                ->method('getDescription')
                ->will($this->returnValue($description))
        ;

        $manager->expects($this->once())->method('persist');
        $manager->expects($this->once())->method('flush');

        $updatedCategory = $service->update($category, array(
            'label' => $newLabel,
            'description' => $description,
        ));
        $this->assertEquals($category, $updatedCategory);
    }

    /**
     * @expectedException \CBList\ModelBundle\Exception\EntityNotModifiedException
     */
    public function testUpdateIdenticValues()
    {
        $label = 'label';
        $description = 'description';

        $manager = $this->createEntityManagerMock();
        $service = $this->createCategoryService($manager);

        $category = $this->createCategoryMock();
        $category->expects($this->once())
                ->method('getLabel')
                ->will($this->returnValue($label))
        ;
        $category->expects($this->once())
                ->method('getDescription')
                ->will($this->returnValue($description))
        ;
        $category->expects($this->never())->method('getId');
        $category->expects($this->never())->method('setId');
        $category->expects($this->never())->method('setLabel');
        $category->expects($this->never())->method('setDescription');
        $category->expects($this->never())->method('getReports');
        $category->expects($this->never())->method('setReports');
        $manager->expects($this->never())->method('persist');
        $manager->expects($this->never())->method('flush');

        $this->assertNull($service->update($category, array(
            'label' => $label, 'description' => $description,
        )));
    }

    /**
     * @expectedException \CBList\ModelBundle\Exception\EntityNotModifiedException
     */
    public function testUpdateNoValue()
    {
        $manager = $this->createEntityManagerMock();
        $service = $this->createCategoryService($manager);

        $category = $this->createCategoryMock();
        $category->expects($this->never())->method('getId');
        $category->expects($this->never())->method('setId');
        $category->expects($this->never())->method('getLabel');
        $category->expects($this->never())->method('setLabel');
        $category->expects($this->never())->method('getDescription');
        $category->expects($this->never())->method('setDescription');
        $category->expects($this->never())->method('getReports');
        $category->expects($this->never())->method('setReports');
        $manager->expects($this->never())->method('persist');
        $manager->expects($this->never())->method('flush');

        $this->assertNull($service->update($category, array()));
    }

    public function testUpdateById()
    {
        $id = 1; $labelField = $oldLabel = 'label'; $newLabel = 'LABEL';
        $oldCategory = new Category(array('id' => $id, $labelField => $oldLabel));
        $newCategory = new Category(array('id' => $id, $labelField => $newLabel));
        $values = array($labelField => $newLabel);

        $service = $this->getMockBuilder(CategoryService::class)
                ->setMethods(array('getCategory', 'update'))
                ->disableOriginalConstructor()
                ->getMock();

        $service->expects($this->once())
                ->method('getCategory')
                ->with($this->equalTo($id))
                ->will($this->returnValue($oldCategory))
        ;
        $service->expects($this->once())
                ->method('update')
                ->with($this->equalTo($oldCategory), $this->equalTo($values))
                ->will($this->returnValue($newCategory))
        ;

        $returnedCategory = $service->updateById($id, $values);
        $this->assertEquals($newCategory, $returnedCategory);
    }

    /**
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testUpdateByIdNonExistingCategory()
    {
        $id = 1;

        $service = $this->getMockBuilder(CategoryService::class)
                ->setMethods(array('getCategory', 'update'))
                ->disableOriginalConstructor()
                ->getMock();

        $service->expects($this->once())
                ->method('getCategory')
                ->with($this->equalTo($id))
                ->will($this->returnValue(null))
        ;
        $service->expects($this->never())->method('update');

        $this->assertNull($service->updateById($id, array('label' => 'label')));
    }

    public function testReplace()
    {
        $id = 1; $label = 'label'; $description = 'description';
        $oldCategory = $this->createCategoryMock();
        $newCategory = $this->createCategoryMock();

        $manager = $this->createEntityManagerMock();
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService($manager, $repository);

        $repository->expects($this->once())
                ->method('exists')
                ->with($this->equalTo($oldCategory))
                ->will($this->returnValue(true))
        ;
        $newCategory->expects($this->exactly(2))
                ->method('getId')
                ->will($this->returnValue($id))
        ;
        $newCategory->expects($this->once())
                ->method('getLabel')
                ->will($this->returnValue($label))
        ;
        $newCategory->expects($this->once())
                ->method('getDescription')
                ->will($this->returnValue($description))
        ;
        $oldCategory->expects($this->once())
                ->method('getId')
                ->will($this->returnValue($id))
        ;
        $oldCategory->expects($this->once())
                ->method('setLabel')
                ->with($this->equalTo($label))
                ->will($this->returnSelf())
        ;
        $oldCategory->expects($this->once())
                ->method('setDescription')
                ->with($this->equalTo($description))
                ->will($this->returnSelf())
        ;
        $manager->expects($this->once())
                ->method('persist')
                ->with($this->equalTo($oldCategory))
        ;
        $manager->expects($this->once())
                ->method('flush')
                ->with($this->equalTo($oldCategory))
        ;

        $returnedCategory = $service->replace($oldCategory, $newCategory);
        $this->assertEquals($oldCategory, $returnedCategory);
    }

    public function testReplaceNullId()
    {
        $id = 1; $label = 'label'; $description = 'description';
        $oldCategory = $this->createCategoryMock();
        $newCategory = $this->createCategoryMock();

        $manager = $this->createEntityManagerMock();
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService($manager, $repository);

        $repository->expects($this->once())
                ->method('exists')
                ->with($this->equalTo($oldCategory))
                ->will($this->returnValue(true))
        ;
        $newCategory->expects($this->once())
                ->method('getId')
                ->will($this->returnValue(null))
        ;
        $newCategory->expects($this->once())
                ->method('getLabel')
                ->will($this->returnValue($label))
        ;
        $newCategory->expects($this->once())
                ->method('getDescription')
                ->will($this->returnValue($description))
        ;
        $oldCategory->expects($this->never())->method('getId');
        $oldCategory->expects($this->once())
                ->method('setLabel')
                ->with($this->equalTo($label))
                ->will($this->returnSelf())
        ;
        $oldCategory->expects($this->once())
                ->method('setDescription')
                ->with($this->equalTo($description))
                ->will($this->returnSelf())
        ;
        $manager->expects($this->once())
                ->method('persist')
                ->with($this->equalTo($oldCategory))
        ;
        $manager->expects($this->once())
                ->method('flush')
                ->with($this->equalTo($oldCategory))
        ;

        $returnedCategory = $service->replace($oldCategory, $newCategory);
        $this->assertEquals($oldCategory, $returnedCategory);
    }

    /**
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testReplaceNonExistingCategory()
    {
        $oldCategory = $this->createCategoryMock();
        $newCategory = $this->createCategoryMock();
        $manager = $this->createEntityManagerMock();
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService($manager, $repository);

        $repository->expects($this->once())
                ->method('exists')
                ->with($this->equalTo($oldCategory))
                ->will($this->returnValue(false))
        ;
        $manager->expects($this->never())->method('persist');
        $manager->expects($this->never())->method('flush');

        $this->assertNull($service->replace($oldCategory, $newCategory));
    }

    /**
     * @expectedException \CBList\ModelBundle\Exception\EntityMismatchException
     */
    public function testReplaceDifferentCategory()
    {
        $oldCategory = $this->createCategoryMock();
        $newCategory = $this->createCategoryMock();
        $manager = $this->createEntityManagerMock();
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService($manager, $repository);

        $repository->expects($this->once())
                ->method('exists')
                ->with($this->equalTo($oldCategory))
                ->will($this->returnValue(true))
        ;
        $oldCategory->expects($this->once())
                ->method('getId')
                ->will($this->returnValue(1))
        ;
        $newCategory->expects($this->exactly(2))
                ->method('getId')
                ->will($this->returnValue(2))
        ;
        $oldCategory->expects($this->never())->method('setLabel');
        $oldCategory->expects($this->never())->method('setDescription');
        $manager->expects($this->never())->method('persist');
        $manager->expects($this->never())->method('flush');

        $this->assertNull($service->replace($oldCategory, $newCategory));
    }

    public function testReplaceById()
    {
        $id = 1;
        $oldCategory = $this->createCategoryMock();
        $newCategory = $this->createCategoryMock();

        $service = $this->getMockBuilder(CategoryService::class)
                ->setMethods(array('getCategory', 'replace'))
                ->disableOriginalConstructor()
                ->getMock();

        $service->expects($this->once())
                ->method('getCategory')
                ->with($this->equalTo($id))
                ->will($this->returnValue($oldCategory))
        ;
        $service->expects($this->once())
                ->method('replace')
                ->with($this->equalTo($oldCategory), $this->equalTo($newCategory))
                ->will($this->returnValue($oldCategory));

        $returnedCategory = $service->replaceById($id, $newCategory);
        $this->assertEquals($oldCategory, $returnedCategory);
    }

    /**
     * @expectedException \Doctrine\ORM\EntityNotFoundException
     */
    public function testReplaceByIdNonExistingCategory()
    {
        $id = 1; $newCategory = $this->createCategoryMock();
        $service = $this->getMockBuilder(CategoryService::class)
                ->setMethods(array('getCategory', 'replace'))
                ->disableOriginalConstructor()
                ->getMock();

        $service->expects($this->once())
                ->method('getCategory')
                ->with($this->equalTo($id))
                ->will($this->returnValue(null))
        ;
        $service->expects($this->never())->method('replace');

        $this->assertNull($service->replaceById($id, $newCategory));
    }

    public function testGetCategories()
    {
        $instances = array(
            $this->createCategoryMock(),
            $this->createCategoryMock(),
            $this->createCategoryMock()
        );
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);

        $repository->expects($this->once())
                ->method('findAll')
                ->will($this->returnValue($instances))
        ;

        $this->assertEquals($instances, $service->getCategories());
    }

    public function testGetCategory()
    {
        $id = 1;
        $category = $this->createCategoryMock();
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->once())
                ->method('find')
                ->with($this->equalTo($id))
                ->will($this->returnValue($category));
        $this->assertEquals($category, $service->getCategory($id));
    }

    public function testGetCategoryNotFound()
    {
        $id = 1;
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->once())
                ->method('find')
                ->with($this->equalTo($id))
                ->will($this->returnValue(null));
        $this->assertNull($service->getCategory($id));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryNullValue()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('find');
        $this->assertNull($service->getCategory(null));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryArrayValue()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('find');
        $this->assertNull($service->getCategory(array()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryStdClass()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('find');
        $this->assertNull($service->getCategory(new \stdClass()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryStringValue()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('find');
        $this->assertNull($service->getCategory('1'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryBooleanValue()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('find');
        $this->assertNull($service->getCategory(false));
    }

    public function testGetCategoryByLabel()
    {
        $label = 'label';
        $category = $this->createCategoryMock();
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->once())
                ->method('findOneBy')
                ->with($this->equalTo(array(CategoryService::LABEL_FIELD => $label)))
                ->will($this->returnValue($category))
        ;
        $this->assertEquals($category, $service->getCategoryByLabel($label));
    }

    public function testGetCategoryByLabelNotFound()
    {
        $label = 'label';
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->once())
                ->method('findOneBy')
                ->with($this->equalTo(array(CategoryService::LABEL_FIELD => $label)))
                ->will($this->returnValue(null))
        ;
        $this->assertNull($service->getCategoryByLabel($label));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryByLabelInvalidLabel()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('findOneBy');
        $this->assertNull($service->getCategoryByLabel(''));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryByLabelNullValue()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('findOneBy');
        $this->assertNull($service->getCategoryByLabel(null));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryByLabelArrayValue()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('findOneBy');
        $this->assertNull($service->getCategoryByLabel(array()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryByLabelStdClass()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('findOneBy');
        $this->assertNull($service->getCategoryByLabel(new \stdClass()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryByLabelNumericValue()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('findOneBy');
        $this->assertNull($service->getCategoryByLabel(1));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetCategoryByLabelBooleanValue()
    {
        $repository = $this->createCategoryRepositoryMock();
        $service = $this->createCategoryService(null, $repository);
        $repository->expects($this->never())->method('findOneBy');
        $this->assertNull($service->getCategoryByLabel(false));
    }

    private function createCategoryService(
            EntityManager $manager = null, CategoryRepository $repository = null
    ) {
        if (null === $manager) {
            $manager = $this->createEntityManagerMock();
        }
        if (null === $repository) {
            $repository = $this->createCategoryRepositoryMock();
        }
        return new CategoryService($manager, $repository);
    }

    /**
     *
     * @return Category
     */
    private function createCategoryMock()
    {
        return $this->getMockBuilder(Category::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    /**
     * @return CategoryRepository
     */
    private function createCategoryRepositoryMock()
    {
        return $this->getMockBuilder(CategoryRepository::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    /**
     * @return EntityManager
     */
    private function createEntityManagerMock()
    {
        return $this->getMockBuilder(EntityManager::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }
}