<?php
include_once("templates/header.php");
include_once("config/dbconection.php");
include_once("config/process.php");

include 'templates/backButton.php';

if ($_SESSION['tipo'] === 'aluno') {
    $reunioes = getReunioesAluno($conn);
} else {
    $reunioes = getReunioesAluno($conn);
}


?>

<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h4 class="mb-4 text-center">Registro de Reuniões</h4>

            <!-- Formulário para adicionar reunião -->
            <form action="config/process.php" method="post" class="mb-4">
                <input type="hidden" name="acao" value="reuniao">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Data da Reunião</label>
                        <input type="date" name="data" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Assunto Discutido</label>
                        <input type="text" name="assunto" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rubrica Aluno</label>
                        <input type="text" name="aluno" class="form-control" placeholder="✔">
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-dark">Registrar</button>
                </div>
            </form>

            <!-- Tabela -->
            <div class="table-responsive">
                <table class="table table-striped table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Data da Reunião</th>
                            <th>Assunto Discutido</th>
                            <th>Rubrica do Prof.</th>
                            <th>Rubrica do Aluno</th>
                            <?php if ($_SESSION['tipo'] === 'professor'): ?>
                                <th>Aceitar Registro</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reunioes as $reuniao): ?>
                            <tr>
                                <td><?= date("d/m/Y", strtotime($reuniao["data"])) ?></td>
                                <td><?= $reuniao["assunto"] ?></td>
                                <td><?= $reuniao["ass_prof"] ?></td>
                                <td><?= $reuniao["ass_aluno"] ?></td>

                                <?php if ($_SESSION['tipo'] === 'professor'): ?>
                                    <td>
                                        <?php if ($reuniao['ass_prof'] === 'Pendente Confirmação'): ?>
                                            <form action="config/process.php" method="post" class="d-inline">
                                                <input type="hidden" name="acao" value="assinar_reuniao">
                                                <input type="hidden" name="id_reuniao" value="<?= $reuniao['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    Assinar
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-success">Assinado</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>