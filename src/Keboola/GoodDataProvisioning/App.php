<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning;

use Keboola\GoodData\Client;
use Keboola\GoodDataProvisioning\Task\CreateProject;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Output\ConsoleOutput;

class App
{
    /** @var ConsoleOutput  */
    private $consoleOutput;
    /** @var Client  */
    private $gdClient;
    /** @var \Doctrine\DBAL\Connection  */
    private $db;

    public function __construct($consoleOutput, $imageParameters)
    {
        $this->consoleOutput = $consoleOutput;
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
                $task = new CreateProject($this->gdClient);
                return $task->run($options['taskParameters']);
                break;
            case 'CreateUser':
                break;
            case 'AddUserToProject':
                break;
            default:
                throw new UserException('Task is not supported');
        }
    }
}
