<?php
include_once("templates/header.php");

// Exemplo de alunos no banco (simulação)
$alunos = [
    ["id" => 1, "nome" => "Maria Silva", "email" => "maria@email.com"],
    ["id" => 2, "nome" => "João Souza", "email" => "joao@email.com"],
    ["id" => 3, "nome" => "Ana Costa", "email" => "ana@email.com"],
];
?>

<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-body">
            <h4 class="mb-4 text-center">Professores e Alunos Cadastrados</h4>

            <!-- Barra de busca -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form method="get" action="">
                        <div class="input-group">
                            <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou e-mail">
                            <button class="btn btn-dark" type="submit">
                                🔍
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Botões de ação -->
                <div class="col-md-6 text-end">
                    <a href="cadastrar.php" class="btn btn-success">Cadastrar</a>
                    <button class="btn btn-primary" id="editarBtn" disabled>Editar</button>
                    <button class="btn btn-danger" id="excluirBtn" disabled>Excluir</button>
                </div>
            </div>

            <!-- Tabela -->
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Selecionar</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alunos as $aluno): ?>
                            <tr>
                                <td>
                                    <input type="radio" name="selectAluno" value="<?= $aluno['id'] ?>">
                                </td>
                                <td><?= $aluno['nome'] ?></td>
                                <td><?= $aluno['email'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
// Ativar botões somente quando um aluno for selecionado
const radios = document.querySelectorAll("input[name='selectAluno']");
const editarBtn = document.getElementById("editarBtn");
const excluirBtn = document.getElementById("excluirBtn");

radios.forEach(radio => {
    radio.addEventListener("change", () => {
        editarBtn.disabled = false;
        excluirBtn.disabled = false;
    });
});
</script>
