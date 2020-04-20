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

namespace Sylius\Bundle\MailerBundle\DependencyInjection\Compiler;

use Sylius\Bundle\MailerBundle\Renderer\Adapter\EmailTwigAdapter;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterRendererAdapterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasAlias('sylius.email_renderer.adapter')) {
            return;
        }

        if ($container->has('twig')) {
            $twigAdapter = new ChildDefinition('sylius.email_renderer.adapter.abstract');
            $twigAdapter->setClass(EmailTwigAdapter::class);
            $twigAdapter->setArguments([new Reference('twig'), new Reference('event_dispatcher', ContainerInterface::IGNORE_ON_INVALID_REFERENCE)]);
            $twigAdapter->setPublic(true);

            $container->setDefinition('sylius.email_renderer.adapter.twig', $twigAdapter);
            $container->setAlias('sylius.email_renderer.adapter', 'sylius.email_renderer.adapter.twig');

            return;
        }

        $container->setAlias('sylius.email_renderer.adapter', 'sylius.email_renderer.adapter.default');
    }
}
