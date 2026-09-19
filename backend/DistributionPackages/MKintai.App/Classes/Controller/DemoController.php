<?php
declare(strict_types=1);

namespace MKintai\App\Controller;

use MKintai\App\Service\ProductProvider;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;

class DemoController extends ActionController
{
    #[Flow\Inject]
    protected ProductProvider $productProvider;

    public function indexAction(): void
    {
        $this->view->assign('propsJson', json_encode([
            'initialProducts' => $this->productProvider->all(),
            'apiUrl' => '/api/products',
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }
}
