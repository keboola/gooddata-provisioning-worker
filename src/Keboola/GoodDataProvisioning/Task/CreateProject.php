<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Task;

use Keboola\GoodData\Exception;
use Keboola\GoodDataProvisioning\UserException;

class CreateProject extends AbstractTask
{
    public function run($jobId, $params)
    {
        $job = $this->db->fetchAssoc('SELECT * FROM projects WHERE jobId=?', [$jobId]);
        if (!$job) {
            throw new UserException("Job $jobId not found, try again please");
        }
        try {
            $pid = $this->gdClient->getProjects()->createProject($params['name'], $params['authToken']);
        } catch (Exception $e) {
            if ($e->getCode() === 404) {
                $err = 'Project creation failed, check your authToken';
                $this->db->update(
                    'projects',
                    ['error' => $err, 'status' => 'error'],
                    ['jobId=?' => $jobId]
                );
                throw new UserException($err);
            }
            $this->db->update(
                'projects',
                ['error' => $e->getMessage(), 'status' => 'error'],
                ['jobId=?' => $jobId]
            );
            throw $e;
        }
        $this->db->update('projects', ['pid' => $pid, 'status' => 'ready'], ['jobId' => $jobId]);
    }
}
