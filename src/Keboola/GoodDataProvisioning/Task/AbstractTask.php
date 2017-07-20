<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Task;

abstract class AbstractTask
{
    /** @var  \Keboola\GoodData\Client */
    protected $gdClient;
    /** @var \Doctrine\DBAL\Connection  */
    protected $db;
    protected $imageParameters;

    public function __construct(
        \Keboola\GoodData\Client $gdClient,
        \Doctrine\DBAL\Connection $db,
        array $imageParameters
    ) {
        $this->gdClient = $gdClient;
        $this->db = $db;
        $this->imageParameters = $imageParameters;
    }

    abstract public function run($jobId, $params);
}
