<?php
include_once("templates/header.php");


?>

<div>
    <div class="container my-5">
        <div class="card shadow-lg col-md-6 mx-auto">
            <div class="card-body">
                <h3 class="text-center mb-4">Cadastro</h3>
                
                <form action="config/process.php" method="post" id="cadastroForm">
                <input type="hidden" name="acao" value="cadastro_formulario_aluno_professor">

                <!-- Escolha -->
                <div class="mb-3 text-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo" id="alunoRadio" value="aluno" checked>
                        <label class="form-check-label" for="alunoRadio">Aluno</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipo" id="professorRadio" value="professor">
                        <label class="form-check-label" for="professorRadio">Professor</label>
                    </div>
                </div>

                <!-- Formulário -->
                
                    <!-- Campos do Aluno -->
                    <div id="formAluno">
                        <div class="mb-3">
                            <label class="form-label">Nome do Aluno:</label>
                            <input type="text" name="nome_aluno" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Matrícula:</label>
                            <input type="text" name="matricula" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail:</label>
                            <input type="email" name="email_aluno" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Curso:</label>
                            <input type="text" name="curso_aluno" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Orientador:</label>
                            <input type="text" name="orientador" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Banca 1:</label>
                            <input type="text" name="banca1" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Banca 2:</label>
                            <input type="text" name="banca2" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha:</label>
                            <input type="password" name="senha_aluno" class="form-control">
                        </div>
                    </div>

                    <!-- Campos do Professor -->
                    <div id="formProfessor" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Nome do Professor:</label>
                            <input type="text" name="nome_professor" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail:</label>
                            <input type="email" name="email_professor" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Curso:</label>
                            <input type="text" name="curso_professor" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha:</label>
                            <input type="password" name="senha_professor" class="form-control">
                        </div>
                    </div>

                    <!-- Botão -->
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-dark">Cadastrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
  const alunoRadio = document.getElementById("alunoRadio");
  const professorRadio = document.getElementById("professorRadio");
  const formAluno = document.getElementById("formAluno");
  const formProfessor = document.getElementById("formProfessor");

  function alternarFormulario() {
    if (alunoRadio.checked) {
      formAluno.style.display = "block";
      formProfessor.style.display = "none";
    } else if (professorRadio.checked) {
      formAluno.style.display = "none";
      formProfessor.style.display = "block";
    }
  }

  // dispara quando muda
  alunoRadio.addEventListener("change", alternarFormulario);
  professorRadio.addEventListener("change", alternarFormulario);

  // força ajuste ao carregar a página
  alternarFormulario();
</script>