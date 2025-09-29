let carrinho = [];

        document.querySelectorAll('.btn-adicionar').forEach(btn => {
            btn.addEventListener('click', function() {
                const produtoRow = this.closest('tr');
                const produto = JSON.parse(produtoRow.dataset.produto);
                
                if (produto.tipo === 'produto' && produto.estoque === 0) {
                    alert('Produto sem estoque!');
                    return;
                }

                const itemExistente = carrinho.find(item => item.id === produto.id);
                
                if (itemExistente) {
                    if (produto.tipo === 'produto' && itemExistente.quantidade >= produto.estoque) {
                        alert('Estoque insuficiente!');
                        return;
                    }
                    itemExistente.quantidade++;
                    itemExistente.subtotal = itemExistente.quantidade * itemExistente.preco;
                } else {

                    carrinho.push({
                        id: produto.id,
                        nome: produto.nome,
                        tipo: produto.tipo,
                        preco: parseFloat(produto.preco),
                        quantidade: 1,
                        subtotal: parseFloat(produto.preco)
                    });
                }

                atualizarCarrinho();
            });
        });

        function atualizarCarrinho() {
            const carrinhoVazio = document.getElementById('carrinho-vazio');
            const carrinhoItens = document.getElementById('carrinho-itens');
            const totalContainer = document.getElementById('total-container');
            const contadorCarrinho = document.getElementById('contador-carrinho');
            const totalVenda = document.getElementById('total-venda');
            const totalVendaInput = document.getElementById('total_venda');
            const itensCarrinhoInput = document.getElementById('itens_carrinho');

            if (carrinho.length === 0) {
                carrinhoVazio.style.display = 'block';
                carrinhoItens.style.display = 'none';
                totalContainer.style.display = 'none';
            } else {
                carrinhoVazio.style.display = 'none';
                carrinhoItens.style.display = 'block';
                totalContainer.style.display = 'block';

                
                carrinhoItens.innerHTML = '';
                let total = 0;

                carrinho.forEach((item, index) => {
                    total += item.subtotal;
                    
                    const itemHTML = `
                        <div class="carrinho-item mb-3 p-3 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">${item.nome}</h6>
                                    <small class="text-muted">${item.tipo === 'produto' ? 'Produto' : 'Serviço'}</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerItem(${index})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="input-group input-group-sm" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button" onclick="alterarQuantidade(${index}, -1)">-</button>
                                    <input type="number" class="form-control text-center" value="${item.quantidade}" min="1" onchange="atualizarQuantidade(${index}, this.value)">
                                    <button class="btn btn-outline-secondary" type="button" onclick="alterarQuantidade(${index}, 1)">+</button>
                                </div>
                                <strong>R$ ${item.subtotal.toFixed(2)}</strong>
                            </div>
                            <small class="text-muted">R$ ${item.preco.toFixed(2)} cada</small>
                        </div>
                    `;
                    carrinhoItens.innerHTML += itemHTML;
                });

                contadorCarrinho.textContent = carrinho.length;
                totalVenda.textContent = `R$ ${total.toFixed(2)}`;
                totalVendaInput.value = total.toFixed(2);
                itensCarrinhoInput.value = JSON.stringify(carrinho);
            }
        }

        function removerItem(index) {
            carrinho.splice(index, 1);
            atualizarCarrinho();
        }

        function alterarQuantidade(index, change) {
            const novoValor = carrinho[index].quantidade + change;
            if (novoValor >= 1) {

                if (carrinho[index].tipo === 'produto') {
                    const produtoRow = document.querySelector(`[data-produto*='"id":${carrinho[index].id}']`);
                    if (produtoRow) {
                        const produto = JSON.parse(produtoRow.dataset.produto);
                        if (novoValor > produto.estoque) {
                            alert('Estoque insuficiente!');
                            return;
                        }
                    }
                }
                
                carrinho[index].quantidade = novoValor;
                carrinho[index].subtotal = carrinho[index].quantidade * carrinho[index].preco;
                atualizarCarrinho();
            }
        }

        function atualizarQuantidade(index, novaQuantidade) {
            novaQuantidade = parseInt(novaQuantidade);
            if (novaQuantidade >= 1) {
                carrinho[index].quantidade = novaQuantidade;
                carrinho[index].subtotal = carrinho[index].quantidade * carrinho[index].preco;
                atualizarCarrinho();
            }
        }

        atualizarCarrinho();