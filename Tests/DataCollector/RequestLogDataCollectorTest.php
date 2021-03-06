<?php
/**
 * This file is a part of the Phystrix Bundle
 *
 * Copyright 2013-2015 oDesk Corporation. All Rights Reserved.
 *
 * This file is licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Odesk\Bundle\PhystrixBundle\Tests\DataCollector;

use Odesk\Bundle\PhystrixBundle\DataCollector\RequestLogDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestLogDataCollectorTest extends \PHPUnit_Framework_TestCase
{

    public function testCollect()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Odesk\Phystrix\RequestLog $requestLogMock */
        $requestLogMock = $this->getMockBuilder('Odesk\Phystrix\RequestLog')
            ->disableOriginalConstructor()
            ->getMock();
        $requestLogMock->expects($this->once())
            ->method('getExecutedCommands')
            ->willReturn(array(
                    $this->prepareCommandMock('Command1', 234, array('e11','e12')),
                    $this->prepareCommandMock('Command2', 345, array('e2')),
                    $this->prepareCommandMock('Command3', 456, array('e31','e32','e33')),
                ));

        $collector = new RequestLogDataCollector($requestLogMock);
        $collector->collect(new Request(), new Response());

        $this->assertEquals(array(
                array('class' => 'Command1', 'duration' => 234, 'events' => array('e11','e12')),
                array('class' => 'Command2', 'duration' => 345, 'events' => array('e2')),
                array('class' => 'Command3', 'duration' => 456, 'events' => array('e31','e32','e33')),
            ), $collector->getCommands());

        $this->assertSame('phystrix', $collector->getName());
    }

    /**
     * @param $name
     * @param $executionTime
     * @param $executionEvents
     * @return \PHPUnit_Framework_MockObject_MockObject|\Odesk\Phystrix\AbstractCommand
     */
    private function prepareCommandMock($name, $executionTime, $executionEvents)
    {
        $commandMock = $this->getMockBuilder('\Odesk\Phystrix\AbstractCommand')
            ->disableOriginalConstructor()
            ->setMockClassName($name)
            ->setMethods(array('getExecutionTimeInMilliseconds', 'getExecutionEvents'))
            ->getMockForAbstractClass();
        $commandMock->expects($this->once())
            ->method('getExecutionTimeInMilliseconds')
            ->willReturn($executionTime);
        $commandMock->expects($this->once())
            ->method('getExecutionEvents')
            ->willReturn($executionEvents);
        return $commandMock;
    }
}
