<?php

/*
 * This file is part of the NIM package.
 *
 * (c) Langlade Arnaud
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NIM\Component;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

abstract class Bundle extends BaseBundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new ResolveDoctrineTargetEntitiesPass(
                $this->getBundlePrefix(),
                $this->getEntities()
            )
        );

        if (null !== $this->getEntityPath()) {
            foreach ($this->getMapping() as $key => $value) {
                $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver(
                    array($key => $value),
                    array('doctrine.orm.entity_manager'),
                    $this->getBundlePrefix().'.driver.doctrine/orm'
                ));
            }
        }
    }

    /**
     * Return array of currently supported drivers.
     *
     * @return array
     */
    public static function getSupportedDrivers()
    {
        return array();
    }

    /**
     * Return the prefix of the bundle
     *
     * @return string
     */
    abstract protected function getBundlePrefix();

    /**
     * Return an array of entity mapping (className - container parameter name)
     *
     * @return array
     */
    abstract protected function getEntities();

    /**
     * Return xml mapping (XML - Entity)
     *
     * @return array
     */
    protected function getMapping()
    {
        $mapping = array();

        $xmlRootDir = $this->getXmlFilesPath($this->getEntityPath());
        $entityNamespace = $this->getEntityNamespace($this->getEntityPath());

        //$mapping[$xmlRootDir] = $entityNamespace;

        $finder = new Finder();
        $finder->directories()->in($xmlRootDir);

        foreach ($finder as $dir) {
            $mapping[$dir->getRealpath()] = $this->getEntityNamespace($dir->getRelativePathname(), $entityNamespace);
        }
        $mapping[$xmlRootDir] = $entityNamespace;
        return $mapping;
    }

    /**
     * Generate the path to the xml directory
     *
     * @return string
     * @throws \Exception
     */
    protected function getXmlFilesPath($entityPath)
    {
        $xmlFilesPath = sprintf("%s/Resources/config/doctrine/%s", $this->getPath(), strtolower($entityPath));

        if(false == ($realXmlFilesPath = realpath($xmlFilesPath))) {
            throw new \Exception('');
        }

        return $realXmlFilesPath;
    }

    protected function getEntityNamespace($entityPath, $baseNamespace = null)
    {
        if (null == $baseNamespace) {
            $baseNamespace = $this->getNamespace();
        }

        if (false !== strpos($entityPath, '/')) {
            $explodedPath = explode('/', $entityPath);

            $explodedPath = array_map(function ($val) {
                return ucfirst($val);
            }, $explodedPath);

            $entityPath = implode('\\', $explodedPath);
        }
        return  sprintf("%s\\%s", $baseNamespace, ucfirst($entityPath));
    }

    /**
     * Return the path to the Entity directory
     * It only manage one level
     *
     * @return string
     */
    protected function getEntityPath()
    {
        return null;
    }
}
