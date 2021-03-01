<div class="card">
    <h4 class="card-header"><?= htmlspecialchars($capitolo->descrizione, ENT_QUOTES, 'UTF-8') ?></h4>
    <div class="card-body">
        <?php if (!empty($capitolo->getGruppi())) : ?>
            <div class="row">
                <?php foreach ($capitolo->getGruppi() as $gruppo) : ?>
                    <div class="col-lg-4 mb-4">
                        <div class="card-hover card h-100 shadow-sm group-card" data-id="<?= $gruppo->id ?>">
                            <div class="card-body cr-card-body">
                                <?php if (!empty($gruppo->id_immagine)) : ?>
                                    <img class="rounded" src="/img/segnali/<?= htmlspecialchars($gruppo->id_immagine, ENT_QUOTES, 'UTF-8') ?>.jpg" alt="Immagine <?= htmlspecialchars($gruppo->id_immagine, ENT_QUOTES, 'UTF-8') ?>">
                                <?php endif; ?>
                                <h5 class="card-title"><?= htmlspecialchars(ucfirst($gruppo->descrizione), ENT_QUOTES, 'UTF-8') ?></h5>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else : ?>
            <span>Nessun gruppo in questo argomento</span>
        <?php endif; ?>
    </div>
</div>
<script>
    $(function() {
        $(".group-card").click(function() {
            location.href = `?controller=<?= $_GET['controller'] ?? 'argomentiController' ?>&action=viewGroup&id=${$(this).attr('data-id')}`;
        })
    })
</script>