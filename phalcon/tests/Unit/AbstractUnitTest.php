<?php

declare(strict_types=1);

namespace Tests\Unit;

use Phalcon\Di;
use Phalcon\Di\FactoryDefault;
use Phalcon\Incubator\Test\PHPUnit\UnitTestCase;
use PHPUnit\Framework\IncompleteTestError;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

abstract class AbstractUnitTest extends UnitTestCase
{
    private bool $loaded = false;

    protected function setUp(): void
    {
        parent::setUp();

        $di = new FactoryDefault();

        Di::reset();

         $di['db'] = function() {
            return new DbAdapter(array(
                'adapter'     => 'Mysql',
                'host'        => 'mysql',
                'username'    => 'user',
                'password'    => 'user_pw',
                'dbname'      => 'app',
                'charset'     => 'utf8',
            ));
        };

        Di::setDefault($di);

        $this->loaded = true;
    }

    public function __destruct()
    {
        if (!$this->loaded) {
            throw new IncompleteTestError(
                "Please run parent::setUp()."
            );
        }
    }
}