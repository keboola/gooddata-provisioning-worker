<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Tests;

use Keboola\GoodDataProvisioning\UserException;

class ApiClient extends \Keboola\GoodDataProvisioning\ApiClient
{
    protected $projects;
    protected $users;

    public function __construct()
    {
        parent::__construct(uniqid(), uniqid());
    }

    public function createProject($projectId, $authToken, $createdBy)
    {
        $jobId = rand(1, 255);
        $this->projects[$jobId] = [
            'pid' => null,
            'projectId' => $projectId,
            'authToken' => $authToken,
            'createdOn' => date('c'),
            'createdBy' => $createdBy,
            'deletedOn' => null,
            'deletedBy' => null,
            'status' => 'waiting',
            'error' => null
        ];
        return $jobId;
    }

    public function getProjectJob($id)
    {
        if (!isset($this->projects[$id])) {
            throw new UserException('Job not found');
        }
        return $this->projects[$id];
    }

    public function updateProjectJob($id, $params)
    {
        if (!isset($this->projects[$id])) {
            throw new UserException('Job not found');
        }
        $this->projects[$id] = array_replace($this->projects[$id], $params);
    }

    public function createUser($login, $createdBy)
    {
        $jobId = rand(1, 255);
        $this->users[$jobId] = [
            'uid' => null,
            'login' => $login,
            'projectId' => null,
            'createdOn' => date('c'),
            'createdBy' => $createdBy,
            'deletedOn' => null,
            'deletedBy' => null,
            'status' => 'waiting',
            'error' => null
        ];
        return $jobId;
    }

    public function getUserJob($id)
    {
        if (!isset($this->users[$id])) {
            throw new UserException('Job not found');
        }
        return $this->users[$id];
    }

    public function updateUserJob($id, $params)
    {
        if (!isset($this->users[$id])) {
            throw new UserException('Job not found');
        }
        $this->users[$id] = array_replace($this->users[$id], $params);
    }
}
