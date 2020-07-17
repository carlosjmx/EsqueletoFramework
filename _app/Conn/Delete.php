<?php

namespace app\Conn;

use app\Conn\Conn;
use \PDO;

class Delete extends Conn {

    private $Tabela;
    private $Termos;
    private $PdoValue;
    private $Result;
    /** @var PDOStatement */
    private $Delete;

    /** @var PDO */
    private $Conn;

    public function ExeDelete($Tabela, $Termos, $ParseString) {
        $this->Tabela = (string) $Tabela;
        $this->Termos = (string) $Termos;
        parse_str($ParseString, $this->PdoValue);
        $this->getSyntax();
        $this->Execute();
    }

    public function getResult() {
        return $this->Result;
    }

    public function getRowCount() {
        return $this->Delete->rowCount();
    }

    public function setPdoValue($ParseString) {
        parse_str($ParseString, $this->PdoValue);
        $this->getSyntax();
        $this->Execute();
    }

    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Delete = $this->Conn->prepare($this->Delete);
    }

    private function getSyntax() {
        $this->Delete = "DELETE FROM {$this->Tabela} {$this->Termos}";
    }

    private function Execute() {
        $this->Connect();
        try {
            $this->Delete->execute($this->PdoValue);
            $this->Result = true;
        } catch (PDOException $e) {
            $this->Result = null;
            ErroJMX("<b>Erro ao Deletar:</b> {$e->getMessage()}", $e->getCode());
        }
    }
}
