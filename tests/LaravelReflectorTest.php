<?php

use Kalani\LaravelReflector\LaravelReflector;
use \Mockery as m;

class LaravelReflectorTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testCanInstantiateAndGetMyOwnClass() 
    {
        $test = new LaravelReflector(Null, Null);
        $this->assertEquals('Kalani\LaravelReflector\LaravelReflector', get_class($test));
    }

    public function testCanWriteOutputInCorrectFormat()
    {
        $test = new LaravelReflector(Null, Null);

        ob_start();
        $test->write('Foo', array('foo'=>'bar', 'bazz'=>'buzz'));
        $result = ob_get_contents();

        // We expect 2 spaces before the name; description starts on col 33
        $expected = "Foo\r\n" 
                  . "  foo" . str_repeat(' ', 28) . "bar\r\n"
                  . "  bazz" . str_repeat(' ', 27) . "buzz\r\n\r\n";
        ob_end_clean();

        $this->assertEquals($expected, $result);
    }

    public function testSkipWritingForEmptyArray()
    {
        $test = new LaravelReflector(Null, Null);

        ob_start();
        $test->write('Foo', array());
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(Null, $result);

    }

    // public function testCanGetFacadeRootName()
    // {
    //     $class = m::mock('RandomClass')->shouldReceive('getFacadeRoot')->andReturn('Bazz')->getMock();
    //     $app = m::mock('App')->shouldReceive('make')->andReturn($class)->getMock();
    //     $test = new LaravelReflector($app, Null);

    //     $this->assertEquals('Bazz', $test->getFacade('buzz'));
    // }

    private function getAppMock()
    {
        $app = m::mock('App');
        return $app;        
    }


}


