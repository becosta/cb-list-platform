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

use Darsyn\IP\IP as InetAddress;

use CBList\ModelBundle\Entity\Category;
use CBList\ModelBundle\Entity\Host;
use CBList\ModelBundle\Entity\Report;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class ReportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Report
     */
    private $report;

    /**
     * @var Report
     */
    private $nullValueReport;

    /**
     * @var \DateTime
     */
    private $dateNow;

    /**
     * @var InetAddress
     */
    private $localhost;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var Host
     */
    private $host;

    /**
     * @{inheritDoc}
     */
    protected function setUp()
    {
        $this->dateNow = new \DateTime('NOW');
        $this->localhost = new InetAddress('127.0.0.1');
        $this->category = $this->createCategoryMock();
        $this->host = $this->createHostMock();

        $this->report = new Report(
                array(
                    'id' => 1, 'dateSubmitted' => $this->dateNow,
                    'senderInetAddress' => $this->localhost,
                    'category' => $this->category, 'host' => $this->host,
                )
        );

        $this->nullValueReport = new Report(
                array(
                    'id' => null, 'dateSubmitted' => $this->dateNow,
                    'senderInetAddress' => $this->localhost,
                    'category' => $this->category, 'host' => $this->host,
                )
        );
    }

    public function testConstructor()
    {
        $this->assertAttributeEquals(1,                'id',                $this->report);
        $this->assertAttributeEquals($this->dateNow,   'dateSubmitted',     $this->report);
        $this->assertAttributeEquals($this->localhost, 'senderInetAddress', $this->report);
        $this->assertAttributeEquals($this->category,  'category',          $this->report);
        $this->assertAttributeEquals($this->host,      'host',              $this->report);

        $this->assertAttributeEquals(null, 'id', $this->nullValueReport);
    }

    public function testGetId()
    {
        $this->assertEquals(1, $this->report->getId());
        $this->assertAttributeEquals($this->report->getId(), 'id', $this->report);
        $this->assertNull($this->nullValueReport->getId());
        $this->assertAttributeEquals(
                $this->nullValueReport->getId(), 'id', $this->nullValueReport)
        ;
    }

    public function testGetDateSubmitted()
    {
        $this->assertAttributeEquals(
                $this->report->getDateSubmitted(), 'dateSubmitted', $this->report
        );
        $this->assertEquals($this->dateNow, $this->report->getDateSubmitted());
    }

    public function testSetDateSubmitted()
    {
        $now = new \DateTime('NOW');
        $this->assertEquals($this->report, $this->report->setDateSubmitted($now));
        $this->assertAttributeEquals($now, 'dateSubmitted', $this->report);
    }

    public function testGetSenderInetAddress()
    {
        $this->assertEquals($this->localhost, $this->report->getSenderInetAddress());
        $this->assertAttributeEquals(
                $this->report->getSenderInetAddress(), 'senderInetAddress', $this->report
        );
    }

    public function testSetSenderInetAddress()
    {
        $ip = new InetAddress('192.168.0.1');
        $this->assertEquals($this->report, $this->report->setSenderInetAddress($ip));
        $this->assertAttributeEquals($ip, 'senderInetAddress', $this->report);
    }

    public function testGetCategory()
    {
        $this->assertEquals($this->category, $this->report->getCategory());
        $this->assertAttributeEquals(
                $this->report->getCategory(), 'category', $this->report
        );
    }

    public function testSetCategory()
    {
        $category = $this->createCategoryMock();
        $category->expects($this->once())
                ->method('addReport')
                ->with($this->equalTo($this->report))
        ;
        $this->assertEquals($this->report, $this->report->setCategory($category));
        $this->assertAttributeEquals($category, 'category', $this->report);
    }

    public function testGetHost()
    {
        $this->assertEquals($this->host, $this->report->getHost());
        $this->assertAttributeEquals($this->report->getHost(), 'host', $this->report);
    }

    public function testSetHost()
    {
        $host = $this->createHostMock();
        $host->expects($this->once())
                ->method('addReport')
                ->with($this->equalTo($this->report))
        ;
        $this->assertEquals($this->report, $this->report->setHost($host));
        $this->assertAttributeEquals($host, 'host', $this->report);
    }

    /**
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
     * @return Host
     */
    private function createHostMock()
    {
        return $this->getMockBuilder(Host::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }
}
