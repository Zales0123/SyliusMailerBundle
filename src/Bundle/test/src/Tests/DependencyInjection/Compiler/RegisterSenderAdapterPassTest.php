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
use Sylius\Bundle\MailerBundle\DependencyInjection\Compiler\RegisterSenderAdapterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Mailer\Mailer;

final class RegisterSenderAdapterPassTest extends AbstractCompilerPassTestCase
{
    /** @test */
    public function it_does_nothing_if_the_adapter_is_already_configured(): void
    {
        $this->container->setAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.custom');

        $this->compile();

        $this->assertContainerBuilderHasAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.custom');
    }

    /** @test */
    public function it_registers_the_swiftmailer_adapter_when_its_dependencies_are_available(): void
    {
        $this->registerService('swiftmailer.mailer.default', \Swift_Mailer::class);

        $this->compile();

        $this->assertContainerBuilderHasAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.swiftmailer');
    }

    /** @test */
    public function it_registers_the_symfony_mailer_adapter_when_its_dependencies_are_available(): void
    {
        $this->registerService('mailer.mailer', Mailer::class);

        $this->compile();

        $this->assertContainerBuilderHasAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.symfony_mailer');
    }

    /** @test */
    public function it_registers_the_default_adapter_when_no_other_adapters_are_available(): void
    {
        $this->compile();

        $this->assertContainerBuilderHasAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.default');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterSenderAdapterPass());
    }
}
