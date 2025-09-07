<?php
include_once("templates/header.php");
session_start();
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
    <tr>
      <td>25/08</td>
      <td>Proposta</td>
      <td>—</td>
      <td>
        <form method="post" action="config/process.php" enctype="multipart/form-data">
          <input type="hidden" name="acao" value="upload">
          <input type="hidden" name="id" value="1">
          <input type="file" name="arquivo" required>
          <button type="submit" class="btn btn-primary">ENVIAR</button>
        </form>
      </td>
    </tr>
    <tr>

      <td>09/09</td>
      <td>Relaboração Proposta</td>
      <td>—</td>
      <td>
        <form method="post" action="config/process.php" enctype="multipart/form-data">
          <input type="hidden" name="acao" value="upload">
          <input type="hidden" name="id" value="2">
          <input type="file" name="arquivo" required>
          <button type="submit" class="btn btn-primary">ENVIAR</button>
        </form>
      </td>
    </tr>
    <tr>
      <td>20/11</td>
      <td>TC</td>
      <td>—</td>
      <td>
        <form method="post" action="config/process.php" enctype="multipart/form-data">
          <input type="hidden" name="acao" value="upload">
          <input type="hidden" name="id" value="3">
          <input type="file" name="arquivo" required>
          <button type="submit" class="btn btn-primary">ENVIAR</button>
        </form>
      </td>
    </tr>
    <tr>
      <td>05/12</td>
      <td>Reelaboração TC</td>
      <td>—</td>
      <td>
        <form method="post" action="config/process.php" enctype="multipart/form-data">
          <input type="hidden" name="acao" value="upload">
          <input type="hidden" name="id" value="4">
          <input type="file" name="arquivo" required>
          <button type="submit" class="btn btn-primary">ENVIAR</button>
        </form>
      </td>
    </tr>
  </tbody>
</table>