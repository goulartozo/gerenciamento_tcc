<?php
include_once("templates/header.php");
include_once("config/process.php");
include_once("config/dbconection.php");

$tipo = $_SESSION['tipo'] ?? null;

if (!$tipo) {
    header("Location: index.php");
    exit;
}

// 1. Capture o ID do aluno da URL
$alunoId = $_GET['id'] ?? null;
$tarefaId = $_GET['id'] ?? null;

// Se não houver ID, não continue
if (!$alunoId) {
    echo "<div class='alert alert-danger'>Erro: Aluno não especificado.</div>";
    exit;
}

// ... seu código para buscar as entregas do aluno ...
$entregasDoAluno = getTarefasEntregasAluno($conn, $alunoId, $tarefaId);
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

        $notaProposta = $entregasDoAluno[1]['nota'] ?? null;
        $notaRelabProposta = $entregasDoAluno[2]['nota'] ?? null;
        $notaTC = $entregasDoAluno[3]['nota'] ?? null;

        foreach ($tarefas as $id => $tarefa):
            $entregaAtual = $entregasDoAluno[$id] ?? null;
            $statusAtual = $entregaAtual['status'] ?? 'pendente';
            $notaAtual = $entregaAtual['nota'] ?? '';

            $habilitada = false;
            if ($id == 1) {
                // Proposta sempre habilitada
                $habilitada = true;
            } elseif ($id == 2) {
                // Relaboração Proposta: só se nota da Proposta < 7
                $habilitada = ($notaProposta !== null && $notaProposta < 7);
            } elseif ($id == 3) {
                // TC: só se nota da Relaboração Proposta >= 7
                $habilitada = ($notaProposta!== null && $notaProposta >= 7 || $notaRelabProposta >= 7);
            } elseif ($id == 4) {
                // Reelaboração TC: só se nota do TC < 7
                $habilitada = ($notaTC !== null && $notaTC < 7);
            }
        ?>
            <tr>
                <td><?= $tarefa['data'] ?></td>
                <td><?= $tarefa['nome'] ?></td>
                <td><?= $notaAtual ?></td>
                <td>
                    <!-- Formulário de envio para aluno -->
                    <?php if ($tipo === 'aluno'): ?>
                        <form method="post" action="config/process.php" enctype="multipart/form-data">
                            <input type="hidden" name="acao" value="upload">
                            <input type="hidden" name="id" value="<?= $id ?>">

                            <?php
                            $jaEnviado = ($statusAtual === 'enviado' || $statusAtual === 'avaliado');
                            ?>
                            <input type="file" name="arquivo" <?= ($habilitada && !$jaEnviado) ? 'required' : 'disabled' ?>>
                            <button type="submit" class="btn btn-primary" <?= ($habilitada && !$jaEnviado) ? '' : 'disabled' ?>>
                                <?= ($habilitada && !$jaEnviado) ? 'Enviar' : 'Já enviado' ?>
                            </button>
                            <?php if ($jaEnviado): ?>
                                <span class="text-success ms-2">Arquivo já enviado</span>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                    <!-- Botão de download para professor -->
                    <?php if ($tipo === 'professor' && !empty($entregaAtual['arquivo'])): ?>
                        <a href="arquivos/<?= htmlspecialchars($entregaAtual['arquivo']) ?>" class="btn btn-success" download>
                            Download
                        </a>
                        <a href="avaliacao_proposta_tc.php?id=<?= $alunoId ?>&tarefaId=<?= $id ?>"class="btn btn-primary">
                            Avaliar
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
            $tarefaAnteriorCompleta = ($statusAtual === 'enviado' || $statusAtual === 'avaliado');
            ?>
        <?php endforeach; ?>
    </tbody>
</table>