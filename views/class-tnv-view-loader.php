<?php
/**
 * Базовый класс для загрузки представлений
 */
class TNV_View_Loader {
    private $views_dir;
    private $template_data = array();

    public function __construct() {
        $this->views_dir = TNV_PLUGIN_DIR . 'views/';
    }

    /**
     * Установка данных для шаблона
     */
    public function set_template_data($data) {
        $this->template_data = array_merge($this->template_data, $data);
    }

    /**
     * Рендеринг представления
     */
    public function render($view_name, $args = array()) {
        $view_file = $this->views_dir . $view_name . '.php';
        
        if (!file_exists($view_file)) {
            throw new Exception("View {$view_name} not found");
        }

        // Объединяем переданные аргументы с общими данными шаблона
        $data = array_merge($this->template_data, $args);
        
        // Делаем переменные доступными в представлении
        extract($data);
        
        ob_start();
        include $view_file;
        return ob_get_clean();
    }

    /**
     * Безопасный вывод переменной
     */
    public function escape($value) {
        return esc_html($value);
    }
}