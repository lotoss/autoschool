<div class="panel-login">
    <h6 class="display-4 text-center mb-4" style="font-size: 24px">Accedi</h6>
    <div class="p-2 rounded">
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger" role="alert">
                <i class='bx bxs-x-square mr-2'></i><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        <form id="login-form" method="post">
            <div class="form-group">
                <label>Email</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class='bx bxs-user'></i></span>
                    </div>
                    <input type="email" name="email" placeholder="Inserisci l'email." class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class='bx bxs-key'></i></span>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="Inserisci la password." required>
                </div>
            </div>
            <button type="submit" class="btn btn-sm btn-success w-100" id="btn-sign-in">Accedi</button>
        </form>
    </div>
    <small class="d-block text-center text-muted mt-5">Copyright&copy; <?= date('Y') ?> <?= htmlspecialchars($_SERVER['SERVER_NAME'], ENT_QUOTES, 'UTF-8') ?></small>
</div>
<script>
    $(function() {
        $("#login-form").submit(function() {
            $("#btn-sign-in").prop('disabled', true);
            $("#btn-sign-in").text("Accedendo...");
        })
    })
</script>
<style>
    body {
        background-color: #f8f9fa;
    }

    .form-group label {
        font-weight: 500;
        font-size: .9em;
    }

    .input-group {
        border: 1px solid #ced4da;
        border-radius: .12em;
        padding: .3em .75em;
        background-color: #fff;
    }

    .input-group-text {
        border: 0;
        background-color: transparent;
        padding: 0 !important;
        margin-right: .75em;
    }

    .input-group>input.form-control {
        border: 0;
        padding: .2em .45em !important;
        height: auto !important;
        box-shadow: none !important;
        font-size: .9em;
        border-radius: inherit !important;
    }

    .btn-success {
        border-color: #2e7d32;
    }

    .panel-login {
        max-width: 400px;
        margin: 100px auto 0;
        padding: 10px;
    }

    @media screen and (max-width: 768px) {
        .panel-login {
            margin: 10px auto 0;
        }
    }
</style>