<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Tests\Task;

use Keboola\GoodData\Client;

abstract class AbstractTaskTest extends \PHPUnit\Framework\TestCase
{
    /** @var  \Keboola\GoodData\Client */
    protected $gdClient;
    /** @var \Doctrine\DBAL\Connection  */
    protected $db;
    protected $imageParameters = [
        "gd" => [
            "username" => TEST_GD_LOGIN,
            "#password" => TEST_GD_PASSWORD,
            "backendUrl" => TEST_GD_BACKEND,
            "domain" => TEST_GD_DOMAIN,
            "ssoProvider" => TEST_GD_SSO_PROVIDER
        ],
        "db" => [
            "host" => TEST_DB_HOST,
            "name" => TEST_DB_NAME,
            "user" => TEST_DB_USER,
            "#password" => TEST_DB_PASSWORD
        ]
    ];

    public function setUp()
    {
        $this->gdClient = new Client(TEST_GD_BACKEND);
        $this->gdClient->login(TEST_GD_LOGIN, TEST_GD_PASSWORD);

        $this->db = \Doctrine\DBAL\DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => TEST_DB_HOST,
            'dbname' => TEST_DB_NAME,
            'user' => TEST_DB_USER,
            'password' => TEST_DB_PASSWORD,
            'port' => TEST_DB_PORT
        ]);

        $stmt = $this->db->prepare(file_get_contents(__DIR__ . '/../db.sql'));
        $stmt->execute();
        $stmt->closeCursor();

        parent::setUp();
    }
}
