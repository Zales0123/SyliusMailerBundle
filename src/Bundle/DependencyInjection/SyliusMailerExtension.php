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

namespace Sylius\Bundle\MailerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

final class SyliusMailerExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        if (isset($mergedConfig['sender_adapter'])) {
            $container->setAlias('sylius.email_sender.adapter', $mergedConfig['sender_adapter']);
        }

        if (isset($mergedConfig['renderer_adapter'])) {
            $container->setAlias('sylius.email_renderer.adapter', $mergedConfig['renderer_adapter']);
        }

        $container->setParameter('sylius.mailer.sender_name', $mergedConfig['sender']['name']);
        $container->setParameter('sylius.mailer.sender_address', $mergedConfig['sender']['address']);

        $templates = $mergedConfig['templates'] ?? ['Default' => '@SyliusMailer/default.html.twig'];

        $container->setParameter('sylius.mailer.emails', $mergedConfig['emails']);
        $container->setParameter('sylius.mailer.templates', $templates);
    }
}
