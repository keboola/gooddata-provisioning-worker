<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Tests;

use Keboola\GoodDataProvisioning\ConfigParameters;

class ConfigParametersTest extends \PHPUnit\Framework\TestCase
{
    protected $validConfig = [
        'image_parameters' => [
            'db' => ['name' => 'a', 'user' => 'b', '#password' => 'c', 'host' => 'd'],
            'gd' => ['backendUrl' => 'x', 'username' => 'y', '#password' => 'z']
        ],
        'parameters' => [
            'taskName' => 'CreateProject',
            'taskParameters' => ['name' => 'Test', 'authToken' => 'token']
        ]
    ];

    public function testValidConfig()
    {
        $params = new ConfigParameters($this->validConfig);
        $this->assertArrayHasKey('taskName', $params->getParameters());
        $this->assertArrayHasKey('taskParameters', $params->getParameters());
        $this->assertArrayHasKey('name', $params->getParameters()['taskParameters']);
        $this->assertArrayHasKey('authToken', $params->getParameters()['taskParameters']);
        $this->assertArrayHasKey('db', $params->getImageParameters());
        $this->assertArrayHasKey('gd', $params->getImageParameters());
    }

    public function testMissingImageParameters()
    {
        $config = $this->validConfig;
        unset($config['image_parameters']['db']['name']);
        $this->checkConfig($config);

        $config = $this->validConfig;
        unset($config['image_parameters']['db']['#password']);
        $this->checkConfig($config);

        $config = $this->validConfig;
        unset($config['image_parameters']['gd']['backendUrl']);
        $this->checkConfig($config);

        $config = $this->validConfig;
        unset($config['image_parameters']['gd']['#password']);
        $this->checkConfig($config);
    }

    public function testMissingParameters()
    {
        $config = $this->validConfig;
        unset($config['parameters']['taskName']);
        $this->checkConfig($config);

        $config = $this->validConfig;
        unset($config['parameters']['taskParameters']);
        $this->checkConfig($config);
    }

    public function testWrongTaskName()
    {
        $config = $this->validConfig;
        $config['parameters']['taskName'] = 'Whatever';
        $this->checkConfig($config);
    }

    public function testMissingCreateProjectParameters()
    {
        $config = $this->validConfig;
        unset($config['parameters']['taskParameters']['name']);
        $this->checkConfig($config);

        $config = $this->validConfig;
        unset($config['parameters']['taskParameters']['authToken']);
        $this->checkConfig($config);
    }

    private function checkConfig($config)
    {
        try {
            new ConfigParameters($config);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
