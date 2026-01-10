<?php
$pageTitle = 'Checkout - Seu Conto';
$additionalCSS = [];
$additionalJS = ['/refactor/assets/js/checkout.js'];

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div id="checkout-page" style="padding: 3rem 0; background: var(--color-muted);">
    <div class="container" style="max-width: 900px;">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h1 style="margin-bottom: 1rem;">Revise seu Pedido</h1>
            <p class="text-muted">Confira os dados antes de prosseguir para o pagamento</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
            <!-- Resumo do Pedido -->
            <div class="card">
                <h3 style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border);">Dados do Livro</h3>
                <div style="display: grid; gap: 1rem;">
                    <div><span class="text-muted">Criança:</span> <strong id="summary-child-name">-</strong>, <span id="summary-child-age">-</span></div>
                    <div><span class="text-muted">Tema:</span> <strong id="summary-theme">-</strong></div>
                    <div id="theme-preview"></div>
                </div>
            </div>

            <!-- Preços -->
            <div class="card">
                <h3 style="margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border);">Resumo do Pedido</h3>
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span>E-book Personalizado</span>
                    <strong id="ebook-price">R$ 29,90</strong>
                </div>
                <div id="coloring-row" style="display: none; justify-content: space-between; margin-bottom: 1rem;">
                    <span>Livro de Colorir</span>
                    <strong id="coloring-price">R$ 9,90</strong>
                </div>
                <div style="padding: 1rem 0; margin: 1rem 0; border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
                    <label class="checkbox-group" style="cursor: pointer;">
                        <input type="checkbox" id="includeColoring" style="margin-right: 0.75rem;">
                        <span>Adicionar Livro de Colorir (+R$ 9,90)</span>
                    </label>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 1.25rem; font-weight: 700; margin-top: 1.5rem;">
                    <span>Total:</span>
                    <strong id="total-price" style="color: var(--color-primary);">R$ 29,90</strong>
                </div>

                <div class="badge badge-warning" style="margin-top: 1.5rem; width: 100%; justify-content: center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line></svg>
                    Cópia Física: EM BREVE!
                </div>
            </div>

            <!-- Ações -->
            <div style="display: flex; gap: 1rem;">
                <button id="back-to-edit" class="btn btn-outline btn-full">← Voltar</button>
                <button id="checkout-button" class="btn btn-secondary btn-full btn-lg">Ir para Pagamento →</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
