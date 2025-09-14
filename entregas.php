<?php
include_once("templates/header.php");
include_once("config/dbconection.php");
include_once("config/process.php");
// Exemplo de alunos no banco (simulação)
$usuarios = getAlunosVinculadosAoProfessor($conn)
?>

<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-body">

            <!-- Tabela -->
            <div class="table-responsive">
                <table class="table table-striped table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Nome</th>
                            <th>Matrícula</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaAlunos">
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr data-id="<?= $usuario['id'] ?>">
                                <td><?= $usuario['nome'] ?></td>
                                <td><?= $usuario['matricula'] ?></td>
                                <td><?= $usuario['status'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("#tabelaAlunos tr").forEach(row => {
        row.addEventListener("dblclick", () => {
            let alunoId = row.getAttribute("data-id");
            window.location.href = "entregas_aluno.php?id=" + alunoId;
        });
    });
});
</script>