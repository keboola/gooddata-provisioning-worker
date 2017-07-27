<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Tests\Task;

use Keboola\GoodData\Client;
use Keboola\GoodDataProvisioning\Tests\ApiClient;

abstract class AbstractTaskTest extends \PHPUnit\Framework\TestCase
{
    /** @var  \Keboola\GoodData\Client */
    protected $gdClient;
    /** @var  ApiClient */
    protected $apiClient;
    protected $imageParameters = [
        "gd" => [
            "username" => TEST_GD_LOGIN,
            "#password" => TEST_GD_PASSWORD,
            "backendUrl" => TEST_GD_BACKEND,
            "domain" => TEST_GD_DOMAIN,
            "ssoProvider" => TEST_GD_SSO_PROVIDER
        ]
    ];

    public function setUp()
    {
        $this->gdClient = new Client(TEST_GD_BACKEND);
        $this->gdClient->login(TEST_GD_LOGIN, TEST_GD_PASSWORD);

        $this->apiClient = new ApiClient();

        parent::setUp();
    }
}
