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

namespace tests\CBList\ModelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use CBList\ModelBundle\Entity\Category;
use CBList\ModelBundle\Entity\Report;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Category
     */
    private $category;

    /**
     * @var Category
     */
    private $nullValueCategory;

    /**
     * @var ArrayCollection
     */
    private $reports;

    /**
     * @{inheritDoc}
     */
    protected function setUp()
    {
        $this->reports = new ArrayCollection();
        $this->category = new Category(
                array(
                    'id' => 1, 'label' => 'label',
                    'description' => 'description', 'reports' => $this->reports
                )
        );
        $this->nullValueCategory = new Category(
                array(
                    'id' => null, 'label' => 'null',
                    'description' => null, 'reports' => $this->reports
                )
        );
    }

    public function testConstructor()
    {
        $this->assertAttributeEquals(1,              'id',          $this->category);
        $this->assertAttributeEquals('label',        'label',       $this->category);
        $this->assertAttributeEquals('description',  'description', $this->category);
        $this->assertAttributeEquals($this->reports, 'reports',     $this->category);

        $this->assertAttributeEquals(null,   'id',          $this->nullValueCategory);
        $this->assertAttributeEquals('null', 'label',       $this->nullValueCategory);
        $this->assertAttributeEquals(null,   'description', $this->nullValueCategory);
        $this->assertAttributeEquals($this->reports, 'reports', $this->nullValueCategory);
    }

    public function testAddReport()
    {
        $report = $this->createReportMock();
        $reports = $this->createArrayCollectionMock();
        $reports->expects($this->once())
                ->method('contains')
                ->with($this->equalTo($report))
                ->will($this->returnValue(false))
        ;
        $reports->expects($this->once())
                ->method('add')
                ->with($this->equalTo($report))
                ->will($this->returnValue(true))
        ;
        $category = new Category(array('reports' => $reports));

        $category->addReport($report);
    }

    public function testAddReportAlreadyExists()
    {
        $report = $this->createReportMock();
        $reports = $this->createArrayCollectionMock();
        $reports->expects($this->once())
                ->method('contains')
                ->with($this->equalTo($report))
                ->will($this->returnValue(true))
        ;
        $reports->expects($this->never())->method('add');
        $category = new Category(array('reports' => $reports));

        $category->addReport($report);
    }

    public function testGetId()
    {
        $this->assertEquals(1, $this->category->getId());
        $this->assertNull($this->nullValueCategory->getId());
    }

    public function testGetLabel()
    {
        $this->assertEquals('label', $this->category->getLabel());
        $this->assertEquals('null', $this->nullValueCategory->getLabel());
    }

    public function testSetLabel()
    {
        $newLabel = 'LABEL';
        $this->assertEquals($this->category, $this->category->setLabel($newLabel));
        $this->assertEquals($newLabel, $this->category->getLabel());
        $this->assertEquals(
                $this->nullValueCategory, $this->nullValueCategory->setLabel($newLabel)
        );
        $this->assertEquals($newLabel, $this->nullValueCategory->getLabel());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetLabelEmptyString()
    {
        $newLabel = '';
        $this->assertNull($this->category->setLabel($newLabel));
        $this->assertEquals('label', $this->category->getLabel());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetLabelNullValue()
    {
        $newLabel = null;
        $this->assertNull($this->category->setLabel($newLabel));
        $this->assertEquals('label', $this->category->getLabel());
    }

    public function testGetDescription()
    {
        $this->assertEquals('description', $this->category->getDescription());
        $this->assertNull($this->nullValueCategory->getDescription());
    }

    public function testSetDescription()
    {
        $newDescription = 'DESCRIPTION';
        $this->assertEquals(
                $this->category, $this->category->setDescription($newDescription)
        );
        $this->assertEquals($newDescription, $this->category->getDescription());
        $this->assertEquals(
                $this->nullValueCategory,
                $this->nullValueCategory->setDescription($newDescription)
        );
        $this->assertEquals($newDescription, $this->nullValueCategory->getDescription());

        $newDescription = '';
        $this->assertEquals(
                $this->category, $this->category->setDescription($newDescription)
        );
        $this->assertEquals($newDescription, $this->category->getDescription());
        $this->assertEquals(
                $this->nullValueCategory,
                $this->nullValueCategory->setDescription($newDescription)
        );
        $this->assertEquals($newDescription, $this->nullValueCategory->getDescription());

        $newDescription = null;
        $this->assertEquals(
                $this->category, $this->category->setDescription($newDescription)
        );
        $this->assertEquals($newDescription, $this->category->getDescription());
        $this->assertEquals(
                $this->nullValueCategory,
                $this->nullValueCategory->setDescription($newDescription)
        );
        $this->assertEquals($newDescription, $this->nullValueCategory->getDescription());
    }

    public function testGetReports()
    {
        $this->assertEquals($this->reports, $this->category->getReports());
        $this->assertEquals($this->reports, $this->nullValueCategory->getReports());
    }

    public function testSetReports()
    {
        $newReports = new ArrayCollection(array(new Category(), new Category()));
        $this->assertEquals($this->category, $this->category->setReports($newReports));
        $this->assertEquals($newReports, $this->category->getReports());
        $this->assertEquals(
                $this->nullValueCategory, $this->nullValueCategory->setReports($newReports)
        );
        $this->assertEquals($newReports, $this->nullValueCategory->getReports());
    }

    /**
     *
     * @return ArrayCollection
     */
    private function createArrayCollectionMock()
    {
        return $this->getMockBuilder(ArrayCollection::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    /**
     *
     * @return Report
     */
    private function createReportMock()
    {
        return $this->getMockBuilder(Report::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }
}
