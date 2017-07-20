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
        $jobId = rand(1, 255);
        $projectName = '[gdpr] ' . uniqid();
        $this->db->insert('projects', ['jobId' => $jobId, 'projectId' => 1, 'createdById' => 1, 'createdByName' => 'me']);

        $task = new CreateProject($this->gdClient, $this->db, $this->imageParameters);
        $task->run($jobId, ['name' => $projectName, 'authToken' => TEST_GD_AUTH_TOKEN]);

        $job = $this->db->fetchAssoc('SELECT * FROM projects WHERE jobId=?', [$jobId]);
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
