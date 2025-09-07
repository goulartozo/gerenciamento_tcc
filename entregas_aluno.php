<?php
include_once("templates/header.php");
session_start();
?>

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
      <form method="post" action="upload.php" enctype="multipart/form-data">
        <td>25/08</td>
        <td>Proposta</td>
        <td>—</td>
        <td>
          <input type="hidden" name="acao" value="enviar">
          <input type="hidden" name="id" value="1">
          <input type="file" name="arquivo" required>
          <button type="submit" class="btn btn-primary">ENVIAR</button>
        </td>
      </form>
    </tr>
    <tr>
      <form method="post" action="upload.php" enctype="multipart/form-data">
        <td>09/09</td>
        <td>Relaboração Proposta</td>
        <td>—</td> 
        <td>
          <input type="hidden" name="acao" value="enviar">
          <input type="hidden" name="id" value="2">
          <input type="file" name="arquivo" required>
          <button type="submit" class="btn btn-primary">ENVIAR</button>
        </td>
      </form>
    </tr>
    <tr>
      <form method="post" action="upload.php" enctype="multipart/form-data">
        <td>20/11</td>
        <td>TC</td>
        <td>—</td> 
        <td>
          <input type="hidden" name="acao" value="enviar">
          <input type="hidden" name="id" value="3">
          <input type="file" name="arquivo" required>
          <button type="submit" class="btn btn-primary">ENVIAR</button>
        </td>
      </form>
    </tr>
    <tr>
      <form method="post" action="upload.php" enctype="multipart/form-data">
        <td>05/12</td>
        <td>Reelaboração TC</td>
        <td>—</td> 
        <td>
          <input type="hidden" name="acao" value="enviar">
          <input type="hidden" name="id" value="4">
          <input type="file" name="arquivo" required>
          <button type="submit" class="btn btn-primary">ENVIAR</button>
        </td>
      </form>
    </tr>
  </tbody>
</table>
