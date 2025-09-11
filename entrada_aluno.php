<?php
    include_once ("templates/header.php");

    if (!isset($_SESSION["usuario_id"])) {
        header("Location: index.php");
        exit;
    }

    if ($_SESSION['usuario_tipo'] !== 'aluno') {
      header("Location: acesso_negado.php");
      exit;
    }
?>

<div class="row row-cols-1 row-cols-md-3 g-4">
  <div class="col">
    <div class="card">
      <a href="entregas_aluno.php" class="card h-100">
        <img src="./img/entragas_img.jpg" class="card-img-top" alt="Entregas">
        <div class="card-body">
          <h5 class="card-title">Entregas</h5>
          <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
        </div>
      </a>
    </div>
  </div>
  <div class="col">
    <div class="card">
      <a href="historico_reunioes.php">
      <img src="./img/reunioes_img.jpg" class="card-img-top" alt="Reuniões">
      <div class="card-body">
        <h5 class="card-title">Reuniões</h5>
        <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
      </div>
      </a>
    </div>
  </div>
  <div class="col">
    <div class="card">
      <img src="./img/notificacoes_img.jpg" class="card-img-top" alt="Notificações">
      <div class="card-body">
        <h5 class="card-title">Notificações</h5>
        <p class="card-text">This is a longer card with supporting text below as a natural lead-in to additional content.</p>
      </div>
    </div>
  </div>

</div>