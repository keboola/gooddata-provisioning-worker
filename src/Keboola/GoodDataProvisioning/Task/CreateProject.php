<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Task;

use Symfony\Component\Console\Output\ConsoleOutput;

class CreateProject
{
    /** @var  \Keboola\GoodData\Client */
    protected $gdClient;

    public function __construct(\Keboola\GoodData\Client $gdClient)
    {
        $this->gdClient = $gdClient;

    }

    public function run($params)
    {
        $projectPid = $this->gdClient->getProjects()->createProject($params['name'], $params['authToken']);
    }
}
