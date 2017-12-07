<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Tests\Task;

use Keboola\GoodDataProvisioning\Task\CreateProject;
use Keboola\GoodDataProvisioning\Tests\StorageClient;
use Keboola\GoodDataProvisioning\UserException;

class CreateProjectTest extends AbstractTaskTest
{
    public function testRunWithToken()
    {
        $projectName = '[gdpr] ' . uniqid();
        $jobId = $this->apiClient->createProject(1, TEST_GD_AUTH_TOKEN, 'me');

        $task = new CreateProject($this->gdClient, $this->apiClient, $this->imageParameters);
        $task->run($jobId, ['name' => $projectName, 'authToken' => TEST_GD_AUTH_TOKEN], TEST_STORAGE_TOKEN);

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

    public function testCheckProjectsCountLimitExceeded()
    {
        $this->apiClient->projects = [
            ['authToken' => 'keboola_production'],
            ['authToken' => 'xx']
        ];

        $this->expectException(UserException::class);
        $task = new CreateProject($this->gdClient, $this->apiClient, $this->imageParameters);
        $task->checkProjectsCountLimit(
            'keboola_production',
            ['owner' => ['limits' => ['goodData.projectsCount' => 1]]]
        );
        $this->assertTrue(true);
    }

    public function testCheckProjectsCountLimitOk()
    {
        $this->apiClient->projects = [
            ['authToken' => 'keboola_production'],
            ['authToken' => 'xx']
        ];

        $task = new CreateProject($this->gdClient, $this->apiClient, $this->imageParameters);
        $task->checkProjectsCountLimit(
            'keboola_production',
            ['owner' => ['limits' => ['goodData.projectsCount' => 2]]]
        );
        $this->assertTrue(true);
    }

    public function testGetDefaultTokenSuccess()
    {
        $productionToken = uniqid();
        $this->apiClient->projects = [];
        $this->apiClient->tokens = [
            ['name' => 'keboola_demo', 'token' => TEST_GD_AUTH_TOKEN],
            ['name' => 'keboola_production', 'token' => $productionToken],
        ];
        $task = new CreateProject($this->gdClient, $this->apiClient, $this->imageParameters);
        $this->assertEquals($productionToken, $task->getDefaultToken(new StorageClient()));
    }

    public function testGetDefaultTokenExceedProjects()
    {
        $this->expectException(UserException::class);
        $this->apiClient->projects = [['authToken' => 'keboola_production'], ['authToken' => 'keboola_production']];
        $task = new CreateProject($this->gdClient, $this->apiClient, $this->imageParameters);
        $this->assertEquals(TEST_GD_AUTH_TOKEN, $task->getDefaultToken(new StorageClient()));
    }
}
