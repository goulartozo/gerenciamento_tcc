<?php
    include_once("templates/header.php");
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
      <td>25/08</td>
      <td>Proposta</td>
      <td><input type="text" class="form-control"></td>
      <td><button class="btn btn-primary">ENVIAR</button></td>
    </tr>
    <tr>
      <td>09/09</td>
      <td>Reelaboração Proposta</td>
      <td><input type="text" class="form-control"></td>
      <td><button class="btn btn-success" disabled>ENVIADO</button></td>
    </tr>
    <tr>
      <td>20/11</td>
      <td>TC</td>
      <td><input type="text" class="form-control"></td>
      <td><button class="btn btn-primary">ENVIAR</button></td>
    </tr>
    <tr>
      <td>05/12</td>
      <td>Reelaboração TC</td>
      <td><input type="text" class="form-control"></td>
      <td><button class="btn btn-success" disabled>ENVIADO</button></td>
    </tr>
  </tbody>
</table>
