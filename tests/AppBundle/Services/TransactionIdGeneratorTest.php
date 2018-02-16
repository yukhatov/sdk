<?php
/**
 * Created by PhpStorm.
 * User: artur
 * Date: 22.06.17
 * Time: 15:48
 */
namespace Tests\AppBundle\Services;

require 'vendor/autoload.php';

use AppBundle\Entity\Reward;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransactionIdGeneratorTest extends WebTestCase
{
    protected $em;
    protected $transactionIdGenerator;

    protected function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->transactionIdGenerator = static::$kernel->getContainer()
            ->get('transaction.id.generator');
    }

    public function testGenerate()
    {
        $application = $this->em
            ->getRepository('AppBundle:Application')
            ->findFirst();

        $affSub = $this->transactionIdGenerator->generate($application, 'TEST_DEVICE', 12345, 150, 100);
        
        $this->assertTrue($affSub != '');
        $this->assertInstanceOf(Reward::class, $this->transactionIdGenerator->decodeToReward($affSub));
    }
}