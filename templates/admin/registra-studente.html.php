<div class="mb-3 w-100 d-flex justify-content-center">
    <?php if (isset($mail_exception)) : ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($mail_exception->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($exception)) : ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
    <div class="card" style="max-width: 1100px">
        <div class="card-body">
            <h5 class="card-title"><?= $_GET['action'] == 'add' ? 'Nuovo studente' : 'Modifica studente' ?></h5>
            <form method="post" id="register-form">
                <input type="hidden" name="studente[id]" value="<?= htmlspecialchars($_POST['studente']['id'] ?? $studente->id ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Nome <span class="text-danger">*</span></label>
                        <input type="text" name="studente[nome]" class="form-control<?= isset($errors['nome']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['studente']['nome'] ?? $studente->nome ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" required>
                        <?php if (isset($errors['nome'])) : ?>
                            <span class="invalid-feedback"><?= htmlspecialchars($errors['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Cognome <span class="text-danger">*</span></label>
                        <input type="text" name="studente[cognome]" class="form-control<?= isset($errors['cognome']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['studente']['cognome'] ?? $studente->cognome ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" required>
                        <?php if (isset($errors['cognome'])) : ?>
                            <span class="invalid-feedback"><?= htmlspecialchars($errors['cognome'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Data di nascita <span class="text-danger">*</span></label>
                        <input type="date" name="studente[data_nascita]" class="form-control<?= isset($errors['data_nascita']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['studente']['data_nascita'] ??  $studente->data_nascita ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" required min="1920-01-01" max="<?= date('Y-m-d') ?>">
                        <?php if (isset($errors['data_nascita'])) : ?>
                            <span class="invalid-feedback"><?= htmlspecialchars($errors['data_nascita'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Sesso <span class="text-danger">*</span></label>
                        <select name="studente[sesso]" class="custom-select<?= isset($errors['sesso']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['studente']['sesso'] ?? $studente->sesso ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" required>
                            <option>Seleziona...</option>
                            <option value="M" <?= ($_POST['studente']['sesso'] ?? $studente->sesso ?? '') == 'M' ? 'selected' : '' ?>>Maschio</option>
                            <option value="F" <?= ($_POST['studente']['sesso']  ?? $studente->sesso ?? '') == 'F' ? 'selected' : '' ?>>Femmina</option>
                        </select>
                        <?php if (isset($errors['sesso'])) : ?>
                            <span class="invalid-feedback"><?= htmlspecialchars($errors['sesso'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Email <span class="text-danger">*</span></label>
                        <input type="email" name="studente[email]" class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>" value=" <?= htmlspecialchars($_POST['studente']['email'] ?? $studente->email ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" required>
                        <?php if (isset($errors['email'])) : ?>
                            <span class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Tipo esame da sostenere <span class="text-danger">*</span></label>
                        <select name="studente[tipo_esame]" class="custom-select<?= isset($errors['tipo_esame']) ? ' is-invalid' : '' ?>" autocomplete="off" required>
                            <option>Seleziona...</option>
                            <option value="B" <?= ($_POST['studente']['tipo_esame'] ?? $studente->tipo_esame ?? '') == 'B' ? 'selected' : '' ?>>Patente B</option>
                            <option value="AM" <?= ($_POST['studente']['tipo_esame'] ??  $studente->tipo_esame ?? '') == 'AM' ? 'selected' : '' ?>>Patente AM</option>
                        </select>
                        <?php if (isset($errors['tipo_esame'])) : ?>
                            <span class="invalid-feedback"><?= htmlspecialchars($errors['tipo_esame'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Data esame teoria</label>
                        <input type="date" name="studente[data_esame_teoria]" class="form-control<?= isset($errors['data_esame_teoria']) ? ' is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['studente']['data_esame_teoria'] ?? $studente->data_esame_teoria ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="off">
                        <?php if (isset($errors['data_esame_teoria'])) : ?>
                            <span class="invalid-feedback"><?= htmlspecialchars($errors['data_esame_teoria'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Note</label>
                        <textarea name="studente[note]" rows="5" class="form-control"><?= htmlspecialchars($_POST['studente']['note'] ?? $studente->note ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                    <div class="d-flex col-md-12 justify-content-between">
                        <button type="button" class="btn btn-danger" onclick="location.href='/admin/studenti'">Annulla</button>
                        <button type="submit" class="btn btn-success" id="btn-register"><?= $_GET['action'] == 'add' ? 'REGISTRA' : 'AGGIORNA' ?></button>
                    </div>
                </div>
            </form>
            <small class="d-block mt-3"><span class="text-danger mt-4">*</span> : Campi obbligatori</small>
            <?php if (isset($_GET['action']) && $_GET['action'] == "edit") : ?>
                <small class="d-block mt-3"><a class="text-danger" href="javascript:void(0)" onclick="confirm('Sei sicuro di voler eliminare questo utente?\nQuesta azione non è reversibile constinuare solo se si ha davvero intenzione di procedere') ? location.href='?action=delete&id=<?= htmlspecialchars($studente->id, ENT_QUOTES, 'UTF-8') ?>' : '' ">Elimina studente (definitivo)</a></small>
            <?php endif; ?>
        </div>
    </div>
</div>
<style>
    .form-group>input,
    .form-group>select {
        font-size: 20px !important;
    }

    label,
    select,
    input {
        color: #fff !important;
    }

    a.text-danger:hover {
        opacity: 0.8;
        text-decoration: underline;
    }
</style>
<script>
    $(function() {
        $("#register-form").submit(function() {
            $("#btn-register").prop('disabled', true);
            $("#btn-register").text("Processo la richiesta...");
        })
    })
</script>