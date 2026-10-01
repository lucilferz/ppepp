<?php
/**
 * Controller: Setting (Legacy — Redirects to UserController)
 * Platform PPEPP Fakultas
 */

class SettingController extends Controller
{
    /**
     * Redirect pengaturan lama ke halaman API Key baru
     */
    public function index(): void
    {
        $this->requireAuth();
        $this->redirect('/users/api-key');
    }

    /**
     * Legacy update endpoint (redirect ke UserController)
     */
    public function update(): void
    {
        $this->requireAuth();
        $userSession = $this->currentUser();

        $apiKey = $this->post('gemini_api_key', '');
        $model  = $this->post('gemini_model', DEFAULT_GEMINI_MODEL);

        if (!array_key_exists($model, AVAILABLE_GEMINI_MODELS)) {
            $model = DEFAULT_GEMINI_MODEL;
        }

        $userModel = new User();
        $userModel->updateApiKey($userSession['id'], $apiKey, $model);

        // Update session
        $_SESSION['user']['gemini_api_key'] = trim($apiKey);
        $_SESSION['user']['gemini_model']   = trim($model);

        $this->flash('success', 'API Key Gemini berhasil disimpan!');
        $this->redirect('/users/api-key');
    }
}
