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

use Sylius\Bundle\MailerBundle\Sender\Adapter\SwiftMailerAdapter;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SymfonyMailerAdapter;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterSenderAdapterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasAlias('sylius.email_sender.adapter')) {
            return;
        }

        if ($container->has('swiftmailer.mailer.default')) {
            $swiftmailerAdapter = new ChildDefinition('sylius.email_sender.adapter.abstract');
            /** @psalm-suppress DeprecatedClass */
            $swiftmailerAdapter->setClass(SwiftMailerAdapter::class);
            $swiftmailerAdapter->setArguments([new Reference('swiftmailer.mailer.default'), new Reference('event_dispatcher', ContainerInterface::IGNORE_ON_INVALID_REFERENCE)]);
            $swiftmailerAdapter->setPublic(true);
            $swiftmailerAdapter->setDeprecated(
                'sylius/mailer-bundle',
                '1.8',
                'The "%service_id%" service is deprecated, use the Symfony Mailer integration instead.'
            );

            $container->setDefinition('sylius.email_sender.adapter.swiftmailer', $swiftmailerAdapter);
            $container->setAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.swiftmailer');

            return;
        }

        if ($container->has('mailer.mailer')) {
            $symfonyMailerAdapter = new ChildDefinition('sylius.email_sender.adapter.abstract');
            $symfonyMailerAdapter->setClass(SymfonyMailerAdapter::class);
            $symfonyMailerAdapter->setArguments([new Reference('mailer.mailer')]);
            $symfonyMailerAdapter->setPublic(true);

            $container->setDefinition('sylius.email_sender.adapter.symfony_mailer', $symfonyMailerAdapter);
            $container->setAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.symfony_mailer');

            return;
        }

        $container->setAlias('sylius.email_sender.adapter', 'sylius.email_sender.adapter.default');
    }
}
