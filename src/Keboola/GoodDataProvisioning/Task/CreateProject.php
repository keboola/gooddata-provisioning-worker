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
        $job = $this->apiClient->getJob($jobId);
        if (!$job) {
            throw new UserException("Job $jobId not found, try again please");
        }
        try {
            $pid = $this->gdClient->getProjects()->createProject($params['name'], $params['authToken']);
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                $err = 'Project creation failed, check your authToken';
                $this->apiClient->updateJob($jobId, ['error' => $err, 'status' => 'error']);
                throw new UserException($err);
            }
            $error = $e instanceof Exception ? $e->getMessage() : 'Failed with unknown error, check the job in KBC.';
            $this->apiClient->updateJob($jobId, ['error' => $error, 'status' => 'error']);
            throw $e;
        }
        $this->apiClient->updateProject($jobId, ['pid' => $pid]);
        $this->apiClient->updateJob($jobId, ['status' => 'success']);
    }
}
