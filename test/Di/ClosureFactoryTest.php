<?php
use chilimatic\lib\Di\ClosureFactory;

/**
 * Created by PhpStorm.
 * User: j
 * Date: 25.11.14
 * Time: 22:14
 */
class ClosureFactory_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @before
     */
    public function destroyDI()
    {
        ClosureFactory::destroyInstance();
    }

    /**
     * @test
     */
    public function closureFactorySingeltonInstance()
    {
        self::assertInstanceOf('\chilimatic\lib\di\ClosureFactory', ClosureFactory::getInstance());
    }

    /**
     * @test
     */
    public function setServiceClosure()
    {
        $di = ClosureFactory::getInstance();
        $di->set('my-test', function () use ($di) {
            return 'my-test';
        });

        self::assertEquals('my-test', $di->get('my-test'));
    }

    /**
     * @test
     */
    public function loadClosureSetFromFileViaConstructor()
    {
        $di = ClosureFactory::getInstance(
            __DIR__ . '/../testdata/test-service-list.php'
        );

        $testArray = ['test1', 'test2'];

        self::assertEquals($testArray, $di->get('my-test', $testArray));
    }

    /**
     * @test
     */
    public function loadClosureSetByArrayViaConstructor()
    {
        $di = ClosureFactory::getInstance(null, [
                'my-test' => function ($setting) {
                    return $setting;
                }
            ]
        );

        $testArray = ['test1', 'test2'];

        self::assertEquals($testArray, $di->get('my-test', $testArray));
    }

    /**
     * @test
     */
    public function loadClosureSetByArray()
    {
        $di = ClosureFactory::getInstance();
        $di->setServiceList([
            'my-test' => function ($setting) {
                return $setting;
            }
        ]);

        $testArray = ['test1', 'test2'];

        self::assertEquals($testArray, $di->get('my-test', $testArray));

    }

    /**
     * @test
     */
    public function loadClosureSetByFile()
    {
        $di = ClosureFactory::getInstance();
        $di->loadServiceFromFile(__DIR__ . '/../testdata/test-service-list.php');

        $testArray = ['test1', 'test2'];

        self::assertEquals($testArray, $di->get('my-test', $testArray));
    }

    /**
     * @test
     */
    public function overrideClosureBySet()
    {
        $di = ClosureFactory::getInstance();
        $di->loadServiceFromFile(__DIR__ . '/../testdata/test-service-list.php');

        $di->set('my-test', function ($setting) {
            return array_pop($setting);
        });
        $testArray = ['test1', 'test2'];

        self::assertEquals('test2', $di->get('my-test', $testArray));
    }

    /**
     * @test
     *
     * @expectedException BadFunctionCallException
     * @expectedExceptionMessage my-test closure is missing
     */
    public function tryToGetNonExistingClosure()
    {
        $di = ClosureFactory::getInstance();
        $di->get('my-test');
    }

    /**
     * @test
     *
     * @expectedException BadFunctionCallException
     * @expectedExceptionMessage my-test closure is missing
     */
    public function removeClosure()
    {
        $di = ClosureFactory::getInstance();
        $di->set('my-test', function () {
        });
        $di->remove('my-test');
        $di->get('my-test');
    }

    /**
     * @test
     */
    public function getClosure()
    {
        $di = ClosureFactory::getInstance();
        $di->set('my-test', function () {
        });

        self::assertInstanceOf('\Closure', $di->getClosure('my-test'));
    }

    /**
     * @test
     *
     * @expectedException BadFunctionCallException
     * @expectedExceptionMessage my-test closure is missing
     */
    public function destroyInstance()
    {
        $di = ClosureFactory::getInstance();
        $di->set('my-test', function () {
        });

        ClosureFactory::destroyInstance();

        $di = ClosureFactory::getInstance();
        $di->set('my-test2', function () {});

        $di->get('my-test');
    }

    /**
     * @test
     */
    public function checkIfClosureExists()
    {
        $di = ClosureFactory::getInstance();
        $di->set('my-test', function () {
        });

        self::assertEquals(true, $di->exists('my-test'));
    }


    /**
     * @test
     */
    public function checkIfPseudoSingeltonIsWorking()
    {
        $di = ClosureFactory::getInstance();
        $di->set('my-test', function () {
            return new \stdClass();
        });

        $asSingelton  = $di->get('my-test', [], true);
        $asSingelton2 = $di->get('my-test', [], true);

        self::assertEquals(true, $asSingelton === $asSingelton2);
    }
}