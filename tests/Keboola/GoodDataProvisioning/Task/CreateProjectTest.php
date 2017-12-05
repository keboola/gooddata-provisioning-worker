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
    public function testRunWithToken()
    {
        $projectName = '[gdpr] ' . uniqid();
        $jobId = $this->apiClient->createProject(1, TEST_GD_AUTH_TOKEN, 'me');

        $task = new CreateProject($this->gdClient, $this->apiClient, $this->imageParameters);
        $task->run($jobId, ['name' => $projectName, 'authToken' => TEST_GD_AUTH_TOKEN]);

        $project = $this->apiClient->getProject($jobId);
        $this->assertNotEmpty($project['pid']);

        $job = $this->apiClient->getJob($jobId);
        $this->assertEquals('success', $job['status']);

        $result = $this->gdClient->get("/gdc/projects/{$project['pid']}");
        $this->assertArrayHasKey('project', $result);
        $this->assertArrayHasKey('meta', $result['project']);
        $this->assertArrayHasKey('title', $result['project']['meta']);
        $this->assertEquals($projectName, $result['project']['meta']['title']);

        $this->gdClient->getProjects()->deleteProject($project['pid']);
    }

    public function testRunWithoutToken()
    {
        $projectName = '[gdpr] ' . uniqid();
        $jobId = $this->apiClient->createProject(2, null, 'me');

        $task = new CreateProject($this->gdClient, $this->apiClient, $this->imageParameters);
        $task->run($jobId, ['name' => $projectName], TEST_STORAGE_API_TOKEN);

        $project = $this->apiClient->getProject($jobId);
        $this->assertNotEmpty($project['pid']);

        $job = $this->apiClient->getJob($jobId);
        $this->assertEquals('success', $job['status']);

        $result = $this->gdClient->get("/gdc/projects/{$project['pid']}");
        $this->assertArrayHasKey('project', $result);
        $this->assertArrayHasKey('meta', $result['project']);
        $this->assertArrayHasKey('title', $result['project']['meta']);
        $this->assertEquals($projectName, $result['project']['meta']['title']);

        $this->gdClient->getProjects()->deleteProject($project['pid']);
    }
}
