<?php

namespace app\Conn;

use app\Conn\Conn;
use \PDO;

class Update extends Conn {

    private $Tabela;
    private $Dados;
    private $Termos;
    private $PdoValue;
    private $Result;
    /** @var PDOStatement */
    private $Update;

    /** @var PDO */
    private $Conn;

    /**
     *
     * @param STRING $Tabela = Nome da tabela
     * @param ARRAY $Dados = [ NomeDaColuna ] => Valor ( Atribuição )
     * @param STRING $Termos = WHERE coluna = :link AND.. OR..
     * @param STRING $ParseString = link={$link}&link2={$link2}
     */
    public function updateDb($Tabela, array $Dados, $Termos, $ParseString) {
        $this->Tabela = (string) $Tabela;
        $this->Dados = $Dados;
        $this->Termos = (string) $Termos;
        parse_str($ParseString, $this->PdoValue);
        $this->getSyntax();
        $this->Execute();
    }

    /**
     *
     * @return BOOL $Var = True ou False
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     *
     * @return INT $Var = Quantidade de linhas alteradas
     */
    public function getRowCount() {
        return $this->Update->rowCount();
    }

    /**
     *
     * @param STRING $ParseString = id={$id}&..
     */
    public function setPdoValue($ParseString) {
        parse_str($ParseString, $this->PdoValue);
        $this->getSyntax();
        $this->Execute();
    }

    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Update = $this->Conn->prepare($this->Update);
    }

    private function getSyntax() {
        foreach ($this->Dados as $Key => $Value):
            $PdoValue[] = $Key . ' = :' . $Key;
        endforeach;

        $PdoValue = implode(', ', $PdoValue);
        $this->Update = "UPDATE {$this->Tabela} SET {$PdoValue} {$this->Termos}";
    }

    private function Execute() {
        $this->Connect();
        try {
            $this->Update->execute(array_merge($this->Dados, $this->PdoValue));
            $this->Result = true;
        } catch (PDOException $e) {
            $this->Result = null;
            ErroJMX("<b>Erro ao Ler:</b> {$e->getMessage()}", $e->getCode());
        }
    }
}
