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

class AddUserToProject extends AbstractTask
{
    public function run($jobId, $params, $storageToken = null)
    {
        $job = $this->apiClient->getJob($jobId);
        if (!$job) {
            throw new UserException("Job $jobId not found, try again please");
        }
        try {
            $allowedRoles = array_keys(Projects::$roles);
            if (!in_array($params['role'], $allowedRoles)) {
                throw new UserException("Parameter role is not valid, must be one of: " . implode(', ', $allowedRoles));
            }

            $project = $this->apiClient->getProject($params['projectId']);
            if (!$project || empty($project['pid'])) {
                throw new UserException('Project id is invalid');
            }
            $user = $this->apiClient->getUser($params['userId']);
            if (!$user || empty($user['uid'])) {
                throw new UserException('User id is invalid');
            }
            $this->gdClient->getProjects()->addUser($project['pid'], $user['uid'], Projects::$roles[$params['role']]);
        } catch (\Exception $e) {
            $error = ($e instanceof Exception || $e instanceof UserException)
                ? $e->getMessage() : 'Failed with unknown error, check the job in KBC.';
            $this->apiClient->updateJob($jobId, ['error' => $error, 'status' => 'error']);
            throw $e;
        }
        $this->apiClient->updateJob($jobId, ['status' => 'success']);
    }
}
