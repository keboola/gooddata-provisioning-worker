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
        foreach (['baseUri', '#token'] as $key) {
            if (!isset($config['image_parameters']['api'][$key])) {
                throw new \Exception("Configuration key image_parameters.api.$key is missing");
            }
        }
        foreach (['backendUrl', 'username', '#password', 'domain', 'ssoProvider'] as $key) {
            if (!isset($config['image_parameters']['gd'][$key])) {
                throw new \Exception("Configuration key image_parameters.gd.$key is missing");
            }
        }

        if (!isset($config['parameters']['job'])) {
            throw new UserException("Configuration key parameters.job is missing");
        }

        foreach (['name', 'parameters', 'id'] as $key) {
            if (!isset($config['parameters']['job'][$key])) {
                throw new UserException("Configuration key parameters.job.$key is missing");
            }
        }
        if (!is_array($config['parameters']['job']['parameters'])) {
            throw new UserException('Configuration key parameters.job.parameters must be an array');
        }
        switch ($config['parameters']['job']['name']) {
            case 'CreateProject':
                $this->validateRequired($config, ['name', 'authToken']);
                break;
            case 'CreateUser':
                $this->validateRequired($config, ['firstName', 'lastName', 'login', 'password']);
                if (strlen($config['parameters']['job']['parameters']['password']) < 7) {
                    throw new UserException("Configuration parameter password must have at least seven characters");
                }
                break;
            case 'AddUserToProject':
                $this->validateRequired($config, ['projectId', 'userId', 'role']);
                break;
            case 'RemoveUserFromProject':
                $this->validateRequired($config, ['projectId', 'userId']);
                break;
            default:
                throw new UserException("Task {$config['parameters']['job']['name']} is not supported");
        }
        return $config;
    }

    protected function validateRequired($config, $taskParams)
    {
        if (count($taskParams)) {
            foreach ($taskParams as $key) {
                if (!isset($config['parameters']['job']['parameters'][$key])) {
                    throw new UserException("Configuration key parameters.job.parameters.$key is missing");
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
