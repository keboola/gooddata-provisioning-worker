<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning;

use Symfony\Component\Config\Definition\Processor;

class ConfigParameters
{
    private $parameters;

    public function __construct(array $config)
    {
        $this->parameters = (new Processor)->processConfiguration(
            new ConfigDefinition(),
            [$config['parameters']]
        );
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
