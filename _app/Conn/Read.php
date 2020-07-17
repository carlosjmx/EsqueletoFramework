<?php

namespace app\Conn;

use app\Conn\Conn;
use \PDO;

class Read extends Conn {

    private $Select;
    private $bindValue; 
    private $Result;
    /** @var PDOStatement */
    private $Read;

    /** @var PDO */
    private $Conn;

    /**
     *
     * @param STRING $Tabela = Nome da tabela
     * @param STRING $Query = WHERE | ORDER | LIMIT :limit | OFFSET :offset
     * @param STRING $ParseString = link={$link}&link2={$link2}
     */
    public function readDb($Tabela, $Query = null, $ParseString = null) {
        if (!empty($ParseString)):
            parse_str($ParseString, $this->bindValue);
        endif;

        $this->Select = "SELECT * FROM {$Tabela} {$Query}";
        $this->Execute();
    }

    /**
     *
     * @return ARRAY $this = Array ResultSet
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     *
     * @return INT $Var = Quantidade de registros encontrados
     */
    public function getRowCount() {
        return $this->Read->rowCount();
    }

    public function FullRead($Query, $ParseString = null) {
        $this->Select = (string) $Query;
        if (!empty($ParseString)):
            parse_str($ParseString, $this->bindValue);
        endif;
        $this->Execute();
    }

    /**
     *
     * @param STRING $Query = Query Select Syntax
     * @param STRING $ParseString = link={$link}&link2={$link2}
     */
    public function setbindValue($ParseString) {
        parse_str($ParseString, $this->bindValue);
        $this->Execute();
    }

    private function Connect() {
        $this->Conn = parent::getConn();
        $this->Read = $this->Conn->prepare($this->Select);
        $this->Read->setFetchMode(PDO::FETCH_ASSOC);
    }

    private function getQuery() {
        if ($this->bindValue):
            foreach ($this->bindValue as $Vinculo => $Valor):
                if ($Vinculo == 'limit' || $Vinculo == 'offset'):
                    $Valor = (int) $Valor;
                endif;
                $this->Read->bindValue(":{$Vinculo}", $Valor, ( is_int($Valor) ? PDO::PARAM_INT : PDO::PARAM_STR));
            endforeach;
        endif;
    }

    private function Execute() {
        $this->Connect();
        try {
            $this->getQuery();
            $this->Read->execute();
            $this->Result = $this->Read->fetchAll();
        } catch (PDOException $e) {
            $this->Result = null;
            ErroJMX("<b>Erro ao Ler:</b> {$e->getMessage()}", $e->getCode());
        }
    }
}
