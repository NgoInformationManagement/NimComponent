<?php

/*
 * This file is part of the NIM package.
 *
 * (c) Langlade Arnaud
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NIM\Component\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

abstract class NIMExtension extends Extension
{

    /**
     * Remap class parameters.
     *
     * @param array $classes
     * @param ContainerBuilder $container
     */
    protected function mapClassParameters(array $classes, ContainerBuilder $container)
    {
        foreach ($classes as $model => $serviceClasses) {
            foreach ($serviceClasses as $service => $class) {
                $service = $service === 'form' ? 'form.type' : $service;
                $container->setParameter(sprintf('nim.%s.%s.class', $service, $model), $class);
            }
        }
    }

    /**
     * Remap validation group parameters.
     *
     * @param array $classes
     * @param ContainerBuilder $container
     */
    protected function mapValidationGroupParameters(array $validationGroups, ContainerBuilder $container)
    {
        foreach ($validationGroups as $model => $groups) {
            $container->setParameter(sprintf('nim.validation_group.%s', $model), $groups);
        }
    }
}
