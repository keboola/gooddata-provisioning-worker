<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Task;

use Keboola\GoodData\Exception;
use Keboola\GoodData\Projects;
use Keboola\GoodDataProvisioning\UserException;

class RemoveUserFromProject extends AbstractTask
{
    public function run($jobId, $params, $storageToken = null)
    {
        $job = $this->apiClient->getJob($jobId);
        if (!$job) {
            throw new UserException("Job $jobId not found, try again please");
        }
        try {
            $project = $this->apiClient->getProject($params['projectId']);
            if (!$project || empty($project['pid'])) {
                throw new UserException('Project id is invalid');
            }
            $user = $this->apiClient->getUser($params['userId']);
            if (!$user || empty($user['uid'])) {
                throw new UserException('User id is invalid');
            }
            $this->gdClient->getProjects()->leaveProject($project['pid'], $user['uid']);
        } catch (\Exception $e) {
            $error = ($e instanceof Exception || $e instanceof UserException)
                ? $e->getMessage() : 'Failed with unknown error, check the job in KBC.';
            $this->apiClient->updateJob($jobId, ['error' => $error, 'status' => 'error']);
            throw $e;
        }
        $this->apiClient->updateJob($jobId, ['status' => 'success']);
    }
}
