<?php

namespace App\Command;

use App\Service\ElasticSearch\ElasticSearchClientFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class HealthCheckCommand
 *
 * @package App\Command
 */
class HealthCheckCommand extends Command
{
    private const NOT_REACHABLE_ERROR = 'Services with NOK status are not reachable';
    private const STATUS_HEALTHY = 'OK';
    private const STATUS_UNHEALTHY = 'NOK';

    /**
     * @var ElasticSearchClientFactoryInterface
     */
    private $elasticSearchClientFactory;

    /**
     * @var string
     */
    private $avalonAdminHost;

    /**
     * HealthCheckCommand constructor.
     *
     * @param ElasticSearchClientFactoryInterface $elasticSearchClientFactory
     * @param string $avalonAdminHost
     * @throws LogicException
     */
    public function __construct(
        ElasticSearchClientFactoryInterface $elasticSearchClientFactory,
        string $avalonAdminHost
    ) {
        $this->elasticSearchClientFactory = $elasticSearchClientFactory;
        $this->avalonAdminHost = $avalonAdminHost;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('app:health-check')
            ->setDescription('Check that all dependencies are OK');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $healthy = true;
        $healthy = $this->handleCheckResponse($this->checkElasticSearch(), $healthy);
        $healthy = $this->handleCheckResponse($this->checkAvalonAdmin(), $healthy);

        if ($healthy === false) {
            throw new \RuntimeException(self::NOT_REACHABLE_ERROR);
        }
    }

    /**
     * Check ElasticSearch.
     *
     * @return bool
     */
    private function checkElasticSearch() : bool
    {
        $elasticSearchClient = $this->elasticSearchClientFactory->getClient();
        $isElasticSearchReachable = $elasticSearchClient->ping();
        $this->logStatus($isElasticSearchReachable, 'ElasticSearch');

        return $isElasticSearchReachable;
    }

    /**
     * Check Avalon access.
     *
     * @return bool
     */
    private function checkAvalonAdmin() : bool
    {
        try {
            $process = (new Process('curl -I ' . $this->avalonAdminHost))
                ->setTimeout(10);
            $process->start();
            $process->wait();
            $isAvalonAdminReachable = $process->getExitCode() === 0;
            $this->logStatus($isAvalonAdminReachable, 'Avalon Admin');

            return $isAvalonAdminReachable;
        } catch (\Throwable $e) {
            $this->logStatus(false, 'Avalon Admin check component');

            return false;
        }
    }

    /**
     * Handle check response.
     *
     * @param bool $isReachable
     * @param bool $healthStatus
     * @return bool
     */
    private function handleCheckResponse(bool $isReachable, bool $healthStatus) : bool
    {
        return $isReachable === false ? false : $healthStatus;
    }

    /**
     * Log check status.
     *
     * @param bool $checkResult
     * @param string $serviceName
     */
    private function logStatus(bool $checkResult, string $serviceName)
    {
        echo sprintf(
            '  [ %s ] ' . $serviceName  . PHP_EOL,
            $checkResult ? self::STATUS_HEALTHY : self::STATUS_UNHEALTHY
        );
    }
}
