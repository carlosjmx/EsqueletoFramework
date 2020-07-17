<?php 

namespace app\interfaces;

interface CarrinhoInterface{


    public function adicionarNoCarrinho($id);

    public function removerDoCarrinho($id);

    public function pegarProdutosCarrinho();

    public function totaldoPedido();

    public function limparCarrinho();








}



 ?>