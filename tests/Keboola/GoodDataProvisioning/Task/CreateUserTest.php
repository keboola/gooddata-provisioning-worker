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
        $jobId = rand(1, 255);
        $login = uniqid() . '@gdpr.test.keboola.com';
        $this->db->insert(
            'users',
            ['jobId' => $jobId, 'login' => $login, 'projectId' => 1, 'createdById' => 1, 'createdByName' => 'me']
        );

        $task = new CreateUser($this->gdClient, $this->db, $this->imageParameters);
        $task->run($jobId, ['login' => $login, 'password' => '1234567x', 'firstName' => 'Test', 'lastName' => 'GD']);

        $job = $this->db->fetchAssoc('SELECT * FROM users WHERE jobId=?', [$jobId]);
        $this->assertNotEmpty($job['uid']);
        $this->assertEquals('ready', $job['status']);

        $result = $this->gdClient->get("/gdc/account/profile/{$job['uid']}");
        $this->assertArrayHasKey('accountSetting', $result);
        $this->assertArrayHasKey('login', $result['accountSetting']);
        $this->assertEquals($login, $result['accountSetting']['login']);

        $this->gdClient->getUsers()->deleteUser($job['uid']);
    }
}
