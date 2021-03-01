<div class="tab-content bg-transparent px-0" id="tab-content">
    <div class="tab-pane fade" id="expl" role="tabpanel" aria-labelledby="expl-tab">
        <h2 class="text-center mb-3"><?= htmlspecialchars(ucfirst($gruppo->descrizione), ENT_QUOTES, 'UTF-8') ?></h2>
        <div class="row justify-content-center">
            <?php if (!empty($gruppo->id_immagine)) : ?>
                <div class="col-md-3">
                    <img src="/img/segnali/<?= htmlspecialchars($gruppo->id_immagine, ENT_QUOTES, 'UTF-8') ?>.jpg" alt="Immagine <?= htmlspecialchars($gruppo->id_immagine, ENT_QUOTES, 'UTF-8') ?>" class="rounded shadow-sm w-100 view-img">
                </div>
            <?php endif; ?>
            <?php if (!empty($questionImgs) || !empty($relatedFiles) || $_SESSION['work_mode']) : ?>
                <div class="col-md-8">
                    <?php if (!empty($questionImgs) || !empty($relatedFiles)) : ?>
                        <div class="mb-2 flex-wrap d-flex" id="images-container" sortable>
                            <?php foreach ($questionImgs as $img) : ?>
                                <div class="inserted-image-content" style="margin: 0 10px 10px 0">
                                    <img src="/img/segnali/<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>.jpg" alt="Immagine <?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" style="width: 100%; height: 100%; object-fit: cover;" class="rounded view-img">
                                </div>
                            <?php endforeach; ?>
                            <?php foreach ($relatedFiles as $file) : ?>
                                <div style="margin: 0 10px 10px 0">
                                    <?php if ($file->file_type == 'image') : ?>
                                        <div sortable-item class="inserted-image-content" data-id="<?= htmlspecialchars($file->id, ENT_QUOTES, 'UTF-8') ?>">
                                            <img src="/img/gruppi/correlate/<?= htmlspecialchars($file->file_name, ENT_QUOTES, 'UTF-8') ?>" data-gruppo-link="<?= htmlspecialchars($file->id_gruppo_link, ENT_QUOTES, 'UTF-8') ?>" data-domanda-link="<?= htmlspecialchars($file->id_domanda_link ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="Immagine <?= htmlspecialchars($file->file_name, ENT_QUOTES, 'UTF-8') ?>" style="width: 100%; height: 100%; object-fit: contain; padding: 5px;" class="uploaded-img rounded view-img">
                                        </div>
                                        <?php if (!empty($file->id_gruppo_link)) : ?>
                                            <a class="go-to-link-content" target="_blank" rel="noopener noreferrer" href="/admin/lezione?controller=argomentiController&action=viewGroup&id=<?= htmlspecialchars($file->id_gruppo_link, ENT_QUOTES, 'UTF-8') ?><?= !empty($file->id_domanda_link) ? '&searched_id=' . htmlspecialchars($file->id_domanda_link, ENT_QUOTES, 'UTF-8') : '' ?>">Vai al collegamento</a>
                                        <?php endif; ?>
                                    <?php elseif ($file->file_type == 'video') : ?>
                                        <div sortable-item class="inserted-video-content" data-id="<?= htmlspecialchars($file->id, ENT_QUOTES, 'UTF-8') ?>">
                                            <video controls style="width: 100%; height: 100%; object-fit: cover;" class="rounded">
                                                <source src="/video/gruppi/correlati/<?= htmlspecialchars($file->file_name, ENT_QUOTES, 'UTF-8') ?>" type="video/<?= pathinfo($file->file_name, PATHINFO_EXTENSION) ?>">
                                            </video>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($_SESSION['work_mode']) : ?>
                        <div <?= (empty($questionImgs) && empty($relatedFiles)) ? 'class="text-center"' : '' ?>>
                            <button class="btn btn-primary" id="btn-add-file">Aggiungi immagini o video</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <script>
            const upload_max_filesize = <?= (int) substr(ini_get('upload_max_filesize'), 0, -1) * 1e+6 ?>;
            $(function() {
                $("#btn-add-file").click(function() {
                    $("#rel-img-card").remove();
                    $(".app-content.content").addClass("show-overlay");
                    $("body").append(`
                        <div class="card shadow-sm position-fixed border" id="rel-img-card" style="margin: 10px; top: 100px; z-index: 100; left: 50%; transform: translateX(-50%)">
                        <!--<button type="button" id="rm-rel-img-card" class="close" style="outline: 0;" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>-->  
                            <div class="card-body" style="width: 500px">
                                <form enctype="multipart/form-data" id="form-rel-img" action="?controller=argomentiController&action=saveRelFile" method="post">
                                    <input type="hidden" name="id_gruppo" value="<?= htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="media-uploader">
                                    </div>
                                    <button type="submit" class="btn btn-primary float-right mt-1">Salva</button>
                                </form>
                            </div>
                        </div>
                    `)
                    media.display();
                })
            })

            $(document).on("click", "#rm-rel-img-card", function() {
                $(".app-content.content").removeClass("show-overlay");
                $("#rel-img-card").remove();
            })

            $(document).on("mousedown", function(e) {
                if ($(e.target).attr('id') != "rel-img-card" && $(e.target).parents('#rel-img-card').length == 0 && $(e.target).attr("id") != "btn-add-comment" && $(e.target).parents('button').attr("id") != "btn-add-comment") {
                    $("#rel-img-card").remove();
                    $(".app-content.content").removeClass("show-overlay");
                }
            })

            $(document).on("submit", "#form-rel-img", function(e) {
                $("#form-rel-img button[type=submit]").prop("disabled", true).text("Salvataggio...");
                if ($("#rel-img").val() == "") {
                    $(".app-content.content").removeClass("show-overlay");
                    $("#rel-img-card").remove();
                    e.preventDefault();
                    return false;
                }
            })

            <?php if ($_SESSION['work_mode']) : ?>
                $(document).on("mouseenter", ".inserted-image-content,  .inserted-video-content", function() {
                    $(this).append(`
                        <button type="button" class="close" id="remove-image-icon" style="position: absolute; opacity: 1; top: -1px; right: -1px; outline: 0; background-color: #ff0000; border-radius: 100%; padding: 6px 1px; line-height: 0.2" data-id="${$(this).attr("data-id")}">
                            <span style="color: #fff">&times;</span>
                        </button>
                    `)
                })

                $(document).on("mouseleave", ".inserted-image-content, .inserted-image-content > div, .inserted-video-content, .inserted-video-content > div", function() {
                    $("#remove-image-icon").remove();
                })

                $(document).on("click", "#remove-image-icon", function() {
                    var that = $(this).parent();
                    if (confirm("Sei sicuro di vole eliminare questo file?")) {
                        fetch(`/admin/remove-related-file?id=${$(this).attr("data-id")}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.status == "OK") {
                                    $(that).parent().remove();
                                    if ($(".inserted-image-content").length == 0 && $(".inserted-video-content").length == 0) {
                                        $("#images-container").remove();
                                        $("#btn-add-file").parent().addClass("text-center");
                                    }
                                } else {
                                    console.error(data.error);
                                }
                            })
                    }
                })
            <?php endif; ?>
        </script>
        <script src="/js/media.js"></script>
    </div>

    <div class="tab-pane fade" id="quiz" role="tabpanel" aria-labelledby="quiz-tab">
        <table class="table table-striped border" id="question-table" style="font-size: 40px">
            <thead>
                <tr>
                    <?php if ($imageColumn) : ?>
                        <th>Figura</th>
                    <?php endif; ?>
                    <th>#</th>
                    <th>Domanda</th>
                    <?php if (!empty($_GET['view_ans']) && $_GET['view_ans'] == 'true') : ?>
                        <th>Risposta</th>
                    <?php endif; ?>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 0; ?>
                <?php $domande = $gruppo->getDomande(); ?>
                <?php if (!isset($_SESSION['work_mode']) && (empty($_GET['view_ans']) || $_GET['view_ans'] == 'false')) {
                    shuffle($domande);
                } ?>
                <?php foreach ($domande as $key => $domanda) : ?>
                    <tr data-id="<?= htmlspecialchars($domanda->id, ENT_QUOTES, 'UTF-8') ?>">
                        <?php if ($imageColumn && !$count) : ?>
                            <?php
                            $prev = $domanda->id_immagine;
                            for ($i = $key; $i < count($domande); $i++)
                                if ($prev == $domande[$i]->id_immagine)
                                    $count++;
                                else break 1;
                            ?>
                            <td class="td-img border" rowspan="<?= $count ?>">
                                <?php if (!empty($domanda->id_immagine)) : ?>
                                    <img src="/img/segnali/<?= htmlspecialchars($domanda->id_immagine, ENT_QUOTES, 'UTF-8') ?>.jpg" alt="Immagine <?= htmlspecialchars($domanda->id_immagine, ENT_QUOTES, 'UTF-8') ?>" class="rounded view-img">
                                <?php elseif (!empty($gruppo->id_immagine)) : ?>
                                    <img src="/img/segnali/<?= htmlspecialchars($gruppo->id_immagine, ENT_QUOTES, 'UTF-8') ?>.jpg" alt="Immagine <?= htmlspecialchars($gruppo->id_immagine, ENT_QUOTES, 'UTF-8') ?>" class="rounded view-img">
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <td class="text-center p-0" style="<?= isset($_GET['searched_id']) && $_GET['searched_id'] == $domanda->id ? 'background-color: #800000;' : '' ?><?= $domanda->isContrapposta() && !empty($_GET['view_ans']) && $_GET['view_ans'] == 'true' ? 'background-color: #46c34c;' : '' ?>"><?= $key + 1 ?></td>
                        <td class="view-question-td" style="<?= isset($_GET['searched_id']) && $_GET['searched_id'] == $domanda->id ? 'background-color: #800000;' : '' ?>"><?= htmlspecialchars($domanda->domanda, ENT_QUOTES, 'UTF-8') ?></td>
                        <?php if (!empty($_GET['view_ans']) && $_GET['view_ans'] == 'true') : ?>
                            <td class="text-center" style="font-weight: 500; font-size: 1.2em; <?= isset($_GET['searched_id']) && $_GET['searched_id'] == $domanda->id ? 'background-color: #800000;' : '' ?><?= $domanda->risposta == 'V' ? 'color: #46c34c' : 'color: #ca0000' ?> !important"><?= htmlspecialchars($domanda->risposta, ENT_QUOTES, 'UTF-8') ?></td>
                        <?php endif; ?>
                        <td class="text-center p-1" <?= isset($_GET['searched_id']) && $_GET['searched_id'] == $domanda->id ? 'style="background-color: #800000"' : '' ?>>
                            <button type="button" class="btn btn-secondary btn-show-risposta">Dettagli</button>
                        </td>
                        <td class="text-center p-0" <?= isset($_GET['searched_id']) && $_GET['searched_id'] == $domanda->id ? 'style="background-color: #800000"' : '' ?>>
                            <button type="button" class="btn btn-primary btn-avvia-domanda w-100">Test</button>
                        </td>
                        <td <?= isset($_GET['searched_id']) && $_GET['searched_id'] == $domanda->id ? 'style="background-color: #800000"' : '' ?>>
                            <?php if (isset($_SESSION['work_mode'])) : ?>
                                <div class="d-flex justify-content-center">
                                    <div data-id="<?= htmlspecialchars($domanda->getGrado()->id ?? '', ENT_QUOTES, 'UTF-8') ?>" data-value="1" class="diff-content-editable <?= isset($domanda->getGrado()->grado) && $domanda->getGrado()->grado == 1 ? 'active' : '' ?>" title="Facile"></div>
                                    <div data-id="<?= htmlspecialchars($domanda->getGrado()->id ?? '', ENT_QUOTES, 'UTF-8') ?>" data-value="2" class="diff-content-editable <?= isset($domanda->getGrado()->grado) && $domanda->getGrado()->grado == 2 ? 'active' : '' ?>" title="Medio"></div>
                                    <div data-id="<?= htmlspecialchars($domanda->getGrado()->id ?? '', ENT_QUOTES, 'UTF-8') ?>" data-value="3" class="diff-content-editable mr-0 <?= isset($domanda->getGrado()->grado) && $domanda->getGrado()->grado == 3 ? 'active' : '' ?>" title="Difficile"></div>
                                </div>
                                <div class="d-flex justify-content-center mt-2">
                                    <div class="skull-icon set-qst-contrapposta <?= $domanda->isContrapposta() ? 'active' : '' ?>"></div>
                                </div>
                            <?php else : ?>
                                <?php if (!empty($domanda->getGrado())) : ?>
                                    <div class="d-flex justify-content-center <?= $domanda->isContrapposta() ? 'mb-2' : '' ?>">
                                        <div data-id="<?= htmlspecialchars($domanda->getGrado()->id, ENT_QUOTES, 'UTF-8') ?>" class="diff-content diff-content-<?= htmlspecialchars($domanda->getGrado()->grado, ENT_QUOTES, 'UTF-8') ?>" title="<?= $domanda->getGrado()->grado == 1 ? 'Facile' : ($domanda->getGrado()->grado == 2 ? 'Medio' : 'Difficile') ?>"></div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($domanda->isContrapposta())) : ?>
                                    <div class="d-flex justify-content-center">
                                        <div class="skull-icon <?= $domanda->isContrapposta() ? 'active' : '' ?>"></div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $count--; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
        <script>
            const prf = new WebSocket("wss://" + location.hostname + ":60000");
            prf.onopen = e => {
                prf.send('{ "type": "prof", "id": "<?= htmlspecialchars($autoscuola->id, ENT_QUOTES, 'UTF-8') ?>" }');
                const prfID = setInterval(() => prf.send("alive"), 1000);
                prf.onclose = () => clearInterval(prfID);

                prf.onmessage = msg => {
                    var domanda = JSON.parse(msg.data).domanda;
                    console.log(domanda);
                    if (domanda && domanda.id_gruppo == urlParams.get('id')) {
                        $(`[data-id="${domanda.id_domanda}"]`).find(".btn-avvia-domanda").removeClass("btn-primary").addClass("btn-warning").html("<nobr>Attiva</nobr>");
                    }
                }
            };

            $(function() {
                if (urlParams.get("searched_id") != null) {
                    $("#quiz").addClass("show active");
                    $("#quiz-tab").addClass("active");
                } else {
                    if (localStorage.getItem("tab") != undefined) {
                        $(localStorage.getItem("tab")).addClass("show active");
                        $(localStorage.getItem("tab") + "-tab").addClass("active");
                    } else {
                        $("#expl").addClass("show active");
                        $("#expl-tab").addClass("active");
                    }
                }

                $("#tab-list button").click(function() {
                    localStorage.setItem("tab", $(this).attr("href"))
                })

                $(".btn-show-risposta").click(function() {
                    window.open(`?controller=<?= htmlspecialchars($_GET['controller'], ENT_QUOTES, 'UTF-8') ?>&action=viewQuestion&id_gruppo=<?= htmlspecialchars($gruppo->id, ENT_QUOTES, 'UTF-8') ?>&id_domanda=${$(this).parent().parent().attr("data-id")}`);
                })

                $(".btn-avvia-domanda").click(function() {
                    prf.send(JSON.stringify({
                        domanda: {
                            session_id: genUUID(),
                            id_domanda: $(this).parent().parent().attr("data-id"),
                            id_gruppo: '<?= htmlspecialchars($gruppo->id, ENT_QUOTES, 'UTF-8') ?>'
                        }
                    }));
                    $(".btn-warning").removeClass('btn-warning').addClass("btn-primary").html("<nobr>Test</nobr>");
                    $(this).removeClass("btn-primary").addClass("btn-warning").html("<nobr>Attiva</nobr>");
                })

                $(".diff-content-editable").click(function() {
                    var id_gruppo = urlParams.get('id');
                    var id_domanda = $(this).parent().parent().parent().attr('data-id');
                    fetch(`/admin/lezione/setgradodomanda?patente=<?= htmlspecialchars($_SESSION['patente'], ENT_QUOTES, 'UTF-8') ?>${$(this).attr('data-id') != "" ? `&id_grado=${$(this).attr('data-id')}` : "" }&id_domanda=${id_domanda}&id_gruppo=${id_gruppo}&grado=${$(this).attr('data-value')}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status == 'OK') {
                                if ($(this).hasClass('active')) {
                                    $(this).removeClass('active');
                                } else {
                                    $(this).parent().find('.active').removeClass('active');
                                    $(this).addClass('active');
                                }
                            }
                        })
                })

                $("#switch-view-ans").change(function(e) {
                    urlParams.set("view_ans", $(this).prop("checked"));
                    location.href = location.pathname + "?" + urlParams.toString();
                })

                $(".set-qst-contrapposta").click(function() {
                    var that = $(this);
                    fetch(`/admin/set-domanda-contrapposta?id_domanda=${$(this).parent().parent().parent().attr("data-id")}&id_gruppo=${urlParams.get("id")}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status == "OK") {
                                if (data.active) {
                                    $(that).addClass("active");
                                } else {
                                    $(that).removeClass("active");
                                }
                            } else {
                                console.error(data.error);
                            }
                        })
                })

                if (urlParams.get("searched_id") != null) {
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $(`tr[data-id=${urlParams.get("searched_id")}]`).offset().top - 200
                    }, 300);
                }
            })
        </script>
        <style>
            td,
            th {
                border-color: inherit !important;
            }

            .td-img {
                vertical-align: top !important;
                width: 240px !important;
            }

            .td-img>img {
                position: sticky;
                top: 140px;
                width: inherit;
            }

            tr:has(.td-img) {
                position: absolute;
            }

            .diff-content-editable {
                width: 15px;
                height: 15px;
                margin-right: 15px;
                border: 1px solid #fff;
                cursor: pointer;
            }

            .diff-content {
                width: 18px;
                height: 18px;
            }

            td .diff-content-editable:nth-child(1):hover,
            td .diff-content-editable:nth-child(1).active,
            .diff-content-1 {
                background-color: #4CAF50;
            }


            td .diff-content-editable:nth-child(2):hover,
            td .diff-content-editable:nth-child(2).active,
            .diff-content-2 {
                background-color: #ffc107;
            }

            td .diff-content-editable:nth-child(3):hover,
            td .diff-content-editable:nth-child(3).active,
            .diff-content-3 {
                background-color: #e64a19;
            }

            .set-qst-contrapposta {
                cursor: pointer;
            }

            .skull-icon {
                background-image: url(/img/icons/skull.svg);
                background-position: center;
                background-size: contain;
                width: 50px;
                height: 50px;
                background-repeat: no-repeat;
                transition: background-image .3s;
            }

            .skull-icon.set-qst-contrapposta:hover {
                background-image: url(/img/icons/skull-hover.svg);
            }

            .skull-icon.set-qst-contrapposta:active,
            .skull-icon.active {
                background-image: url(/img/icons/skull-active.svg);
            }
        </style>
    </div>
</div>