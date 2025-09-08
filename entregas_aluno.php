<?php
include_once("templates/header.php");
include_once("config/process.php");
include_once("config/dbconection.php");


$entregasDoAluno = getTarefasEntregasAluno($conn);
?>

<?php if (isset($_SESSION["msg"])): ?>
    <div class="alert alert-info">
        <?= $_SESSION["msg"] ?>
    </div>
    <?php unset($_SESSION["msg"]); ?>
<?php endif; ?>

<table class="table table-bordered align-middle text-center">
    <thead>
        <tr>
            <th>Data para envio</th>
            <th>Tarefa</th>
            <th>Média final</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $tarefas = [
            1 => ['nome' => 'Proposta', 'data' => '25/08'],
            2 => ['nome' => 'Relaboração Proposta', 'data' => '09/09'],
            3 => ['nome' => 'TC', 'data' => '20/11'],
            4 => ['nome' => 'Reelaboração TC', 'data' => '05/12']
        ];
        
        $tarefaAnteriorCompleta = true; 

        foreach ($tarefas as $id => $tarefa):
            $entregaAtual = $entregasDoAluno[$id] ?? null;
            $statusAtual = $entregaAtual['status'] ?? 'não_enviado';
            
            $notaAtual = ($statusAtual === 'avaliado') ? $entregaAtual['nota'] : '—';
            
            // Lógica de habilitação sequencial
            $tarefaHabilitada = ($id === 1 || $tarefaAnteriorCompleta);

        ?>
        <tr>
            <td><?= $tarefa['data'] ?></td>
            <td><?= $tarefa['nome'] ?></td>
            <td><?= $notaAtual ?></td>
            <td>
                <form method="post" action="config/process.php" enctype="multipart/form-data">
                    <input type="hidden" name="acao" value="upload">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    
                    <?php 
                    // Se a tarefa não estiver habilitada, ela está pendente de uma anterior
                    if (!$tarefaHabilitada): ?>
                        <span class="d-block mb-2"></span>
                        <input type="file" name="arquivo" disabled>
                        <button type="submit" class="btn btn-primary" disabled>PENDENTE</button>
                    
                    <?php 
                    // Se a tarefa estiver habilitada, pegamos as propriedades do status real
                    else:
                        $properties = getEnviosStatus($statusAtual);
                    ?>
                        <?php if ($statusAtual === 'enviado' || $statusAtual === 'avaliado'): ?>
                            <span class="d-block mb-2">Arquivo enviado.</span>
                        <?php endif; ?>

                        <?php if ($properties['disabled']): ?>
                            <input type="file" name="arquivo" disabled>
                            <button type="submit" class="btn btn-primary" disabled><?= $properties['button_text'] ?></button>
                        <?php else: ?>
                            <input type="file" name="arquivo" required>
                            <button type="submit" class="btn btn-primary"><?= $properties['button_text'] ?></button>
                        <?php endif; ?>
                    <?php endif; ?>
                </form>
            </td>
        </tr>
        <?php
            // Atualiza o estado da variável para a próxima iteração
            $tarefaAnteriorCompleta = ($statusAtual === 'enviado' || $statusAtual === 'avaliado');
        ?>
        <?php endforeach; ?>
    </tbody>
</table>