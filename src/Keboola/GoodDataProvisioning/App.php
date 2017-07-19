<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning;

use Keboola\GoodData\Client;
use Keboola\GoodDataProvisioning\Task\CreateProject;
use Keboola\GoodDataProvisioning\Task\CreateUser;
use Keboola\GoodDataProvisioning\Task\TaskInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Output\ConsoleOutput;

class App
{
    /** @var ConsoleOutput  */
    private $consoleOutput;
    private $imageParameters;
    /** @var Client  */
    private $gdClient;
    /** @var \Doctrine\DBAL\Connection  */
    private $db;

    public function __construct($consoleOutput, $imageParameters)
    {
        $this->consoleOutput = $consoleOutput;
        $this->imageParameters = $imageParameters;
        $this->gdClient = new Client(
            $imageParameters['gd']['backendUrl'],
            new Logger('gooddata-provisioning', [new StreamHandler('php://stdout')])
        );
        $this->gdClient->login($imageParameters['gd']['username'], $imageParameters['gd']['#password']);

        $this->db = \Doctrine\DBAL\DriverManager::getConnection([
            'dbname' => $imageParameters['db']['name'],
            'user' => $imageParameters['db']['user'],
            'password' => $imageParameters['db']['#password'],
            'host' => $imageParameters['db']['host'],
            'driver' => 'pdo_mysql'
        ]);
    }

    public function run($options)
    {
        switch ($options['taskName']) {
            case 'CreateProject':
                $task = new CreateProject($this->gdClient, $this->db, $this->imageParameters);
                $task->run($options['taskParameters']);
                break;
            case 'CreateUser':
                $task = new CreateUser($this->gdClient, $this->db, $this->imageParameters);
                $task->run($options['taskParameters']);
                break;
            case 'AddUserToProject':
                break;
            default:
                throw new UserException('Task is not supported');
        }
    }
}
