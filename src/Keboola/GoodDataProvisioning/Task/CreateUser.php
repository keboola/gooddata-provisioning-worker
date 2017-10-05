<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Task;

use Keboola\GoodData\Exception;
use Keboola\GoodDataProvisioning\UserException;

class CreateUser extends AbstractTask
{
    public function run($jobId, $params)
    {
        $job = $this->apiClient->getJob($jobId);
        if (!$job) {
            throw new UserException("Job $jobId not found, try again please");
        }
        $options = [
            'firstName' => $params['firstName'],
            'lastName' => $params['lastName'],
            'ssoProvider' => $this->imageParameters['gd']['ssoProvider']
        ];
        if (isset($params['email'])) {
            $options['email'] = strtolower($params['email']);
        }
        try {
            $uid = $this->gdClient->getUsers()->createUser(
                strtolower($params['login']),
                $params['password'],
                $this->imageParameters['gd']['domain'],
                $options
            );
        } catch (\Exception $e) {
            $error = $e instanceof Exception ? $e->getMessage() : 'Failed with unknown error, check the job in KBC.';
            $this->apiClient->updateJob($jobId, ['error' => $error, 'status' => 'error']);
            throw $e;
        }
        $this->apiClient->updateUser($jobId, ['uid' => $uid]);
        $this->apiClient->updateJob($jobId, ['status' => 'success']);
    }
}
