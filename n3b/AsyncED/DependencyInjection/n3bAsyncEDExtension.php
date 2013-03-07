<?php

namespace n3b\AsyncED\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
	Symfony\Component\DependencyInjection\Loader\YamlFileLoader,
	Symfony\Component\HttpKernel\DependencyInjection\Extension,
	Symfony\Component\Config\FileLocator;


class n3bAsyncEDExtension extends Extension
{

	public function load(array $configs, ContainerBuilder $container)
	{
		$config = array();
		foreach( $configs as $subConfig ) $config = array_merge( $config, $subConfig );

		$loader = new YamlFileLoader( $container, new FileLocator(__DIR__ . '/../Resources/config') );
		$loader->load( 'services.yml' );
	}

	public function getAlias()
	{
		return 'n3b_async_ed';
	}
}