<?php
declare(strict_types=1);

namespace MKintai\App\ViewHelpers;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\ResourceManager;
use Neos\FluidAdaptor\Core\ViewHelper\AbstractViewHelper;

/**
 * Xuất thẻ nạp frontend Vue:
 *  - devServer có giá trị → nạp module trực tiếp từ Vite dev server (hot-reload)
 *  - devServer rỗng       → nạp bản build trong Resources/Public/app (production)
 */
class ViteViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    #[Flow\InjectConfiguration(path: 'frontend', package: 'MKintai.App')]
    protected array $config = [];

    #[Flow\Inject]
    protected ResourceManager $resourceManager;

    public function render(): string
    {
        $devServer = rtrim((string)($this->config['devServer'] ?? ''), '/');

        if ($devServer !== '') {
            $server = htmlspecialchars($devServer, ENT_QUOTES);
            $entry = htmlspecialchars(ltrim((string)($this->config['entry'] ?? 'src/main.ts'), '/'), ENT_QUOTES);
            return '<script type="module" src="' . $server . '/@vite/client"></script>' . "\n"
                 . '<script type="module" src="' . $server . '/' . $entry . '"></script>';
        }

        $css = $this->publicUri((string)($this->config['builtCss'] ?? 'app/app.css'));
        $js = $this->publicUri((string)($this->config['builtJs'] ?? 'app/app.js'));
        return '<link rel="stylesheet" href="' . htmlspecialchars($css, ENT_QUOTES) . '">' . "\n"
             . '<script type="module" src="' . htmlspecialchars($js, ENT_QUOTES) . '"></script>';
    }

    private function publicUri(string $path): string
    {
        return $this->resourceManager->getPublicPackageResourceUriByPath(
            'resource://MKintai.App/Public/' . ltrim($path, '/')
        );
    }
}
