<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Tests\Task;

use Keboola\GoodData\Exception;
use Keboola\GoodDataProvisioning\Task\RemoveUserFromProject;

class RemoveUserFromProjectTest extends AbstractTaskTest
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

        $jobId = $this->apiClient->removeUserFromProject();

        $this->gdClient->getProjects()->addUser($pid, $uid);
        $res = $this->gdClient->get("/gdc/projects/$pid/users/$uid");
        $this->assertArrayHasKey('user', $res);

        $task = new RemoveUserFromProject($this->gdClient, $this->apiClient, $this->imageParameters);
        $task->run($jobId, ['projectId' => $projectJobId, 'userId' => $userJobId]);

        $job = $this->apiClient->getJob($jobId);
        $this->assertEquals('success', $job['status']);
        try {
            $this->gdClient->get("/gdc/projects/$pid/users/$uid");
            $this->fail();
        } catch (Exception $e) {
            $this->assertStringStartsWith('Relation between', $e->getMessage());
        }

        $this->gdClient->getProjects()->deleteProject($pid);
        $this->gdClient->getUsers()->deleteUser($uid);
    }
}
