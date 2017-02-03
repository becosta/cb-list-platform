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

namespace tests\CBList\ModelBundle\Repository;

use Doctrine\ORM\EntityManager;

use CBList\Common\CBListKernelTestCase;
use CBList\ModelBundle\DataFixtures\ORM\Tests\LoadCategoryData;
use CBList\ModelBundle\Entity\Category;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class CategoryRepositoryTest extends CBListKernelTestCase
{
    const CATEGORY_REPOSITORY = 'CBListModelBundle:Category';

    const VALID_LABEL = 'ssh-bruteforce';

    const INVALID_LABEL = 'ssh-bruteforc';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->addFixture(new LoadCategoryData());
        $this->executeFixtures();

        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager()
        ;

    }

    public function testFindOneByLabel()
    {
        $validLabel = self::VALID_LABEL;
        $invalidLabel = self::INVALID_LABEL;
        $validDescription = 'ssh password bruteforce reports';

        $repository = $this->em->getRepository(self::CATEGORY_REPOSITORY);

        $category = $repository->findOneByLabel($validLabel);
        $this->assertNotNull($category);
        $this->assertEquals($validLabel, $category->getLabel());
        $this->assertEquals($validDescription, $category->getDescription());

        $category = $repository->findOneByLabel(strtoupper($validLabel));
        $this->assertNotNull($category);
        $this->assertEquals($validLabel, $category->getLabel());
        $this->assertEquals($validDescription, $category->getDescription());

        $category = $repository->findOneByLabel($invalidLabel);
        $this->assertNull($category);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFindOneByLabelNullValue()
    {
        $this->assertNull(
                $this->em->getRepository(self::CATEGORY_REPOSITORY)->findOneByLabel(null)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFindOneByLabelArrayValue()
    {
        $this->assertNull(
                $this->em->getRepository(
                        self::CATEGORY_REPOSITORY)->findOneByLabel(array()
                )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFindOneByLabelEmptyString()
    {
        $this->assertNull(
                $this->em->getRepository(self::CATEGORY_REPOSITORY)->findOneByLabel('')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFindOneByLabelStdClassValue()
    {
        $this->assertNull(
                $this->em->getRepository(
                        self::CATEGORY_REPOSITORY)->findOneByLabel(new \stdClass()
                )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFindOneByLabelNumericValue()
    {
        $this->assertNull(
                $this->em->getRepository(self::CATEGORY_REPOSITORY)->findOneByLabel(1)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFindOneByLabelBooleanValue()
    {
        $this->assertNull(
                $this->em->getRepository(self::CATEGORY_REPOSITORY)->findOneByLabel(false)
        );
    }

    public function testExists()
    {
        $repository = $this->em->getRepository(self::CATEGORY_REPOSITORY);

        $label = 'ssh-bruteforce';
        $category = $repository->findOneByLabel($label);
        if (null === $category) {
            $this->outOfScopeFailure(
                    'Either method CategoryRepository::findOneByLabel is not working' .
                    "properly or the testing database isn't initialized correctly"
            );
        }
        $id = $category->getId();

        $category = new Category(array('id' => $id, 'label' => $label));
        $this->assertTrue($repository->exists($category));

        $category = new Category(array('id' => -$id, 'label' => $label));
        $this->assertTrue($repository->exists($category));

        $category = new Category(array('id' => -$id, 'label' => strtoupper($label)));
        $this->assertTrue($repository->exists($category));

        $category = new Category(array('id' => $id));
        $this->assertTrue($repository->exists($category));

        $category = new Category(array('label' => $label));
        $this->assertTrue($repository->exists($category));

        $category = new Category(array('label' => strtoupper($label)));
        $this->assertTrue($repository->exists($category));

        $category = new Category(array('label' => 'NOVALUE'));
        $this->assertFalse($repository->exists($category));

        $category = new Category(array('id' => -$id));
        $this->assertFalse($repository->exists($category));

        $category = new Category(array('id' => -$id, 'label' => 'NOVALUE'));
        $this->assertFalse($repository->exists($category));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExistsEmptyStringLabel()
    {
        $repository = $this->em->getRepository(self::CATEGORY_REPOSITORY);
        $category = new Category(array('id' => 1, 'label' => ''));
        $this->assertFalse($repository->exists($category));
    }

    public function testExistsLabel()
    {
        $validLabel = self::VALID_LABEL;
        $invalidLabel = self::INVALID_LABEL;

        $repository = $this->em->getRepository(self::CATEGORY_REPOSITORY);

        $this->assertTrue($repository->existsLabel($validLabel));
        $this->assertTrue($repository->existsLabel(strtoupper($validLabel)));
        $this->assertFalse($repository->existsLabel($invalidLabel));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExistsLabelNullValue()
    {
        $this->assertNull(
                $this->em->getRepository(self::CATEGORY_REPOSITORY)->existsLabel(null)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExistsLabelArrayValue()
    {
        $this->assertNull(
                $this->em->getRepository(self::CATEGORY_REPOSITORY)->existsLabel(array())
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExistsLabelStdClass()
    {
        $this->assertNull(
                $this->em->getRepository(
                        self::CATEGORY_REPOSITORY)->existsLabel(new \stdClass()
                )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExistsLabelEmptyString()
    {
        $this->assertNull(
            $this->em->getRepository(self::CATEGORY_REPOSITORY)->existsLabel('')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExistsLabelNumericValue()
    {
        $this->assertNull(
            $this->em->getRepository(self::CATEGORY_REPOSITORY)->existsLabel(1)
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExistsLabelBooleanValue()
    {
        $this->assertNull(
            $this->em->getRepository(self::CATEGORY_REPOSITORY)->existsLabel(false)
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }
}
