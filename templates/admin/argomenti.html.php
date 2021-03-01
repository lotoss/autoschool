<div class="card">
    <h4 class="card-header">Argomenti</h4>
    <div class="card-body">
        <?php if (!empty($capitoli)) : ?>
            <div class="row">
                <?php foreach ($capitoli as $capitolo) : ?>
                    <div class="col-lg-3 mb-4">
                        <div class="card-hover card chapter-card h-100 shadow-sm" data-id="<?= htmlspecialchars($capitolo->id, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="card-body text-center cr-card-body">
                                <h5 class="card-title text-white"><?= htmlspecialchars($capitolo->descrizione, ENT_QUOTES, 'UTF-8') ?></h5>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php else : ?>
            <span>Nessun argomento</span>
        <?php endif; ?>
    </div>
</div>
<script>
    $(function() {
        $(".chapter-card").click(function() {
            location.href = `?controller=<?= htmlspecialchars($_GET['controller'] ?? 'argomentiController', ENT_QUOTES, 'UTF-8') ?>&action=viewChapter&id=${$(this).attr('data-id')}`;
        })
    })
</script>