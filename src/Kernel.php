<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(__DIR__.'/../config/{packages}/*.yaml');
        $container->import(__DIR__.'/../config/{packages}/'.$this->environment.'/*.yaml', null, true);

        $container->import(__DIR__.'/../config/{services}.yaml');
        $container->import(__DIR__.'/../config/{services}_'.$this->environment.'.yaml', null, true);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__.'/../config/{routes}.yaml');
        $routes->import(__DIR__.'/../config/{routes}/'.$this->environment.'/*.yaml', null, true);
    }
}
