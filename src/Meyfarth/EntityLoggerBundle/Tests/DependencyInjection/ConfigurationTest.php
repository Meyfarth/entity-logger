<?php
/**
 * Created by PhpStorm.
 * User: Meyfarth
 * Date: 04/10/14
 * Time: 22:47
 */

namespace Meyfarth\EntityLoggerBundle\Tests\DependencyInjection;


use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use Meyfarth\EntityLoggerBundle\DependencyInjection\Configuration;

class ConfigurationTest extends AbstractConfigurationTestCase {
    protected function getConfiguration(){
        return new Configuration();
    }

    public function testEnabledIsInvalidIfNotBoolean(){
        $this->assertConfigurationIsInvalid(array(
                array('enable' => 44)
            ), 'enable'
        );
    }
} 