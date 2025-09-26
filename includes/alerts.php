<!-- Sistema de Alertas -->
<?php if (isset($_GET['sucesso'])): ?>
    <?php if ($_GET['sucesso'] == '1'): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <strong>✅ Sucesso!</strong> Agendamento atualizado com êxito.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif ($_GET['sucesso'] == 'cancelado'): ?>
        <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
            <strong>⚠️ Sucesso!</strong> Agendamento cancelado com êxito.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (isset($_GET['erro'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <strong>❌ Erro:
            <?php
            switch ($_GET['erro']) {
                case 'cancelamento':
                    echo 'Falha ao cancelar agendamento';
                    break;
                case 'id_nao_informado':
                    echo 'ID do agendamento não informado';
                    break;
                case 'banco_dados':
                    echo 'Erro no banco de dados';
                    break;
                default:
                    echo 'Erro desconhecido';
            }
            ?>
        </strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>