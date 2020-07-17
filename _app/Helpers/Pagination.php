<?php

namespace app\Helpers;

use app\Conn\Read;

class Pagination {

    private $Page;
    private $Limit;
    private $Offset;

    private $Tabela;
    private $Query;
    private $PdoValue;

    private $Rows;
    private $Link;
    private $MaxLinks;
    private $First;
    private $Last;

    private $Paginator;

    /**
     * @param STRING $Link = Ex: index.php?pagina&page=
     * @param STRING $First = Texto do link (Primeira Página)
     * @param STRING $Last = Texto do link (Última Página)
     * @param STRING $MaxLinks = Quantidade de links (5)
     */

    function __construct($Link, $First = null, $Last = null, $MaxLinks = null) {
        $this->Link = (string) $Link;
        $this->First = ( (string) $First ? $First : 'Primeira' );
        $this->Last = ( (string) $Last ? $Last : 'última' );
        $this->MaxLinks = ( (int) $MaxLinks ? $MaxLinks : 5);
    }

    /**
     * @param INT $Page = Recupere a página na URL
     * @param INT $Limit = Defina o LIMIT da consulta
     */

    public function execute($Page, $Limit) {

        $this->Page = ( (int) $Page ? $Page : 1 );
        $this->Limit = (int) $Limit;
        $this->Offset = ($this->Page * $this->Limit) - $this->Limit;
    }

    /**
     * @return LOCATION = Retorna a página
     */
    public function ReturnPage() {
        if ($this->Page > 1):
            $nPage = $this->Page - 1;
            header("Location: {$this->Link}{$nPage}");
        endif;
    }

    /**
     * @return INT = Retorna a página atual
     */
    public function getPage() {
        return $this->Page;
    }

    /**
     * @return INT = Limite de resultados
     */
    public function getLimit() {
        return $this->Limit;
    }

    /**
     * @return INT = Offset de resultados
     */
    public function getOffset() {
        return $this->Offset;
    }

    /**
     * @param STRING $Tabela = Nome da tabela
     * @param STRING $Query = Condição da seleção caso tenha
     * @param STRING $ParseString = Prepared Statements
     */

    public function exePaginator($Tabela, $Query = null, $ParseString = null) {
        $this->Tabela = (string) $Tabela;
        $this->Query = (string) $Query;
        $this->PdoValue = (string) $ParseString;
        $this->getSyntax();
    }

    /**
     * @return HTML = Paginação de resultados
     */
    public function getPaginator() {
        return $this->Paginator;
    }

    private function getSyntax() {
        $read = new Read;
        $read->readDB($this->Tabela, $this->Query, $this->PdoValue);
        $this->Rows = $read->getRowCount();

        if ($this->Rows > $this->Limit):
            $Paginas = ceil($this->Rows / $this->Limit);
            $MaxLinks = $this->MaxLinks;
            $this->Paginator = "<ul class=\"paginator\">";
            $this->Paginator .= "<li><a title=\"{$this->First}\" href=\"{$this->Link}1\">{$this->First}</a></li>";

            for ($iPag = $this->Page - $MaxLinks; $iPag <= $this->Page - 1; $iPag ++):
                if ($iPag >= 1):
                    $this->Paginator .= "<li><a title=\"Página {$iPag}\" href=\"{$this->Link}{$iPag}\">{$iPag}</a></li>";
                endif;
            endfor;

            $this->Paginator .= "<li><span class=\"active\">{$this->Page}</span></li>";

            for ($dPag = $this->Page + 1; $dPag <= $this->Page + $MaxLinks; $dPag ++):
                if ($dPag <= $Paginas):
                    $this->Paginator .= "<li><a title=\"Página {$dPag}\" href=\"{$this->Link}{$dPag}\">{$dPag}</a></li>";
                endif;
            endfor;

            $this->Paginator .= "<li><a title=\"{$this->Last}\" href=\"{$this->Link}{$Paginas}\">{$this->Last}</a></li>";
            $this->Paginator .= "</ul>";
        endif;
    }
}
