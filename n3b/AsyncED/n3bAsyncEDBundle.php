<?php

namespace n3b\AsyncED;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use n3b\AsyncED\DependencyInjection\Compiler\RegisterSubscribersPass;

class n3bAsyncEDBundle extends Bundle
{
	public function build( ContainerBuilder $container )
	{
		parent::build( $container );

		$container->addCompilerPass( new RegisterSubscribersPass() );
	}
}
