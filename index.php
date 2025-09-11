<?php
include_once("templates/header.php");
?>
<?php if (isset($_SESSION["msg"])): ?>
    <div class="alert alert-info">
        <?= $_SESSION["msg"] ?>
    </div>
    <?php unset($_SESSION["msg"]); ?>
<?php endif; ?>

<div class="container mt-5">
    <form action="<?= $BASE_URL ?>/config/process.php" method="POST">
        <input type="hidden" name="type" value="login">
        <h1 class="text-center">Login</h1>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo" id="flexRadioDefault1" value="aluno"checked>
            <label class="form-check-label" for="flexRadioDefault1">Aluno</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo" id="flexRadioDefault2" value="professor" >
            <label class="form-check-label" for="flexRadioDefault2">Professor</label>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Usuário</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" class="form-control" id="password" name="senha" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <div class="mt-3">
            <span>Caso ainda não tenha as credenciais, entre me contato com o coordenador!</span>
        </div>
    </form>

</div>