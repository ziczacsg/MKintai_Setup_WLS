<?php
declare(strict_types=1);

namespace MKintai\App\Controller;

use MKintai\App\Service\ProductProvider;
use Doctrine\ORM\EntityManagerInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Mvc\View\JsonView;

class ApiController extends ActionController
{
    protected $defaultViewObjectName = JsonView::class;
    protected $supportedMediaTypes = ['application/json'];

    #[Flow\Inject]
    protected ProductProvider $productProvider;

    #[Flow\Inject]
    protected EntityManagerInterface $entityManager;

    public function productsAction(): void
    {
        $this->view->assign('value', $this->productProvider->all());
    }

    public function healthAction(): void
    {
        $this->view->assign('value', [
            'flowContext' => getenv('FLOW_CONTEXT') ?: 'Development',
            'php' => [
                'version' => PHP_VERSION,
                'sapi' => PHP_SAPI,                        // mod_php → "apache2handler"
                'xdebug' => extension_loaded('xdebug'),
            ],
            'https' => $this->request->getHttpRequest()->getUri()->getScheme() === 'https',
            'mysql' => $this->checkMysql(),
            'redis' => $this->checkRedis(),
        ]);
    }

    private function checkMysql(): array
    {
        try {
            $version = $this->entityManager->getConnection()->fetchOne('SELECT VERSION()');
            return ['ok' => true, 'version' => $version];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function checkRedis(): array
    {
        if (!extension_loaded('redis')) {
            return ['ok' => false, 'error' => 'ext-redis chưa được nạp cho SAPI này'];
        }
        try {
            $redis = new \Redis();
            $redis->connect('127.0.0.1', 6379, 1.0);
            $redis->ping();
            $info = $redis->info('server');
            return ['ok' => true, 'version' => $info['redis_version'] ?? null];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
