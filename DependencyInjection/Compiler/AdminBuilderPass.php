<?php

namespace Caxy\EasySonataAdminBundle\DependencyInjection\Compiler;

use Caxy\EasySonataAdminBundle\Admin\AutoAdmin;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class AdminBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('easy_sonata_admin');

        $definitions = array();

            foreach ($config['entities'] as $name => $entity) {
                $class = $entity['class'];

                $definition = new Definition(AutoAdmin::class,
                    array(null, $class, null, $entity, $name));
                $definition->setPublic(true);
                $definition->addTag('sonata.admin', array('manager_type' => 'orm', 'label' => $name));

                $definitions['admin.'.$name] = $definition;
        }

        $container->addDefinitions($definitions);
    }
}
