<?php

namespace app\Helpers;

class View {

    private $Data;
    private $Keys;
    private $Values;
    private $Template;

    /**
     * @param STRING $Template = Nome_do_arquivo
     */
    public function Load($Template) {
        $this->Template = REQUIRE_PATH . DIRECTORY_SEPARATOR . '_tpl' . DIRECTORY_SEPARATOR . (string) $Template;
        $this->Template = file_get_contents($this->Template . '.tpl.html');
        return $this->Template;
    }

    /**
     * @param array $Data = Array com dados obtidos
     * @param View $View = Template carregado pelo método Load()
     */
    public function Show(array $Data, $View) {
        $this->setKeys($Data);
        $this->setValues();
        $this->ShowView($View);
    }

    /**
     * @param STRING $File = Caminho / Nome_do_arquivo
     * @param ARRAY $Data = Array com dados obtidos
     */
    public function Request($File, array $Data) {
        extract($Data);
        require("{$File}.inc.php");
    }

    private function setKeys($Data) {
        $this->Data = $Data;
        $this->Data['HOME'] = HOME;
        
        $this->Keys = explode('&', '#' . implode("#&#", array_keys($this->Data)) . '#');
        $this->Keys[] = '#HOME#';
    }

    private function setValues() {
        $this->Values = array_values($this->Data);
    }

    private function ShowView($View) {
        $this->Template = $View;
        echo str_replace($this->Keys, $this->Values, $this->Template);
    }

}
