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
    public $projects = [];
    public $users = [];
    public $jobs = [];
    public $tokens = [
        ['name' => 'keboola_demo', 'token' => TEST_GD_AUTH_TOKEN],
        ['name' => 'keboola_production', 'token' => TEST_GD_AUTH_TOKEN],
    ];

    public function __construct()
    {
        parent::__construct(uniqid(), uniqid());
    }

    public function createProject($projectId, $authToken = null, $createdBy = null)
    {
        $jobId = rand(1, 255);
        $this->projects[$jobId] = [
            'id' => $jobId,
            'pid' => null,
            'projectId' => $projectId,
            'authToken' => $authToken,
            'createdOn' => date('c'),
            'createdBy' => $createdBy,
            'deletedOn' => null,
            'deletedBy' => null,
        ];
        $this->jobs[$jobId] = [
            'id' => $jobId,
            'projectId' => $projectId,
            'createdOn' => date('c'),
            'createdBy' => $createdBy,
            'status' => 'waiting',
            'error' => null
        ];
        return $jobId;
    }

    public function getProject($id)
    {
        if (!isset($this->projects[$id])) {
            throw new UserException('Project not found');
        }
        return $this->projects[$id];
    }

    public function listProjects()
    {
        return $this->projects;
    }

    public function updateProject($id, $params)
    {
        if (!isset($this->projects[$id])) {
            throw new UserException('Project not found');
        }
        $this->projects[$id] = array_replace($this->projects[$id], $params);
    }

    public function createUser($login, $createdBy)
    {
        $jobId = rand(1, 255);
        $this->users[$jobId] = [
            'id' => $jobId,
            'uid' => null,
            'login' => $login,
            'projectId' => 1,
            'createdOn' => date('c'),
            'createdBy' => $createdBy,
            'deletedOn' => null,
            'deletedBy' => null
        ];
        $this->jobs[$jobId] = [
            'id' => $jobId,
            'projectId' => 1,
            'createdOn' => date('c'),
            'createdBy' => $createdBy,
            'status' => 'waiting',
            'error' => null
        ];
        return $jobId;
    }

    public function getUser($id)
    {
        if (!isset($this->users[$id])) {
            throw new UserException('User not found');
        }
        return $this->users[$id];
    }

    public function updateUser($id, $params)
    {
        if (!isset($this->users[$id])) {
            throw new UserException('User not found');
        }
        $this->users[$id] = array_replace($this->users[$id], $params);
    }

    public function getJob($id)
    {
        if (!isset($this->jobs[$id])) {
            throw new UserException('Job not found');
        }
        return $this->jobs[$id];
    }

    public function updateJob($id, $params)
    {
        if (!isset($this->jobs[$id])) {
            throw new UserException('Job not found');
        }
        $this->jobs[$id] = array_replace($this->jobs[$id], $params);
    }

    public function addUserToProject()
    {
        $jobId = rand(1, 255);
        $this->jobs[$jobId] = [
            'id' => $jobId,
            'projectId' => 1,
            'createdOn' => date('c'),
            'createdBy' => 'me',
            'status' => 'waiting',
            'error' => null
        ];
        return $jobId;
    }

    public function removeUserFromProject()
    {
        $jobId = rand(1, 255);
        $this->jobs[$jobId] = [
            'id' => $jobId,
            'projectId' => 1,
            'createdOn' => date('c'),
            'createdBy' => 'me',
            'status' => 'waiting',
            'error' => null
        ];
        return $jobId;
    }

    public function listTokens()
    {
        return $this->tokens;
    }
}
