<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Task;

use Keboola\GoodData\Exception;
use Keboola\GoodDataProvisioning\UserException;
use Keboola\StorageApi\Client;

class CreateProject extends AbstractTask
{
    public function run($jobId, $params, $storageToken = null)
    {
        $job = $this->apiClient->getJob($jobId);
        if (!$job) {
            throw new UserException("Job $jobId not found, try again please");
        }
        if (!isset($params['authToken'])) {
            $storageClient = new Client(['token' => $storageToken]);
            $tokenData = $storageClient->verifyToken();
            $tokenName = (isset($tokenData['owner']['type']) && $tokenData['owner']['type'] == 'production')
                ? 'keboola_production' : 'keboola_demo';
            $token = null;
            foreach ($this->apiClient->listTokens() as $t) {
                if ($t['name'] == $tokenName) {
                    $token = $t['token'];
                    break;
                }
            }
            if (!$token) {
                throw new \Exception("Auth token $tokenName was not found");
            }
        } else {
            $token = $params['authToken'];
        }
        try {
            $pid = $this->gdClient->getProjects()->createProject($params['name'], $token);
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
        $this->apiClient->updateProject($jobId, ['pid' => $pid, 'token' => $token]);
        $this->apiClient->updateJob($jobId, ['status' => 'success']);
    }
}
