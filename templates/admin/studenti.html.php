<div class="card">
    <div class="card-body">
        <h5>Totale studenti <span class="badge badge-dark"><?= htmlspecialchars($totaleStudenti ?? 0, ENT_QUOTES, 'UTF-8') ?></span></h5>
        <div class="row justify-content-between mt-4">
            <div class="col-md-4">
                <form action="/admin/studenti" method="get">
                    <!-- <input type="hidden" name="controller" value="<?= htmlspecialchars($_GET['controller'] ?? '', ENT_QUOTES, 'UTF-8') ?>"> -->
                    <div class="form-group has-search mb-0">
                        <i class="bx bx-search-alt-2 form-control-feedback" style="margin-top: 2px"></i>
                        <input type="search" name="q" class="form-control text-white" value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>" id="search-link-input" autocomplete="off" placeholder="Cerca studenti...">
                        <small class="form-text">Cerca studenti per nome, cognome o email</small>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-right">
                <button class="btn btn-primary" onclick="location.href='/admin/studenti?action=add'">Aggiungi un nuovo studente</button>
            </div>
        </div>

        <div class="mt-4">
            <?php if (!empty($studenti)) : ?>
                <nav>
                    <ul class="pagination pagination-sm">
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage > 1 ? $currentPage - 1 : 1 ?><?= !empty($_GET['orderBy']) ? '&orderBy=' . $_GET['orderBy'] : '' ?><?= !empty($_GET['sortOrder']) ? '&sortOrder=' . $_GET['sortOrder'] : '' ?><?= !empty($_GET['q']) ? '&q=' . $_GET['q'] : '' ?>">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php
                        $numPages = ceil($totaleStudenti / 20);
                        for ($i = 1; $i <= $numPages; $i++) : ?>
                            <?php if ($i == $currentPage) : ?>
                                <li class="page-item active"><a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['orderBy']) ? '&orderBy=' . $_GET['orderBy'] : '' ?><?= !empty($_GET['sortOrder']) ? '&sortOrder=' . $_GET['sortOrder'] : '' ?><?= !empty($_GET['q']) ? '&q=' . $_GET['q'] : '' ?>"><?= $i ?></a></li>
                            <?php else : ?>
                                <li class="page-item"><a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['orderBy']) ? '&orderBy=' . $_GET['orderBy'] : '' ?><?= !empty($_GET['sortOrder']) ? '&sortOrder=' . $_GET['sortOrder'] : '' ?><?= !empty($_GET['q']) ? '&q=' . $_GET['q'] : '' ?>"><?= $i ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage + 1 <= $numPages ? $currentPage + 1 : $currentPage ?><?= !empty($_GET['orderBy']) ? '&orderBy=' . $_GET['orderBy'] : '' ?><?= !empty($_GET['sortOrder']) ? '&sortOrder=' . $_GET['sortOrder'] : '' ?><?= !empty($_GET['q']) ? '&q=' . $_GET['q'] : '' ?>">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <table class="table table-striped table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th data-sort="nome">Nome</th>
                            <th data-sort="cognome">Cognome</th>
                            <th data-sort="email">Email</th>
                            <th data-sort="data_esame_teoria">Data esame teoria</th>
                            <th data-sort="tipo_esame">Tipo esame</th>
                            <th data-sort="data_creazione">Data registrazione</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studenti as $key => $studente) : ?>
                            <tr data-id="<?= htmlspecialchars($studente->id, ENT_QUOTES, 'UTF-8') ?>">
                                <td class="row-click-edit"><?= $key + 1 ?></td>
                                <td class="row-click-edit"><?= htmlspecialchars($studente->nome, ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="row-click-edit"><?= htmlspecialchars($studente->cognome, ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="row-click-edit"><?= htmlspecialchars($studente->email, ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="row-click-edit"><?= !empty($studente->data_esame_teoria) ? date('d/m/Y', strtotime($studente->data_esame_teoria)) : 'Non impostata' ?></td>
                                <td class="row-click-edit"><?= $studente->tipo_esame ?></td>
                                <td class="row-click-edit"><?= date('d/m/Y', strtotime($studente->data_creazione)) ?></td>
                                <td><a class="text-danger" href="javascript:void(0)" onclick="confirm('Sei sicuro di voler eliminare questo utente?') ? location.href='?<?= !empty($_GET['page']) ? 'page=' . $_GET['page'] : '' ?><?= !empty($_GET['orderBy']) ? '&orderBy=' . $_GET['orderBy'] : '' ?><?= !empty($_GET['sortOrder']) ? '&sortOrder=' . $_GET['sortOrder'] : '' ?><?= !empty($_GET['q']) ? '&q=' . $_GET['q'] : '' ?>&action=delete&id=<?= htmlspecialchars($studente->id, ENT_QUOTES, 'UTF-8') ?>' : '' "><i class='bx bx-trash' style="color: inherit !important; margin-right: 4px"></i>Elimina</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <script>
                    $(function() {
                        $(".row-click-edit").click(function() {
                            location.href = `/admin/studenti?action=edit&id=${$(this).parent().attr('data-id')}`;
                        })
                    })
                </script>
                <nav>
                    <ul class="pagination pagination-sm">
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage > 1 ? $currentPage - 1 : 1 ?><?= !empty($_GET['orderBy']) ? '&orderBy=' . $_GET['orderBy'] : '' ?><?= !empty($_GET['sortOrder']) ? '&sortOrder=' . $_GET['sortOrder'] : '' ?><?= !empty($_GET['q']) ? '&q=' . $_GET['q'] : '' ?>">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php
                        $numPages = ceil($totaleStudenti / 20);
                        for ($i = 1; $i <= $numPages; $i++) : ?>
                            <?php if ($i == $currentPage) : ?>
                                <li class="page-item active"><a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['orderBy']) ? '&orderBy=' . $_GET['orderBy'] : '' ?><?= !empty($_GET['sortOrder']) ? '&sortOrder=' . $_GET['sortOrder'] : '' ?><?= !empty($_GET['q']) ? '&q=' . $_GET['q'] : '' ?>"><?= $i ?></a></li>
                            <?php else : ?>
                                <li class="page-item"><a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['orderBy']) ? '&orderBy=' . $_GET['orderBy'] : '' ?><?= !empty($_GET['sortOrder']) ? '&sortOrder=' . $_GET['sortOrder'] : '' ?><?= !empty($_GET['q']) ? '&q=' . $_GET['q'] : '' ?>"><?= $i ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $currentPage + 1 <= $numPages ? $currentPage + 1 : $currentPage ?><?= !empty($_GET['orderBy']) ? '&orderBy=' . $_GET['orderBy'] : '' ?><?= !empty($_GET['sortOrder']) ? '&sortOrder=' . $_GET['sortOrder'] : '' ?><?= !empty($_GET['q']) ? '&q=' . $_GET['q'] : '' ?>">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php else : ?>
                <?php if (isset($_GET['page']) && $_GET['page'] > 1) : ?>
                    <h5 class="text-center">Nessun utente trovato: pagina "<?= htmlspecialchars($_GET['page'], ENT_QUOTES, 'UTF-8') ?>" inesistente</h5>
                <?php else : ?>
                    <h5 class="text-center">Nessun utente trovato</h5>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<style>
    [data-sort]:hover {
        opacity: 0.7;
        cursor: pointer;
    }

    [data-sort]::after {
        content: '';
        background-image: url(/img/icons/sort.svg);
        width: 15px;
        height: 15px;
        float: right;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
    }

    a.text-danger:hover {
        opacity: 0.8;
        text-decoration: underline;
    }
</style>