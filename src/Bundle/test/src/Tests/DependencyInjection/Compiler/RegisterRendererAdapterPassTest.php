<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\MailerBundle\test\src\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\MailerBundle\DependencyInjection\Compiler\RegisterRendererAdapterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Environment;

final class RegisterRendererAdapterPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_does_nothing_if_the_adapter_is_already_configured(): void
    {
        $this->container->setAlias('sylius.email_renderer.adapter', 'sylius.email_renderer.adapter.custom');

        $this->compile();

        $this->assertContainerBuilderHasAlias('sylius.email_renderer.adapter', 'sylius.email_renderer.adapter.custom');
    }

    /** @test */
    public function it_registers_the_twig_adapter_when_its_dependencies_are_available(): void
    {
        $this->registerService('twig', Environment::class);

        $this->compile();

        $this->assertContainerBuilderHasAlias('sylius.email_renderer.adapter', 'sylius.email_renderer.adapter.twig');
    }

    /** @test */
    public function it_registers_the_default_adapter_when_twig_is_not_available(): void
    {
        $this->compile();

        $this->assertContainerBuilderHasAlias('sylius.email_renderer.adapter', 'sylius.email_renderer.adapter.default');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterRendererAdapterPass());
    }
}
