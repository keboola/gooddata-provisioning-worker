<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Task;

use Keboola\GoodData\Exception;
use Keboola\GoodDataProvisioning\UserException;

class CreateProject
{
    /** @var  \Keboola\GoodData\Client */
    protected $gdClient;
    /** @var \Doctrine\DBAL\Connection  */
    protected $db;

    public function __construct(\Keboola\GoodData\Client $gdClient, \Doctrine\DBAL\Connection $db)
    {
        $this->gdClient = $gdClient;
        $this->db = $db;
    }

    public function run($params)
    {
        try {
            $projectPid = $this->gdClient->getProjects()->createProject($params['name'], $params['authToken']);
        } catch (Exception $e) {
            if ($e->getCode() === 404) {
                throw new UserException('Project creation failed, check your authToken');
            }
            throw $e;
        }
        $this->db->insert('projects', [
            'pid' => $projectPid,
            'projectId' => getenv('KBC_PROJECTID'),
            'jobId' => getenv('KBC_RUNID'),
            'authToken' => $params['authToken'],
            'createdBy' => getenv('KBC_TOKENDESC')
        ]);
    }
}
