<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Tests;

use Keboola\StorageApi\Client;

class StorageClient extends Client
{
    public function __construct(array $config = array())
    {
    }

    public function verifyToken()
    {
        return [
          'owner' => [
              'type' => 'production',
              'limits' => [
                  'goodData.projectsCount' => 2
              ]
          ]
        ];
    }
}
