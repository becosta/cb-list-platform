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


use CBList\ModelBundle\Entity\Entity;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class EntityTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $data = array('id' => 1);
        $entity = $this->createEntityMock(array('hydrate'));
        $entity->expects($this->once())
                ->method('hydrate')
                ->with($this->equalTo($data))
        ;

        $instance = new \ReflectionClass('\CBList\ModelBundle\Entity\Entity');
        $instance->getConstructor()->invoke($entity, $data);
    }

    public function testConstructorEmptyArray()
    {
        $data = array();
        $entity = $this->createEntityMock(array('hydrate'));
        $entity->expects($this->once())
                ->method('hydrate')
                ->with($this->equalTo($data))
        ;

        $instance = new \ReflectionClass('\CBList\ModelBundle\Entity\Entity');
        $instance->getConstructor()->invoke($entity, $data);
    }

    public function testConstructorRandomValueArray()
    {
        $data = array('key' => 'value');
        $entity = $this->createEntityMock(array('hydrate'));
        $entity->expects($this->once())
                ->method('hydrate')
                ->with($this->equalTo($data))
        ;

        $instance = new \ReflectionClass('\CBList\ModelBundle\Entity\Entity');
        $instance->getConstructor()->invoke($entity, $data);
    }

    public function testConstructorNullArg()
    {
        $entity = $this->createEntityMock(array('hydrate'));
        $entity->expects($this->never())->method('hydrate');

        $class = new \ReflectionClass('\CBList\ModelBundle\Entity\Entity');
        $class->getConstructor()->invoke($entity, null);
    }

    public function testHydratePositiveValue()
    {
        $data = array('id' => 1);
        $entity = $this->createEntityMock(array());
        $method = $this->getMethod('hydrate');
        $method->invokeArgs($entity, array($data));
        $this->assertAttributeEquals(1, 'id', $entity);
    }

    public function testHydrateZeroValue()
    {
        $data = array('id' => 0);
        $entity = $this->createEntityMock(array());
        $method = $this->getMethod('hydrate');
        $method->invokeArgs($entity, array($data));
        $this->assertAttributeEquals(0, 'id', $entity);
    }

    public function testHydrateNegativeValue()
    {
        $data = array('id' => -1);
        $entity = $this->createEntityMock(array());
        $method = $this->getMethod('hydrate');
        $method->invokeArgs($entity, array($data));
        $this->assertAttributeEquals(-1, 'id', $entity);
    }

    public function testHydrateNullValue()
    {
        $data = array('id' => null);
        $entity = $this->createEntityMock(array());
        $method = $this->getMethod('hydrate');
        $method->invokeArgs($entity, array($data));
        $this->assertAttributeEquals(null, 'id', $entity);
    }

    public function testHydrateStringValue()
    {
        $data = array('id' => 'id');
        $entity = $this->createEntityMock(array());
        $method = $this->getMethod('hydrate');
        $method->invokeArgs($entity, array($data));
        $this->assertAttributeEquals('id', 'id', $entity);
    }

    public function testHydrateEmptyArray()
    {
        $data = array();
        $entity = $this->createEntityMock(array());
        $method = $this->getMethod('hydrate');
        $method->invokeArgs($entity, array($data));
        $this->assertAttributeEquals(null, 'id', $entity);
    }

    /**
     *
     * @return Entity
     */
    private function createEntityMock(array $methods)
    {
        return $this->getMockBuilder(Entity::class)
                ->disableOriginalConstructor()
                ->setMethods($methods)
                ->getMockForAbstractClass()
        ;
    }

    protected function getMethod(
            $methodName, $className = '\CBList\ModelBundle\Entity\Entity'
    ) {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }
}
