<?php namespace Orchestra\Extension\TestCase;

use Orchestra\Extension\RouteGenerator;

class RouteGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Orchestra\Extension\RouteGenerator construct proper route.
     *
     * @test
     */
    public function testConstructProperRoute()
    {
        $stub   = new RouteGenerator("foo", "http://localhost/laravel");
        $refl   = new \ReflectionObject($stub);
        $domain = $refl->getProperty('domain');
        $prefix = $refl->getProperty('prefix');
        $secure = $refl->getProperty('secure');

        $domain->setAccessible(true);
        $prefix->setAccessible(true);
        $secure->setAccessible(true);

        $this->assertFalse($secure->getValue($stub));
        $this->assertNull($domain->getValue($stub));
        $this->assertEquals('foo', $prefix->getValue($stub));

        $this->assertEquals(null, $stub->domain());
        $this->assertEquals('localhost', $stub->domain(true));
        $this->assertEquals('foo', $stub->prefix());
        $this->assertEquals('laravel/foo', $stub->prefix(true));
        $this->assertEquals('foo', (string) $stub);
        $this->assertEquals('http://localhost/laravel/foo', $stub->root());
    }

    /**
     * Test Orchestra\Extension\RouteGenerator with domain route.
     *
     * @test
     */
    public function testRouteWithDomain()
    {
        $stub1 = new RouteGenerator("//blog.orchestraplatform.com");
        $stub2 = new RouteGenerator("//blog.orchestraplatform.com/hello");
        $stub3 = new RouteGenerator("//blog.orchestraplatform.com/hello/world");

        $this->assertEquals("blog.orchestraplatform.com", $stub1->domain());
        $this->assertEquals("/", $stub1->prefix());
        $this->assertEquals("http://blog.orchestraplatform.com", $stub1->root());
        $this->assertEquals("http://blog.orchestraplatform.com/foo", $stub1->to('foo'));

        $this->assertEquals("blog.orchestraplatform.com", $stub2->domain());
        $this->assertEquals("hello", $stub2->prefix());
        $this->assertEquals("http://blog.orchestraplatform.com/hello", $stub2->root());
        $this->assertEquals("http://blog.orchestraplatform.com/hello/foo", $stub2->to('foo'));

        $this->assertEquals("blog.orchestraplatform.com", $stub3->domain());
        $this->assertEquals("hello/world", $stub3->prefix());
        $this->assertEquals("http://blog.orchestraplatform.com/hello/world", $stub3->root());
        $this->assertEquals("http://blog.orchestraplatform.com/hello/world/foo", $stub3->to('foo'));
    }
}
