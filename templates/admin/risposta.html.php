<h1 class="ml-1"><?= htmlspecialchars(ucfirst($domanda->domanda), ENT_QUOTES, 'UTF-8') ?></h1>
<div class="mt-5">
    <div class="row justify-content-center">
        <?php if (!empty($domanda->getGruppo()->id_immagine) || !empty($domanda->id_immagine)) : ?>
            <div>
                <img src="/img/segnali/<?= $domanda->id_immagine ?? $domanda->getGruppo()->id_immagine ?>.jpg" class="rounded view-img">
            </div>
        <?php endif; ?>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="ans-container <?= $domanda->risposta == 'V' ? 'true' : 'false' ?>"><?= htmlspecialchars($domanda->risposta, ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-primary" id="btn-confirm" type="button">CONFERMA</button>
                        <?php if (empty($domanda->getCommento()) && $_SESSION['work_mode']) : ?>
                            <button class="btn btn-outline-success" id="btn-add-comment" type="button"><?= empty($domanda->getCommento()) ? 'Nuovo commento' : 'Modifica commento' ?></button>
                        <?php endif;  ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if (!empty($domanda->getCommento())) : ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <?php if (!empty($domanda->getCommento()->commento)) : ?>
                            <p class="text-white" style="font-size: 30px; line-height: 1.2"><?= nl2br(htmlspecialchars($domanda->getCommento()->commento, ENT_QUOTES, 'UTF-8')) ?></p>
                        <?php endif;  ?>
                        <?php if ($domanda->getCommento()->file_type == 'image') : ?>
                            <div style="max-width: 300px">
                                <img src="/img/domande/commenti/<?= htmlspecialchars($domanda->getCommento()->file_name, ENT_QUOTES, 'UTF-8') ?>" alt="Immagine <?= htmlspecialchars($file->file_name, ENT_QUOTES, 'UTF-8') ?>" style="width: 100%; height: 100%; object-fit: cover;" class="rounded view-img">
                            </div>
                        <?php elseif ($domanda->getCommento()->file_type == 'video') : ?>
                            <div style="width: 290px; height: 140px;">
                                <video controls style="width: 100%; height: 100%; object-fit: cover;" class="rounded">
                                    <source src="/video/domande/commenti/<?= htmlspecialchars($domanda->getCommento()->file_name, ENT_QUOTES, 'UTF-8') ?>" type="video/<?= pathinfo($domanda->getCommento()->file_name, PATHINFO_EXTENSION) ?>">
                                </video>
                            </div>
                        <?php endif; ?>
                        <?php if ($_SESSION['work_mode']) : ?>
                            <div class="mt-3">
                                <button class="btn btn-outline-success" id="btn-add-comment" type="button"><?= empty($domanda->getCommento()) ? 'Nuovo commento' : 'Modifica commento' ?></button>
                            </div>
                        <?php endif;  ?>
                    </div>
                </div>
            </div>
    </div>
<?php endif; ?>
</div>
</div>
<script>
    const upload_max_filesize = <?= (int) substr(ini_get('upload_max_filesize'), 0, -1) * 1e+6 ?>;
    $(function() {
        $("#btn-confirm").click(function() {
            window.close();
        })

        $("#btn-add-comment").click(function() {
            $("#rel-comment-card").remove();
            $(".app-content.content").addClass("show-overlay");
            $("body").append(`
                <div class="card shadow-sm position-fixed border" id="rel-comment-card" style="margin: 10px; top: 100px; z-index: 100; left: 50%; transform: translateX(-50%)">
                    <div class="card-body" style="width: 500px">
                    <form enctype="multipart/form-data" id="form-rel-comment" action="?controller=argomentiController&action=saveComment" method="post">
                        <input type="hidden" name="id_gruppo" value="${urlParams.get("id_gruppo")}">
                        <input type="hidden" name="id_domanda" value="${urlParams.get("id_domanda")}">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($domanda->getCommento()->id ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <h6>
                            Inserisci il commento
                            <button type="button" id="rm-rel-comment-card" class="close" style="outline: 0;" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </h6>
                        <div class="form-group">
                            <textarea id="rel-comment" name="comment" class="form-control" rows="10"><?= htmlspecialchars($domanda->getCommento()->commento ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <h6 class="text-white mb-1">Aggiungi un'immagine o un video</h6>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" title="<?= $domanda->getCommento()->file_name ? $domanda->getCommento()->file_name : '' ?>" name="file" id="rel-img" accept="image/jpg, image/jpeg, image/gif, image/png, video/mov, video/mp4">
                            <small class="form-text text-white">Grandezza massima supportata: ${Math.round(upload_max_filesize / 1e+6)}MB</small>
                            <label class="custom-file-label" for="rel-img"><?= $domanda->getCommento()->file_name ? 'Carica un\'altra immagine o video' : 'Carica un immagine o un video' ?></label>
                            <div class="invalid-feedback"></div>
                            <?php if (!empty($domanda->getCommento()->file_name)) : ?>
                                <button type="button" id="rm-uploaded-file" class="close" style="outline:0; top: 6px; position: absolute; right: -20px; z-index: 3;" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($domanda->getCommento()->file_name)) : ?>
                            <div id="preview-file-container">
                                <?php if ($domanda->getCommento()->file_type == 'image') : ?>
                                    <div style="width: 140px; height: 140px; margin-top: 20px">
                                        <img src="/img/domande/commenti/<?= htmlspecialchars($domanda->getCommento()->file_name, ENT_QUOTES, 'UTF-8') ?>" alt="Immagine <?= htmlspecialchars($file->file_name, ENT_QUOTES, 'UTF-8') ?>" style="width: 100%; height: 100%; object-fit: cover;" class="rounded">
                                    </div>
                                <?php elseif ($domanda->getCommento()->file_type == 'video') : ?>
                                    <div style="width: 290px; height: 140px; margin-top: 20px">
                                        <video controls style="width: 100%; height: 100%; object-fit: cover;" class="rounded">
                                            <source src="/video/domande/commenti/<?= htmlspecialchars($domanda->getCommento()->file_name, ENT_QUOTES, 'UTF-8') ?>" type="video/<?= pathinfo($domanda->getCommento()->file_name, PATHINFO_EXTENSION) ?>">
                                        </video>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary float-right mt-1">Salva</button>
                    </form>
                </div>
            `)
        })

        $(document).on("click", "#rm-rel-comment-card", function() {
            $(".app-content.content").removeClass("show-overlay");
            $("#rel-comment-card").remove();
        })

        $(document).on("mousedown", function(e) {
            if ($(e.target).attr('id') != "rel-comment-card" && $(e.target).parents('#rel-comment-card').length == 0 && $(e.target).attr("id") != "btn-add-comment" && $(e.target).parents('button').attr("id") != "btn-add-comment") {
                $("#rel-comment-card").remove();
                $(".app-content.content").removeClass("show-overlay");
            }
        })

        $(document).on("change", "input[name=file]", function() {
            $(this).removeClass("is-invalid");
            $(`label[for=${$(this).attr("id")}]`).text("Carica un'immagine o un video");
            $("#preview-file-container").remove();
            $("#rm-uploaded-file").remove();
            $(this).attr("title", "Nessun file selezionato");
            var files = Array.from(this.files);
            var exts = Array.from($(this).attr("accept").split(", "));
            var label = "";
            var error = false;
            var file = files[0];
            try {
                if (exts.includes(file.type)) {
                    label += file.name + ', ';
                } else {
                    throw `Errore in "${file.name.escape()}": estensione non supportata`;
                }
                var filesize = file.size;
                if (filesize > upload_max_filesize) {
                    throw `Errore in "${file.name.escape()}": dimensione file (${Math.round(filesize / 1e+6)}MB) oltre i limit previsti: ${Math.round(upload_max_filesize / 1e+6)}MB`;
                }
            } catch (e) {
                error = true;
                $(this).parent().find(".invalid-feedback").text(e);
                $(this).addClass("is-invalid");
                $(this).val("");
            }
            if (!error && label != "") {
                label = label.substring(0, label.length - 2);
                $(`label[for=${$(this).attr("id")}]`).text(label);
                $(this).attr("title", label);
            }
        })

        $(document).on("mouseup", "#rm-uploaded-file", function() {
            $("input[name=file]").val('');
            $("input[name=file]").removeClass("is-invalid");
            $(`label[for=${$("input[name=file]").attr("id")}]`).text("Carica un'immagine o un video");
            $("input[name=file]").attr("title", "Nessun file selezionato");
            $("#preview-file-container").remove();
            $("#form-rel-comment").prepend(`<input type="hidden" name="deleteImage">`);
            $(this).remove();
        })

        $(document).on("submit", "#form-rel-comment", function(e) {
            $("#form-rel-comment button[type=submit]").prop("disabled", true).text("Salvataggio...");
        })

    })
</script>
<style>
    .ans-container {
        color: #fff;
        font-weight: 500;
        font-size: 80pt;
        text-align: center;
        border-radius: 2px;
        user-select: none;
    }

    .ans-container.true {
        background-color: #46c34c;
        box-shadow: 0px 4px 0px 0px #2d802d;
    }

    .ans-container.false {
        background-color: #ca0000;
        box-shadow: 0px 4px 0px 0px #a50101;
    }
</style>