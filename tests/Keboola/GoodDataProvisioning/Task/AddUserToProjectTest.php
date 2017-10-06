<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Tests\Task;

use Keboola\GoodDataProvisioning\Task\AddUserToProject;

class AddUserToProjectTest extends AbstractTaskTest
{
    public function testRun()
    {
        $pid = $this->gdClient->getProjects()->createProject('test', TEST_GD_AUTH_TOKEN);
        $projectJobId = $this->apiClient->createProject($pid, TEST_GD_AUTH_TOKEN, 'me');
        $this->apiClient->updateProject($projectJobId, ['pid' => $pid]);

        $login = uniqid() . '@gdpr.test.keboola.com';
        $uid = $this->gdClient->getUsers()->createUser($login, md5(uniqid()), TEST_GD_DOMAIN, [
            'firstName' => 'test',
            'lastName' => 'test'
        ]);
        $userJobId = $this->apiClient->createUser($login, 'me');
        $this->apiClient->updateUser($userJobId, ['uid' => $uid]);

        $jobId = $this->apiClient->addUserToProject();

        $task = new AddUserToProject($this->gdClient, $this->apiClient, $this->imageParameters);
        $task->run($jobId, ['projectId' => $projectJobId, 'userId' => $userJobId, 'role' => 'admin']);

        $job = $this->apiClient->getJob($jobId);
        $this->assertEquals('success', $job['status']);
        $result = $this->gdClient->get("/gdc/projects/{$pid}/users");
        $userFound = false;
        foreach ($result['users'] as $user) {
            if ($user['user']['content']['login'] == $login) {
                $userFound = true;
                break;
            }
        }
        $this->assertTrue($userFound);

        $this->gdClient->getProjects()->deleteProject($pid);
        $this->gdClient->getUsers()->deleteUser($uid);
    }
}
