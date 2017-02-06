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

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;
use Darsyn\IP\IP as InetAddress;

use CBList\ModelBundle\Entity\Category;
use CBList\ModelBundle\Entity\Host;
use CBList\ModelBundle\Entity\Report;
use CBList\ModelBundle\Repository\CategoryRepository;
use CBList\ModelBundle\Repository\HostRepository;
use CBList\ModelBundle\Repository\ReportRepository;
use CBList\ModelBundle\Service\CategoryService;
use CBList\ModelBundle\Service\HostService;
use CBList\ModelBundle\Service\ReportService;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class ReportServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $service = $this->createReportService();
        $expectedHost = $this->createHostMock();
        $expectedCategory = $this->createCategoryMock();
        $expectedSenderInetAddress = new InetAddress('127.0.0.1');
        $expectedDateSubmitted = new \DateTime('NOW');

        $expectedReport = new Report(array
                (
                    'host'              => $expectedHost,
                    'category'          => $expectedCategory,
                    'senderInetAddress' => $expectedSenderInetAddress,
                    'dateSubmitted'     => $expectedDateSubmitted,
                )
        );

        $expectedHost->expects($this->once())->method('addReport');
        $expectedCategory->expects($this->once())->method('addReport');

        $actualReport = $service->create(
                $expectedHost, $expectedCategory, $expectedSenderInetAddress
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    public function testCreateNullDate()
    {
        $service = $this->createReportService();
        $expectedHost = $this->createHostMock();
        $expectedCategory = $this->createCategoryMock();
        $expectedSenderInetAddress = new InetAddress('127.0.0.1');
        $expectedDateSubmitted = null;

        $expectedReport = new Report(array
                (
                    'host'              => $expectedHost,
                    'category'          => $expectedCategory,
                    'senderInetAddress' => $expectedSenderInetAddress,
                )
        );

        $expectedHost->expects($this->once())->method('addReport');
        $expectedCategory->expects($this->once())->method('addReport');

        $actualReport = $service->create(
                $expectedHost, $expectedCategory, $expectedSenderInetAddress
        );
        $this->assertNotNull($actualReport);
        $this->assertNotNull($actualReport->getDateSubmitted());
        $this->assertRecentDateTime($actualReport->getDateSubmitted());
    }

    public function testCreateWithCategoryInstance()
    {
        $category = $this->createCategoryMock();
        $host = $this->createHostMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $expectedReport = $this->createReportMock();

        $hostService = $this->createHostServiceMock();
        $hostService->expects($this->once())
                ->method('getHostByInetAddress')
                ->with($this->equalTo($hostInetAddress))
                ->will($this->returnValue($host))
        ;

        $reportService = $this->createReportServiceMock(
                array('create'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $hostService, $this->createCategoryServiceMock()
                )
        );
        $reportService->expects($this->once())
                ->method('create')
                ->with(
                        $this->equalTo($host), $this->equalTo($category),
                        $this->equalTo($senderInetAddress), $this->equalTo(null)
                )
                ->will($this->returnValue($expectedReport))
        ;

        $actualReport = $reportService->createWithCategoryInstance(
                $category, $hostInetAddress, $senderInetAddress
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    public function testCreateWithCategoryInstanceAndDate()
    {
        $category = $this->createCategoryMock();
        $host = $this->createHostMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $expectedReport = $this->createReportMock();
        $date = new \DateTime('NOW');

        $hostService = $this->createHostServiceMock();
        $hostService->expects($this->once())
                ->method('getHostByInetAddress')
                ->with($this->equalTo($hostInetAddress))
                ->will($this->returnValue($host))
        ;

        $reportService = $this->createReportServiceMock(
                array('create'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $hostService, $this->createCategoryServiceMock()
                )
        );
        $reportService->expects($this->once())
                ->method('create')
                ->with(
                        $this->equalTo($host), $this->equalTo($category),
                        $this->equalTo($senderInetAddress), $this->equalTo($date)
                )
                ->will($this->returnValue($expectedReport))
        ;

        $actualReport = $reportService->createWithCategoryInstance(
                $category, $hostInetAddress, $senderInetAddress, $date
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    public function testCreateWithCategoryInstanceAndNonExistingHost()
    {
        $category = $this->createCategoryMock();
        $host = $this->createHostMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $expectedReport = $this->createReportMock();

        $hostService = $this->createHostServiceMock();
        $hostService->expects($this->once())
                ->method('getHostByInetAddress')
                ->with($this->equalTo($hostInetAddress))
                ->will($this->returnValue(null))
        ;
        $hostService->expects($this->once())
                ->method('create')
                ->with($this->equalTo($hostInetAddress))
                ->will($this->returnValue($host))
        ;
        $hostService->expects($this->once())
                ->method('add')
                ->with($this->equalTo($host))
        ;

        $reportService = $this->createReportServiceMock(
                array('create'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $hostService, $this->createCategoryServiceMock()
                )
        );
        $reportService->expects($this->once())
                ->method('create')
                ->with(
                        $this->equalTo($host), $this->equalTo($category),
                        $this->equalTo($senderInetAddress), $this->equalTo(null)
                )
                ->will($this->returnValue($expectedReport))
        ;

        $actualReport = $reportService->createWithCategoryInstance(
                $category, $hostInetAddress, $senderInetAddress
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    public function testCreateWithCategoryInstanceAndDateAndNonExistingHost()
    {
        $category = $this->createCategoryMock();
        $host = $this->createHostMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $expectedReport = $this->createReportMock();
        $date = new \DateTime('NOW');

        $hostService = $this->createHostServiceMock();
        $hostService->expects($this->once())
                ->method('getHostByInetAddress')
                ->with($this->equalTo($hostInetAddress))
                ->will($this->returnValue(null))
        ;
        $hostService->expects($this->once())
                ->method('create')
                ->with($this->equalTo($hostInetAddress))
                ->will($this->returnValue($host))
        ;
        $hostService->expects($this->once())
                ->method('add')
                ->with($this->equalTo($host))
        ;

        $reportService = $this->createReportServiceMock(
                array('create'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $hostService, $this->createCategoryServiceMock()
                )
        );
        $reportService->expects($this->once())
                ->method('create')
                ->with(
                        $this->equalTo($host), $this->equalTo($category),
                        $this->equalTo($senderInetAddress), $this->equalTo($date)
                )
                ->will($this->returnValue($expectedReport))
        ;

        $actualReport = $reportService->createWithCategoryInstance(
                $category, $hostInetAddress, $senderInetAddress, $date
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    public function testCreateWithCategoryId()
    {
        $categoryId = 1;
        $category = $this->createCategoryMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $expectedReport = $this->createReportMock();

        $categoryService = $this->createCategoryServiceMock();
        $categoryService->expects($this->once())
                ->method('getCategory')
                ->with($this->equalTo($categoryId))
                ->will($this->returnValue($category))
        ;

        $reportService = $this->createReportServiceMock(
                array('createWithCategoryInstance'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $this->createHostServiceMock(), $categoryService
                )
        );
        $reportService->expects($this->once())
                ->method('createWithCategoryInstance')
                ->with(
                    $this->equalTo($category), $this->equalTo($hostInetAddress),
                    $this->equalTo($senderInetAddress), $this->equalTo(null)
                )
                ->will($this->returnValue($expectedReport))
        ;

        $actualReport = $reportService->createWithCategoryId(
                $categoryId, $hostInetAddress, $senderInetAddress
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    public function testCreateWithCategoryIdAndDate()
    {
        $categoryId = 1;
        $category = $this->createCategoryMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $date = new \DateTime('NOW');
        $expectedReport = $this->createReportMock();

        $categoryService = $this->createCategoryServiceMock();
        $categoryService->expects($this->once())
                ->method('getCategory')
                ->with($this->equalTo($categoryId))
                ->will($this->returnValue($category))
        ;

        $reportService = $this->createReportServiceMock(
                array('createWithCategoryInstance'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $this->createHostServiceMock(), $categoryService
                )
        );
        $reportService->expects($this->once())
                ->method('createWithCategoryInstance')
                ->with(
                    $this->equalTo($category), $this->equalTo($hostInetAddress),
                    $this->equalTo($senderInetAddress), $this->equalTo($date)
                )
                ->will($this->returnValue($expectedReport))
        ;

        $actualReport = $reportService->createWithCategoryId(
                $categoryId, $hostInetAddress, $senderInetAddress, $date
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    /**
     * @expectedException \CBList\ModelBundle\Exception\EntityNotFoundException
     */
    public function testCreateWithNonExistingCategoryId()
    {
        $categoryId = 1;
        $category = $this->createCategoryMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $expectedReport = $this->createReportMock();

        $categoryService = $this->createCategoryServiceMock();
        $categoryService->expects($this->once())
                ->method('getCategory')
                ->with($this->equalTo($categoryId))
                ->will($this->returnValue(null))
        ;

        $reportService = $this->createReportServiceMock(
                array('createWithCategoryInstance'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $this->createHostServiceMock(), $categoryService
                )
        );
        $reportService->expects($this->never())->method('createWithCategoryInstance');

        $this->assertNull($reportService->createWithCategoryId(
                $categoryId, $hostInetAddress, $senderInetAddress, null
        ));
    }

    /**
     * @expectedException \CBList\ModelBundle\Exception\EntityNotFoundException
     */
    public function testCreateWithNonExistingCategoryIdAndDate()
    {
        $categoryId = 1;
        $category = $this->createCategoryMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $date = new \DateTime('NOW');
        $expectedReport = $this->createReportMock();

        $categoryService = $this->createCategoryServiceMock();
        $categoryService->expects($this->once())
                ->method('getCategory')
                ->with($this->equalTo($categoryId))
                ->will($this->returnValue(null))
        ;

        $reportService = $this->createReportServiceMock(
                array('createWithCategoryInstance'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $this->createHostServiceMock(), $categoryService
                )
        );
        $reportService->expects($this->never())->method('createWithCategoryInstance');

        $this->assertNull($reportService->createWithCategoryId(
                $categoryId, $hostInetAddress, $senderInetAddress, $date
        ));
    }

    public function testCreateWithCategoryLabel()
    {
        $categoryLabel = 'ssh-bruteforce';
        $category = $this->createCategoryMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $expectedReport = $this->createReportMock();

        $categoryService = $this->createCategoryServiceMock();
        $categoryService->expects($this->once())
                ->method('getCategoryByLabel')
                ->with($this->equalTo($categoryLabel))
                ->will($this->returnValue($category))
        ;
        $categoryService->expects($this->never())->method('create');
        $categoryService->expects($this->never())->method('add');

        $reportService = $this->createReportServiceMock(
                array('createWithCategoryInstance'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $this->createHostServiceMock(), $categoryService
                )
        );
        $reportService->expects($this->once())
                ->method('createWithCategoryInstance')
                ->with(
                    $this->equalTo($category), $this->equalTo($hostInetAddress),
                    $this->equalTo($senderInetAddress), $this->equalTo(null)
                )
                ->will($this->returnValue($expectedReport))
        ;

        $actualReport = $reportService->createWithCategoryLabel(
                $categoryLabel, $hostInetAddress, $senderInetAddress, null
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    public function testCreateWithNonExistingCategoryLabel()
    {
        $categoryLabel = 'ssh-bruteforce';
        $category = $this->createCategoryMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $expectedReport = $this->createReportMock();

        $categoryService = $this->createCategoryServiceMock();
        $categoryService->expects($this->once())
                ->method('getCategoryByLabel')
                ->with($this->equalTo($categoryLabel))
                ->will($this->returnValue(null))
        ;
        $categoryService->expects($this->once())
                ->method('create')
                ->with($this->equalTo($categoryLabel))
                ->will($this->returnValue($category))
        ;
        $categoryService->expects($this->once())
                ->method('add')
                ->with($this->equalTo($category))
        ;

        $reportService = $this->createReportServiceMock(
                array('createWithCategoryInstance'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $this->createHostServiceMock(), $categoryService
                )
        );
        $reportService->expects($this->once())
                ->method('createWithCategoryInstance')
                ->with(
                    $this->equalTo($category), $this->equalTo($hostInetAddress),
                    $this->equalTo($senderInetAddress), $this->equalTo(null)
                )
                ->will($this->returnValue($expectedReport))
        ;

        $actualReport = $reportService->createWithCategoryLabel(
                $categoryLabel, $hostInetAddress, $senderInetAddress, null
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    public function testCreateWithCategoryLabelAndDate()
    {
        $categoryLabel = 'ssh-bruteforce';
        $category = $this->createCategoryMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $date = new \DateTime('NOW');
        $expectedReport = $this->createReportMock();

        $categoryService = $this->createCategoryServiceMock();
        $categoryService->expects($this->once())
                ->method('getCategoryByLabel')
                ->with($this->equalTo($categoryLabel))
                ->will($this->returnValue($category))
        ;
        $categoryService->expects($this->never())->method('create');
        $categoryService->expects($this->never())->method('add');

        $reportService = $this->createReportServiceMock(
                array('createWithCategoryInstance'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $this->createHostServiceMock(), $categoryService
                )
        );
        $reportService->expects($this->once())
                ->method('createWithCategoryInstance')
                ->with(
                    $this->equalTo($category), $this->equalTo($hostInetAddress),
                    $this->equalTo($senderInetAddress), $this->equalTo($date)
                )
                ->will($this->returnValue($expectedReport))
        ;

        $actualReport = $reportService->createWithCategoryLabel(
                $categoryLabel, $hostInetAddress, $senderInetAddress, $date
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    public function testCreateWithNonExistingCategoryLabelAndDate()
    {
        $categoryLabel = 'ssh-bruteforce';
        $category = $this->createCategoryMock();
        $hostInetAddress = new InetAddress('10.10.10.1');
        $senderInetAddress = new InetAddress('192.168.0.1');
        $date = new \DateTime('NOW');
        $expectedReport = $this->createReportMock();

        $categoryService = $this->createCategoryServiceMock();
        $categoryService->expects($this->once())
                ->method('getCategoryByLabel')
                ->with($this->equalTo($categoryLabel))
                ->will($this->returnValue(null))
        ;
        $categoryService->expects($this->once())
                ->method('create')
                ->with($this->equalTo($categoryLabel))
                ->will($this->returnValue($category))
        ;
        $categoryService->expects($this->once())
                ->method('add')
                ->with($this->equalTo($category))
        ;

        $reportService = $this->createReportServiceMock(
                array('createWithCategoryInstance'),
                array
                (
                    $this->createEntityManagerMock(), $this->createReportRepositoryMock(),
                    $this->createHostServiceMock(), $categoryService
                )
        );
        $reportService->expects($this->once())
                ->method('createWithCategoryInstance')
                ->with(
                    $this->equalTo($category), $this->equalTo($hostInetAddress),
                    $this->equalTo($senderInetAddress), $this->equalTo($date)
                )
                ->will($this->returnValue($expectedReport))
        ;

        $actualReport = $reportService->createWithCategoryLabel(
                $categoryLabel, $hostInetAddress, $senderInetAddress, $date
        );
        $this->assertNotNull($actualReport);
        $this->assertEquals($expectedReport, $actualReport);
    }

    public function testAdd()
    {
        $report = $this->createReportMock();
        $manager = $this->createEntityManagerMock();
        $repository = $this->createReportRepositoryMock();
        $service = $this->createReportService($manager, $repository);

        $repository->expects($this->once())
                ->method('exists')
                ->with($this->equalTo($report))
                ->will($this->returnValue(false))
        ;
        $manager->expects($this->once())->method('persist')->with($this->equalTo($report));
        $manager->expects($this->once())->method('flush')->with($this->equalTo($report));

        $service->add($report);
    }

    public function testAddWithAlreadyExistingReport()
    {
        $report = $this->createReportMock();
        $manager = $this->createEntityManagerMock();
        $repository = $this->createReportRepositoryMock();
        $service = $this->createReportService($manager, $repository);

        $repository->expects($this->once())
                ->method('exists')
                ->with($this->equalTo($report))
                ->will($this->returnValue(true))
        ;
        $manager->expects($this->never())->method('persist');
        $manager->expects($this->never())->method('flush');

        $service->add($report);
    }

    public function testGetReports()
    {
        $expectedReports = array($this->createReportMock(), $this->createReportMock());
        $repository = $this->createReportRepositoryMock();
        $service = $this->createReportService(null, $repository);

        $repository->expects($this->once())
                ->method('findAll')
                ->will($this->returnValue($expectedReports))
        ;

        $actualReports = $service->getReports();
        $this->assertNotNull($actualReports);
        $this->assertEquals($expectedReports, $actualReports);
    }

    /**
     *
     * @param EntityManager $manager
     * @param HostRepository $repository
     * @return HostService
     */
    private function createReportService(
            EntityManager $manager = null, ReportRepository $repository = null,
            HostService $hostService = null, CategoryService $categoryService = null
    ) {
        if (null === $manager) {
            $manager = $this->createEntityManagerMock();
        }
        if (null === $repository) {
            $repository = $this->createReportRepositoryMock();
        }
        if (null === $hostService) {
            $hostService = $this->createHostServiceMock();
        }
        if (null === $categoryService) {
            $categoryService = $this->createCategoryServiceMock();
        }
        return new ReportService($manager, $repository, $hostService, $categoryService);
    }

    private function createReportServiceMock(
            array $methods = array(), array $constructorArguments = array()
    ) {

        return $this->getMockBuilder(ReportService::class)
                ->setMethods($methods)
                ->setConstructorArgs($constructorArguments)
                ->getMock()
        ;
    }

    /**
     *
     * @return CategoryService
     */
    private function createCategoryServiceMock()
    {
        return $this->getMockBuilder(CategoryService::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    /**
     *
     * @return HostService
     */
    private function createHostServiceMock()
    {
        return $this->getMockBuilder(HostService::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    /**
     * @return ArrayCollection
     */
    public function createArrayCollectionMock()
    {
        return $this->getMockBuilder(ArrayCollection::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    /**
     *
     * @return Host
     */
    private function createCategoryMock()
    {
        return $this->getMockBuilder(Category::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    /**
     *
     * @return Host
     */
    private function createHostMock()
    {
        return $this->getMockBuilder(Host::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    /**
     *
     * @return Host
     */
    private function createReportMock()
    {
        return $this->getMockBuilder(Report::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    /**
     * @return HostRepository
     */
    private function createReportRepositoryMock()
    {
        return $this->getMockBuilder(ReportRepository::class)
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

    /**
     * Asserts that a date is not older than ('NOW' - $seconds)
     *
     * @param \DateTime $date the date to compare
     * @param type $seconds maximum age in seconds
     */
    private function assertRecentDateTime(\DateTime $date, $seconds = 1)
    {
        $now = new \DateTime('NOW');
        $now->sub(new \DateInterval('PT' . $seconds . 'S'));
        $this->assertTrue($date >= $now);
    }
}
