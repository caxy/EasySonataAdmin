<?php

namespace Caxy\EasySonataAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Caxy\EasySonataAdminBundle\DependencyInjection\Compiler\AdminBuilderPass;

class EasySonataAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AdminBuilderPass());
    }
}
