<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning;

class ConfigParameters
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $this->validate($config);
    }

    protected function validate($config)
    {
        foreach (['name', 'user', '#password', 'host'] as $key) {
            if (!isset($config['image_parameters']['db'][$key])) {
                throw new \Exception("Configuration key image_parameters.db.$key is missing");
            }
        }
        foreach (['backendUrl', 'username', '#password'] as $key) {
            if (!isset($config['image_parameters']['gd'][$key])) {
                throw new \Exception("Configuration key image_parameters.gd.$key is missing");
            }
        }

        foreach (['taskName', 'taskParameters'] as $key) {
            if (!isset($config['parameters'][$key])) {
                throw new UserException("Configuration key parameters.$key is missing");
            }
        }
        if (!is_array($config['parameters']['taskParameters'])) {
            throw new UserException('Configuration key parameters.taskParameters must be an array');
        }
        switch ($config['parameters']['taskName']) {
            case 'CreateProject':
                foreach (['name', 'authToken'] as $key) {
                    if (!isset($config['parameters']['taskParameters'][$key])) {
                        throw new UserException("Configuration key parameters.taskParameters.$key is missing");
                    }
                }
                break;
            case 'CreateUser':
                break;
            case 'AddUserToProject':
                break;
            default:
                throw new UserException('Task is not supported');
        }
        return $config;
    }

    public function getImageParameters()
    {
        return $this->config['image_parameters'];
    }

    public function getParameters()
    {
        return $this->config['parameters'];
    }
}
