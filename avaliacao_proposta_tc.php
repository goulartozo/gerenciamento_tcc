<?php
include_once("templates/header.php");

$alunoId = $_GET['id'] ?? null;
$tarefaId = $_GET['tarefaId'] ?? null;

if (!$alunoId) {
    $alunoId = $data['aluno_id'] ?? null;
}

if (!$alunoId) {
    echo "<div class='alert alert-danger'>Aluno não selecionado para avaliação.</div>";
    exit;
}
?>

<div class="bg-light">
    <div class="container my-5">
        <div class="card shadow-lg">
            <div class="card-body">
                <h4 class="text-center mb-4">Avaliação da Proposta de TC</h4>

                <form action="config/process.php" method="post">
                    <input type="hidden" name="acao" value="proposta_ava">
                    <input type="hidden" name="aluno_id" value="<?= $alunoId ?>">
                    <input type="hidden" name="tarefa_id" value="<?= $tarefaId ?>">

                    <!-- Introdução -->
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <span data-bs-toggle="tooltip" title="Justificativa da escolha, relevância do tema e definição clara do problema.">
                                <input type="text" class="form-control text-center" value="2.1 Introdução" readonly>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control text-center bg-light" value="0-2" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="introducao" class="form-control text-center" min="0" max="2">
                        </div>
                    </div>

                    <!-- Definição dos Objetivos -->
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <span data-bs-toggle="tooltip" title="Adequação dos objetivos frente ao problema proposto.">
                                <input type="text" class="form-control text-center" value="2.2 Definição dos Objetivos" readonly>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control text-center bg-light" value="0-1" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="objetivos" class="form-control text-center" min="0" max="1">
                        </div>
                    </div>

                    <!-- Revisão Bibliográfica -->
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <span data-bs-toggle="tooltip"
                                title="Fundamentação do tema com fontes, citações e normas da UNISC. Redação com clareza, terminologia técnica, conceitos científicos, ortografia e concordância.">
                                <input type="text" class="form-control text-center" value="2.3 Revisão Bibliográfica (Parte 1)" readonly>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control text-center bg-light" value="0-2" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="rev_biblio1" class="form-control text-center" min="0" max="2">
                        </div>
                    </div>

                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <span data-bs-toggle="tooltip"
                                title="Abordagem lógica, equilibrada e ordenada. Revisão com abrangência sobre o problema investigativo.">
                                <input type="text" class="form-control text-center" value="2.3 Revisão Bibliográfica (Parte 2)" readonly>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control text-center bg-light" value="0-1" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="rev_biblio2" class="form-control text-center" min="0" max="1">
                        </div>
                    </div>

                    <!-- Orientação Metodológica -->
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <span data-bs-toggle="tooltip"
                                title="Procedimentos adequados e bem definidos.">
                                <input type="text" class="form-control text-center" value="2.4 Orientação Metodológica (Parte 1)" readonly>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control text-center bg-light" value="0-2" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="orientacao1" class="form-control text-center" min="0" max="2">
                        </div>
                    </div>

                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <span data-bs-toggle="tooltip"
                                title="Coerência dos objetivos, metodologia e tipo de instrumentos.">
                                <input type="text" class="form-control text-center" value="2.4 Orientação Metodológica (Parte 2)" readonly>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control text-center bg-light" value="0-2" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="orientacao2" class="form-control text-center" min="0" max="2">
                        </div>
                    </div>

                    <!-- Resumo das Notas -->
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <span data-bs-toggle="tooltip"
                                title="Total Geral da Avaliação do Aluno">
                                <input type="text" class="form-control text-center" value="Resumo das Notas" readonly>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control text-center bg-light" value="0-10" readonly>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="resumo" class="form-control text-center" min="0" max="10">
                        </div>
                    </div>

                    <!-- Botão -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-dark">Salvar Avaliação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Ativar todos os tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })
</script>