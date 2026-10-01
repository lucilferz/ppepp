<?php
/**
 * View Renderer
 * Platform PPEPP Fakultas
 */

class View
{
    private string $viewsPath;

    public function __construct()
    {
        $this->viewsPath = BASE_PATH . '/app/views/';
    }

    /**
     * Render view dengan layout
     */
    public function render(string $viewPath, array $data = [], string $layout = 'main'): void
    {
        // Ekstrak data agar tersedia di view
        extract($data);

        // Capture konten view
        ob_start();
        $viewFile = $this->viewsPath . str_replace('.', '/', $viewPath) . '.php';
        if (!file_exists($viewFile)) {
            ob_end_clean();
            die("View '{$viewPath}' tidak ditemukan di {$viewFile}");
        }
        require $viewFile;
        $content = ob_get_clean();

        // Render layout
        $layoutFile = $this->viewsPath . 'layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            echo $content;
            return;
        }
        require $layoutFile;
    }

    /**
     * Render partial (tanpa layout)
     */
    public function partial(string $partialPath, array $data = []): string
    {
        extract($data);
        ob_start();
        $file = $this->viewsPath . str_replace('.', '/', $partialPath) . '.php';
        if (file_exists($file)) {
            require $file;
        }
        return ob_get_clean();
    }
}
