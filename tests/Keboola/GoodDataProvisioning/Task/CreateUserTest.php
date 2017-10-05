<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Tests\Task;

use Keboola\GoodDataProvisioning\Task\CreateUser;

class CreateUserTest extends AbstractTaskTest
{
    public function testRun()
    {
        $login = uniqid() . '@gdpr.test.keboola.com';
        $jobId = $this->apiClient->createUser($login, 'me');

        $task = new CreateUser($this->gdClient, $this->apiClient, $this->imageParameters);
        $task->run($jobId, ['login' => $login, 'password' => '1234567x', 'firstName' => 'Test', 'lastName' => 'GD']);

        $user = $this->apiClient->getUser($jobId);
        $this->assertNotEmpty($user['uid']);

        $job = $this->apiClient->getJob($jobId);
        $this->assertEquals('success', $job['status']);

        $result = $this->gdClient->get("/gdc/account/profile/{$user['uid']}");
        $this->assertArrayHasKey('accountSetting', $result);
        $this->assertArrayHasKey('login', $result['accountSetting']);
        $this->assertEquals($login, $result['accountSetting']['login']);

        $this->gdClient->getUsers()->deleteUser($user['uid']);
    }
}
