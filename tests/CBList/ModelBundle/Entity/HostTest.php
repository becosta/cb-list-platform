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
use Darsyn\IP\IP as InetAddress;

use CBList\ModelBundle\Entity\Category;
use CBList\ModelBundle\Entity\Host;
use CBList\ModelBundle\Entity\Report;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class HostTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Host
     */
    private $host;

    /**
     * @var Host
     */
    private $nullValueHost;

    /**
     * @var InetAddress
     */
    private $localhost;

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
        $this->localhost = new InetAddress('127.0.0.1');

        $this->host = new Host(
                array(
                    'id' => 1, 'reports' => $this->reports,
                    'inetAddress' => $this->localhost,
                )
        );

        $this->nullValueHost = new Host(array('id' => null));
    }

    public function testConstructor()
    {
        $this->assertAttributeEquals(1,                'id',                $this->host);
        $this->assertAttributeEquals($this->reports,   'reports',           $this->host);
        $this->assertAttributeEquals($this->localhost, 'inetAddress',       $this->host);
        $this->assertAttributeEquals(null, 'id', $this->nullValueHost);
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
        $host = new Host(array('reports' => $reports));

        $host->addReport($report);
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
        $host = new Host(array('reports' => $reports));

        $host->addReport($report);
    }

    public function testGetId()
    {
        $this->assertEquals(1, $this->host->getId());
        $this->assertAttributeEquals($this->host->getId(), 'id', $this->host);
        $this->assertNull($this->nullValueHost->getId());
        $this->assertAttributeEquals(
                $this->nullValueHost->getId(), 'id', $this->nullValueHost)
        ;
    }

    public function testGetReports()
    {
        $this->assertAttributeEquals(
                $this->host->getReports(), 'reports', $this->host
        );
        $this->assertEquals($this->reports, $this->host->getReports());
    }

    public function testSetReports()
    {
        $reports = new ArrayCollection();
        $this->assertEquals($this->host, $this->host->setReports($reports));
        $this->assertAttributeEquals($reports, 'reports', $this->host);
    }

    public function testGetInetAddress()
    {
        $this->assertAttributeEquals(
                $this->host->getInetAddress(), 'inetAddress', $this->host
        );
        $this->assertEquals($this->localhost, $this->host->getInetAddress());
    }

    public function testSetInetAddress()
    {
        $inetAddress = new InetAddress('192.168.0.10');
        $this->assertEquals($this->host, $this->host->setInetAddress($inetAddress));
        $this->assertAttributeEquals($inetAddress, 'inetAddress', $this->host);
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
