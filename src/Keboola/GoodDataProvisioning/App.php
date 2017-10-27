<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning;

use Keboola\GoodData\Client;
use Keboola\GoodDataProvisioning\Task\AddUserToProject;
use Keboola\GoodDataProvisioning\Task\CreateProject;
use Keboola\GoodDataProvisioning\Task\CreateUser;
use Keboola\GoodDataProvisioning\Task\RemoveUserFromProject;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Output\ConsoleOutput;

class App
{
    /** @var ConsoleOutput  */
    private $consoleOutput;
    private $imageParameters;
    /** @var Client  */
    private $gdClient;
    /** @var  ApiClient */
    private $apiClient;

    public function __construct($consoleOutput, $imageParameters)
    {
        $this->consoleOutput = $consoleOutput;
        $this->imageParameters = $imageParameters;
        $this->gdClient = new Client(
            $imageParameters['gd']['backendUrl'],
            new Logger('gooddata-provisioning', [new StreamHandler('php://stdout')])
        );
        $this->gdClient->login($imageParameters['gd']['username'], $imageParameters['gd']['#password']);
        $this->apiClient = new ApiClient($imageParameters['api']['baseUri'], $imageParameters['api']['#token']);
    }

    public function run($options)
    {
        switch ($options['job']['name']) {
            case 'CreateProject':
                $task = new CreateProject($this->gdClient, $this->apiClient, $this->imageParameters);
                $task->run($options['job']['id'], $options['job']['parameters']);
                break;
            case 'CreateUser':
                $task = new CreateUser($this->gdClient, $this->apiClient, $this->imageParameters);
                $task->run($options['job']['id'], $options['job']['parameters']);
                break;
            case 'AddUserToProject':
                $task = new AddUserToProject($this->gdClient, $this->apiClient, $this->imageParameters);
                $task->run($options['job']['id'], $options['job']['parameters']);
                break;
            case 'RemoveUserFromProject':
                $task = new RemoveUserFromProject($this->gdClient, $this->apiClient, $this->imageParameters);
                $task->run($options['job']['id'], $options['job']['parameters']);
                break;
            default:
                throw new UserException('Task is not supported');
        }
    }
}
