<?php
include_once("templates/header.php");

// Exemplo: buscar alunos do banco
// $alunos = mysqli_query($conn, "SELECT id, nome, matricula, status FROM alunos");
$alunos = [
    ["id" => 1, "nome" => "João da Silva", "matricula" => "2023001", "status" => "Em andamento"],
    ["id" => 2, "nome" => "Maria Souza", "matricula" => "2023002", "status" => "Concluído"],
    ["id" => 3, "nome" => "Pedro Lima", "matricula" => "2023003", "status" => "Aguardando revisão"],
];
?>

<div class="container my-5">
    <div class="card shadow-lg">
        <div class="card-body">
            
            <!-- Busca -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="input-group w-50">
                    <input type="text" id="busca" class="form-control" placeholder="Buscar por nome ou matrícula...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <button class="btn btn-dark">Acessar</button>
            </div>

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
                        <?php foreach ($alunos as $aluno): ?>
                            <tr onclick="window.location.href='entregas.php?id=<?= $aluno['id'] ?>'">
                                <td><?= $aluno['nome'] ?></td>
                                <td><?= $aluno['matricula'] ?></td>
                                <td><?= $aluno['status'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
  const busca = document.getElementById("busca");
  busca.addEventListener("keyup", function() {
    let filtro = busca.value.toLowerCase();
    let linhas = document.querySelectorAll("#tabelaAlunos tr");
    linhas.forEach(linha => {
      let texto = linha.textContent.toLowerCase();
      linha.style.display = texto.includes(filtro) ? "" : "none";
    });
  });
</script>
