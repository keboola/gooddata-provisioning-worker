<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Task;

use Keboola\GoodData\Exception;
use Keboola\GoodDataProvisioning\UserException;

class CreateProject extends AbstractTask
{
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
            'authToken' => $params['authToken'],
            'createdById' => getenv('KBC_TOKENID'),
            'createdByName' => getenv('KBC_TOKENDESC')
        ]);
    }
}
