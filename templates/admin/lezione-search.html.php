<div class="card">
    <h3 class="card-header"><?= htmlspecialchars($numItems ?? '', ENT_QUOTES, 'UTF-8') ?> risultat<?= $numItems > 1 ? 'i' : 'o' ?> per "<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>"</h3>
    <div class="card-body">
        <?php if (!empty($capitoli)) : ?>
            <h4 class="text-muted" data-toggle="collapse" href="#capitoli" role="button" aria-expanded="false" aria-controls="capitoli">Argomenti trovati</h4>
            <div class="collapse show" id="capitoli">
                <ul class="list-unstyled">
                    <?php foreach ($capitoli as $capitolo) : ?>
                        <li><a href="/admin/lezione?controller=<?= htmlspecialchars($_GET['controller'], ENT_QUOTES, 'UTF-8') ?>&action=viewChapter&id=<?= htmlspecialchars($capitolo->id, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($capitolo->descrizione, ENT_QUOTES, 'UTF-8') ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (!empty($gruppi)) : ?>
            <h4 class="text-muted" data-toggle="collapse" href="#gruppi" role="button" aria-expanded="false" aria-controls="gruppi">Gruppi trovati</h4>
            <div class="collapse show" id="gruppi">
                <ul class="list-unstyled">
                    <?php foreach ($gruppi as $gruppo) : ?>
                        <li><a href="/admin/lezione?controller=<?= htmlspecialchars($_GET['controller'], ENT_QUOTES, 'UTF-8') ?>&action=viewGroup&id=<?= htmlspecialchars($gruppo->id, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($gruppo->descrizione), ENT_QUOTES, 'UTF-8') ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if (!empty($domande)) : ?>
            <h4 class="text-muted" data-toggle="collapse" href="#domande" role="button" aria-expanded="false" aria-controls="domande">Domande trovate</h4>
            <div class="collapse show" id="domande">
                <ul class="list-unstyled">
                    <?php foreach ($domande as $domanda) : ?>
                        <li><a href="/admin/lezione?controller=argomentiController&action=viewGroup&id=<?= htmlspecialchars($domanda->id_gruppo, ENT_QUOTES, 'UTF-8') ?>&searched_id=<?= htmlspecialchars($domanda->id, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($domanda->domanda), ENT_QUOTES, 'UTF-8') ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>