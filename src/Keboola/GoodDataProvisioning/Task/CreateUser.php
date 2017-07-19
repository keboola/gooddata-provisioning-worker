<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning\Task;

class CreateUser extends AbstractTask
{
    public function run($params)
    {
        $options = [
            'firstName' => $params['firstName'],
            'lastName' => $params['lastName'],
            'ssoProvider' => $this->imageParameters['gd']['ssoProvider']
        ];
        if (isset($params['email'])) {
            $options['email'] = strtolower($params['email']);
        }
        $userId = $this->gdClient->getUsers()->createUser(
            strtolower($params['login']),
            $params['password'],
            $this->imageParameters['gd']['domain'],
            $options
        );
        $this->db->insert('projects', [
            'uid' => $userId,
            'login' => strtolower($params['login']),
            'projectId' => getenv('KBC_PROJECTID'),
            'createdById' => getenv('KBC_TOKENID'),
            'createdByName' => getenv('KBC_TOKENDESC')
        ]);
    }
}
