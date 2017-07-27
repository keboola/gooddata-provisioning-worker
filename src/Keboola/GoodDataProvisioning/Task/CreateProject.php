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
    public function run($jobId, $params)
    {
        $job = $this->apiClient->getProjectJob($jobId);
        if (!$job) {
            throw new UserException("Job $jobId not found, try again please");
        }
        try {
            $pid = $this->gdClient->getProjects()->createProject($params['name'], $params['authToken']);
        } catch (Exception $e) {
            if ($e->getCode() === 404) {
                $err = 'Project creation failed, check your authToken';
                $this->apiClient->updateProjectJob($jobId, ['error' => $err, 'status' => 'error']);
                throw new UserException($err);
            }
            $this->apiClient->updateProjectJob($jobId, ['error' => $e->getMessage(), 'status' => 'error']);
            throw $e;
        }
        $this->apiClient->updateProjectJob($jobId, ['pid' => $pid, 'status' => 'ready']);
    }
}
