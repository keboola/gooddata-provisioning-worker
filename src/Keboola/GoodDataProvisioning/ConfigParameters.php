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
        foreach (['backendUrl', 'username', '#password', 'domain', 'ssoProvider'] as $key) {
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
                $this->validateRequired($config, ['name', 'authToken']);
                break;
            case 'CreateUser':
                $this->validateRequired($config, ['firstName', 'lastName', 'login', 'password']);
                if (strlen($config['parameters']['taskParameters']['password']) < 7) {
                    throw new UserException("Configuration parameter password must have at least seven characters");
                }
                break;
            case 'AddUserToProject':
                break;
            default:
                throw new UserException('Task is not supported');
        }
        return $config;
    }

    protected function validateRequired($config, $taskParams)
    {
        if (count($taskParams)) {
            foreach ($taskParams as $key) {
                if (!isset($config['parameters']['taskParameters'][$key])) {
                    throw new UserException("Configuration key parameters.taskParameters.$key is missing");
                }
            }
        }
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
