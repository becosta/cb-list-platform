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

use CBList\ModelBundle\Entity\Host;
use CBList\ModelBundle\Repository\HostRepository;
use CBList\ModelBundle\Service\HostService;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class HostServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $service = $this->createHostService();
        $expectedAddress = new InetAddress('127.0.0.1');

        $host = $service->create($expectedAddress);
        $this->assertNotNull($host);
        $this->assertEquals($expectedAddress, $host->getInetAddress());
        $this->assertEquals(new ArrayCollection(), $host->getReports());
    }

    public function testAdd()
    {
        $host = new Host(array('inetAddress' => new InetAddress('127.0.0.1')));

        $manager = $this->createEntityManagerMock();
        $repository = $this->createHostRepositoryMock();
        $service = $this->createHostService($manager, $repository);

        $repository->expects($this->once())
                ->method('exists')
                ->with($this->equalTo($host))
                ->will($this->returnValue(false))
        ;
        $manager->expects($this->once())->method('persist');
        $manager->expects($this->once())->method('flush');

        $service->add($host);
    }

    /**
     * TODO: assert that an exception is thrown
     */
    public function testAddExistingHost()
    {
        $host = new Host(array('inetAddress' => new InetAddress('127.0.0.1')));

        $manager = $this->createEntityManagerMock();
        $repository = $this->createHostRepositoryMock();
        $service = $this->createHostService($manager, $repository);

        $repository->expects($this->once())
                ->method('exists')
                ->with($this->equalTo($host))
                ->will($this->returnValue(true))
        ;
        $manager->expects($this->never())->method('persist');
        $manager->expects($this->never())->method('flush');

        $service->add($host);
    }

    public function testGetHosts()
    {
        $instances = array(
            $this->createHostMock(),
            $this->createHostMock(),
            $this->createHostMock()
        );
        $repository = $this->createHostRepositoryMock();
        $service = $this->createHostService(null, $repository);

        $repository->expects($this->once())
                ->method('findAll')
                ->will($this->returnValue($instances))
        ;

        $this->assertEquals($instances, $service->getHosts());
    }

    public function testGetHost()
    {
        $id = 1;
        $host = $this->createHostMock();
        $repository = $this->createHostRepositoryMock();
        $service = $this->createHostService(null, $repository);
        $repository->expects($this->once())
                ->method('find')
                ->with($this->equalTo($id))
                ->will($this->returnValue($host))
        ;
        $this->assertEquals($host, $service->getHost($id));
    }

    public function testGetHostNotFound()
    {
        $id = 1;
        $host = $this->createHostMock();
        $repository = $this->createHostRepositoryMock();
        $service = $this->createHostService(null, $repository);
        $repository->expects($this->once())
                ->method('find')
                ->with($this->equalTo($id))
                ->will($this->returnValue(null))
        ;
        $this->assertNull($service->getHost($id));
    }

    public function testGetHostByInetAddress()
    {
        $inetAddress = new InetAddress('127.0.0.1');
        $host = $this->createHostMock();
        $repository = $this->createHostRepositoryMock();
        $service = $this->createHostService(null, $repository);
        $repository->expects($this->once())
                ->method('findOneByInetAddress')
                ->with($this->equalTo($inetAddress))
                ->will($this->returnValue($host))
        ;
        $this->assertEquals($host, $service->getHostByInetAddress($inetAddress));
    }

    public function testGetHostByInetAddressNotFound()
    {
        $inetAddress = new InetAddress('127.0.0.1');
        $host = $this->createHostMock();
        $repository = $this->createHostRepositoryMock();
        $service = $this->createHostService(null, $repository);
        $repository->expects($this->once())
                ->method('findOneByInetAddress')
                ->with($this->equalTo($inetAddress))
                ->will($this->returnValue(null))
        ;
        $this->assertNull($service->getHostByInetAddress($inetAddress));
    }

    /**
     *
     * @param EntityManager $manager
     * @param HostRepository $repository
     * @return HostService
     */
    private function createHostService(
            EntityManager $manager = null, HostRepository $repository = null
    ) {
        if (null === $manager) {
            $manager = $this->createEntityManagerMock();
        }
        if (null === $repository) {
            $repository = $this->createHostRepositoryMock();
        }
        return new HostService($manager, $repository);
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
     * @return HostRepository
     */
    private function createHostRepositoryMock()
    {
        return $this->getMockBuilder(HostRepository::class)
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
