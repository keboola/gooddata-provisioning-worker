<?php
/**
 * @package gooddata-provisioning
 * @copyright Keboola
 * @author Jakub Matejka <jakub@keboola.com>
 */
namespace Keboola\GoodDataProvisioning;

use Symfony\Component\Console\Output\ConsoleOutput;

class App
{
    /** @var  ConsoleOutput */
    private $consoleOutput;

    public function __construct($consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
    }

    public function run($options)
    {
        //
    }
}
