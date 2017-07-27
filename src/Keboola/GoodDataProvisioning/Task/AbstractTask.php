<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Task;

use Keboola\GoodDataProvisioning\ApiClient;

abstract class AbstractTask
{
    /** @var  \Keboola\GoodData\Client */
    protected $gdClient;
    /** @var  ApiClient */
    protected $apiClient;
    protected $imageParameters;

    public function __construct(
        \Keboola\GoodData\Client $gdClient,
        ApiClient $apiClient,
        array $imageParameters
    ) {
        $this->gdClient = $gdClient;
        $this->apiClient = $apiClient;
        $this->imageParameters = $imageParameters;
    }

    abstract public function run($jobId, $params);
}
