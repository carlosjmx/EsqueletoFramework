
<?php 

namespace app\Models;

use app\interfaces\CarrinhoInterface;

class Carrinho implements CarrinhoInterface{

    public function adicionarNoCarrinho($id){}
    public function removerDoCarrinho($id){}
    public function pegarProdutosCarrinho(){}
    public function totaldoPedido(){ }
    public function limparCarrinho(){}
}



