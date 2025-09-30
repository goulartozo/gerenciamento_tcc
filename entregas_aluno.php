<?php
include_once("templates/header.php");
include_once("config/process.php");
include_once("config/dbconection.php");

include 'templates/backButton.php';

// Valida se o usuário está logado
$tipo = $_SESSION['tipo'] ?? null;
if (!$tipo) {
    header("Location: index.php");
    exit;
}

// --- Captura de Parâmetros da URL ---
$alunoId = $_GET['id'] ?? null;
$tarefaId = $_GET['tarefaId'] ?? null;

// Se nenhum aluno foi especificado, encerra a execução
if (!$alunoId) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Erro: Aluno não especificado.</div></div>";
    include_once("templates/footer.php");
    exit;
}

// --- Busca de Dados ---
$entregasDoAluno = getTarefasEntregasAluno($conn, $alunoId);
?>

<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-body">

            <?php if (isset($_SESSION["msg"])): ?>
                <div class="alert alert-info">
                    <?= $_SESSION["msg"] ?>
                </div>
                <?php unset($_SESSION["msg"]); ?>
            <?php endif; ?>

            <h4 class="mb-4 text-center">Entregas do Aluno</h4>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Data Limite</th>
                            <th>Tarefa</th>
                            <th>Média Final</th>
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

                        $notaProposta = $entregasDoAluno[1]['nota'] ?? null;
                        $notaRelabProposta = $entregasDoAluno[2]['nota'] ?? null;
                        $notaTC = $entregasDoAluno[3]['nota'] ?? null;

                        foreach ($tarefas as $id => $tarefa):
                            $entregaAtual = $entregasDoAluno[$id] ?? null;
                            $statusAtual = $entregaAtual['status'] ?? 'pendente';
                            $notaAtual = $entregaAtual['nota'] ?? '';

                            $habilitada = false;
                            if ($id == 1) {
                                $habilitada = true;
                            } elseif ($id == 2) {
                                $habilitada = ($notaProposta !== null && $notaProposta < 7);
                            } elseif ($id == 3) {
                                $habilitada = ($notaProposta !== null && $notaProposta >= 7 || $notaRelabProposta >= 7);
                            } elseif ($id == 4) {
                                $habilitada = ($notaTC !== null && $notaTC < 7);
                            }
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($tarefa['data']) ?></td>
                                <td><?= htmlspecialchars($tarefa['nome']) ?></td>
                                <td><?= htmlspecialchars($notaAtual) ?></td>
                                <td>
                                    <?php if ($tipo === 'aluno'): ?>
                                        <form method="post" action="config/process.php" enctype="multipart/form-data">
                                            <input type="hidden" name="acao" value="upload">
                                            <input type="hidden" name="id" value="<?= $id ?>">
                                            <?php $jaEnviado = (in_array($statusAtual, ['enviado', 'avaliado'])); ?>

                                            <div class="input-group">
                                                <input type="file" name="arquivo" class="form-control" <?= ($habilitada && !$jaEnviado) ? 'required' : 'disabled' ?>>
                                                <button type="submit" class="btn btn-dark" <?= ($habilitada && !$jaEnviado) ? '' : 'disabled' ?>>
                                                    <?= ($habilitada && !$jaEnviado) ? 'Enviar' : 'Enviado' ?>
                                                </button>
                                            </div>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($tipo === 'professor' && !empty($entregaAtual['arquivo'])): ?>
                                        <div class="d-flex justify-content-center mt-2">
                                            <a href="arquivos/<?= htmlspecialchars($entregaAtual['arquivo']) ?>" class="btn btn-success" download>Download</a>
                                            <a href="avaliacao_proposta_tc.php?id=<?= $alunoId ?>&tarefaId=<?= $id ?>" class="btn btn-primary ms-2">Avaliar</a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (strtolower(trim($statusAtual)) === 'avaliado'): ?>
                                        <a href="entregas_aluno.php?id=<?= $alunoId ?>&tarefaId=<?= $id ?>" class="btn btn-sm btn-secondary mt-2">
                                            Ver Notas Detalhadas
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($tarefaId): ?>
                <div class="mt-5">
                    <h4 class="mb-4 text-center">Notas dos Professores</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Professor</th>
                                    <th>Nota Individual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sqlNotas = "SELECT u.nome AS professor_nome, a.nota
                                         FROM avaliacoes_proposta a
                                         JOIN usuarios u ON u.id = a.professor_id
                                         WHERE a.aluno_id = :aluno_id AND a.tarefa_id = :tarefa_id";
                                $stmtNotas = $conn->prepare($sqlNotas);
                                $stmtNotas->bindParam(':aluno_id', $alunoId, PDO::PARAM_INT);
                                $stmtNotas->bindParam(':tarefa_id', $tarefaId, PDO::PARAM_INT);
                                $stmtNotas->execute();
                                $avaliacoes = $stmtNotas->fetchAll(PDO::FETCH_ASSOC);

                                if ($avaliacoes) {
                                    foreach ($avaliacoes as $av) {
                                        echo "<tr>
                                            <td>" . htmlspecialchars($av['professor_nome']) . "</td>
                                            <td>" . number_format($av['nota'], 2, ',', '.') . "</td>
                                          </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2'>Nenhuma nota individual registrada para esta tarefa.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div> <?php
        include_once("templates/header.php");
        ?>