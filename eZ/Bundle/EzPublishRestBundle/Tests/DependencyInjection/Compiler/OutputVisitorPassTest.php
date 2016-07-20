<?php

/**
 * File containing the OutputVisitorPassTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @version //autogentag//
 */
namespace eZ\Bundle\EzPublishRestBundle\Tests\DependencyInjection\Compiler;

use eZ\Bundle\EzPublishRestBundle\DependencyInjection\Compiler\OutputVisitorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use PHPUnit_Framework_TestCase;

class OutputVisitorPassTest extends PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $regexp1 = array('(^.*/.*$)');
        $regexp2 = array('(^application/json$)');

        $stringDefinition = new Definition();
        $stringDefinition->addTag('ezpublish_rest.output.visitor', array('regexps' => 'ezpublish_rest.output.visitor.test1.regexps'));

        $arrayDefinition = new Definition();
        $arrayDefinition->addTag('ezpublish_rest.output.visitor', array('regexps' => array($regexp1)));

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(
            array(
                'ezpublish_rest.output.visitor.dispatcher' => new Definition(),
                'ezpublish_rest.output.visitor.test_string' => $stringDefinition,
                'ezpublish_rest.output.visitor.test_array' => $arrayDefinition,
            )
        );

        $containerBuilder->setParameter('ezpublish_rest.output.visitor.test1.regexps', array($regexp2));

        $compilerPass = new OutputVisitorPass();
        $compilerPass->process($containerBuilder);

        $dispatcherMethodCalls = $containerBuilder->getDefinition('ezpublish_rest.output.visitor.dispatcher')->getMethodCalls();
        self::assertTrue(isset($dispatcherMethodCalls[0][0]));
        self::assertTrue(isset($dispatcherMethodCalls[0][1]));
        self::assertEquals('addVisitor', $dispatcherMethodCalls[0][0]);
        self::assertEquals('addVisitor', $dispatcherMethodCalls[1][0]);
        self::assertInstanceOf('Symfony\\Component\\DependencyInjection\\Reference', $dispatcherMethodCalls[0][1][1]);
        self::assertInstanceOf('Symfony\\Component\\DependencyInjection\\Reference', $dispatcherMethodCalls[1][1][1]);

        self::assertEquals('ezpublish_rest.output.visitor.test_string', $dispatcherMethodCalls[0][1][1]->__toString());
        self::assertEquals('ezpublish_rest.output.visitor.test_array', $dispatcherMethodCalls[1][1][1]->__toString());
    }

    public function testPriority()
    {
        $regexp1 = array('(^.*/.*$)');
        $regexp2 = array('(^application/.*$)');
        $regexp3 = array('(^application/json$)');
        $regexp4 = array('(^application/xml$)');

        $highDefinition = new Definition();
        $highDefinition->addTag('ezpublish_rest.output.visitor', [
            'regexps' => [$regexp1],
            'priority' => 10,
        ]);

        $lowDefinition = new Definition();
        $lowDefinition->addTag('ezpublish_rest.output.visitor', [
            'regexps' => [$regexp2],
            'priority' => -10,
        ]);

        $definedNormalDefinition = new Definition();
        $definedNormalDefinition->addTag('ezpublish_rest.output.visitor', [
            'regexps' => [$regexp3],
            'prority' => 0,
        ]);

        $normalDefinition = new Definition();
        $normalDefinition->addTag('ezpublish_rest.output.visitor', [
            'regexps' => [$regexp4],
        ]);

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            'ezpublish_rest.output.visitor.dispatcher' => new Definition(),
            'ezpublish_rest.output.visitor.test_defined_normal' => $definedNormalDefinition,
            'ezpublish_rest.output.visitor.test_low' => $lowDefinition,
            'ezpublish_rest.output.visitor.test_normal' => $normalDefinition,
            'ezpublish_rest.output.visitor.test_high' => $highDefinition,
        ]);

        $compilerPass = new OutputVisitorPass();
        $compilerPass->process($containerBuilder);

        $dispatcherMethodCalls = $containerBuilder->getDefinition('ezpublish_rest.output.visitor.dispatcher')->getMethodCalls();

        self::assertEquals('ezpublish_rest.output.visitor.test_high', $dispatcherMethodCalls[0][1][1]->__toString());
        self::assertEquals('ezpublish_rest.output.visitor.test_defined_normal', $dispatcherMethodCalls[1][1][1]->__toString());
        self::assertEquals('ezpublish_rest.output.visitor.test_normal', $dispatcherMethodCalls[2][1][1]->__toString());
        self::assertEquals('ezpublish_rest.output.visitor.test_low', $dispatcherMethodCalls[3][1][1]->__toString());
    }
}
