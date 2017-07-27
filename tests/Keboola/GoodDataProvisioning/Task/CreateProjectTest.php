<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Tests\Task;

use Keboola\GoodDataProvisioning\Task\CreateProject;

class CreateProjectTest extends AbstractTaskTest
{
    public function testRun()
    {
        $projectName = '[gdpr] ' . uniqid();
        $jobId = $this->apiClient->createProject(1, TEST_GD_AUTH_TOKEN, 'me');

        $task = new CreateProject($this->gdClient, $this->apiClient, $this->imageParameters);
        $task->run($jobId, ['name' => $projectName, 'authToken' => TEST_GD_AUTH_TOKEN]);

        $job = $this->apiClient->getProjectJob($jobId);
        $this->assertNotEmpty($job['pid']);
        $this->assertEquals('ready', $job['status']);

        $result = $this->gdClient->get("/gdc/projects/{$job['pid']}");
        $this->assertArrayHasKey('project', $result);
        $this->assertArrayHasKey('meta', $result['project']);
        $this->assertArrayHasKey('title', $result['project']['meta']);
        $this->assertEquals($projectName, $result['project']['meta']['title']);

        $this->gdClient->getProjects()->deleteProject($job['pid']);
    }
}
