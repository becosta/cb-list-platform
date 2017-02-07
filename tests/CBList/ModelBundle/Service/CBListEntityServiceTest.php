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

use CBList\ModelBundle\Entity\Entity;
use CBList\ModelBundle\Repository\CBListRepository;
use CBList\ModelBundle\Service\CBListEntityService;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class NoopService extends CBListEntityService
{
    const SERVICE_NAME = 'noop-service';
}

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class CBListEntityServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testSave()
    {
        $entity = $this->createEntityMock();
        $manager = $this->createEntityManagerMock();
        $service = $this->createCBListEntityServiceMock(
                array(),
                array($manager, $this->createCBListRepositoryMock())
        );

        $manager->expects($this->once())
                ->method('flush')
                ->with($this->equalTo($entity))
        ;

        $service->save($entity);
    }

    public function testSaveAll()
    {
        $manager = $this->createEntityManagerMock();
        $service = $this->createCBListEntityServiceMock(
                array(),
                array($manager, $this->createCBListRepositoryMock())
        );

        $manager->expects($this->once())->method('flush');

        $service->saveAll();
    }

    private function createCBListEntityServiceMock(
            array $methods = array(), array $constructorArguments = array()
    ) {
        return $this->getMockBuilder(NoopService::class)
                ->setMethods($methods)
                ->setConstructorArgs($constructorArguments)
                ->getMockForAbstractClass()
        ;
    }

    private function createEntityManagerMock()
    {
        return $this->getMockBuilder(EntityManager::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    private function createCBListRepositoryMock()
    {
        return $this->getMockBuilder(CBListRepository::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }

    private function createEntityMock()
    {
        return $this->getMockBuilder(Entity::class)
                ->disableOriginalConstructor()
                ->getMock()
        ;
    }
}
