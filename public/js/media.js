class JqueryClass {
    constructor() {
        this.id = this.genId();
    }

    genId() {
        return Math.random().toString(36).substr(2, 9);
    }

    display() {
        if ($(this.target).length > 0) {
            $(this.target).html(this.html);
        } else {
            console.error(`Nessun target "${this.target}" definito`);
        }
    }
}

class MediaSearch extends JqueryClass {
    constructor(title = "Vuoi collegare questa immagine ad una domanda o ad un argomento?") {
        super();
        this.target = ".media-search";
        this.containerId = "media-search-container-" + this.id;
        this.container = "#" + this.containerId;
        this.inputId = "media-search-input-" + this.id;
        this.input = "#" + this.inputId;
        this.resultContainerId = "media-search-result-container-" + this.id;
        this.resultContainer = "#" + this.resultContainerId;
        this.mediaResultListClass = "li-media-search-result-" + this.id;
        this.mediaResultList = "." + this.mediaResultListClass;
        this.selectedResultContainerId = "selected-result-container-" + this.id;
        this.selectedResultContainer = "#" + this.selectedResultContainerId;
        this.title = title;
        this.fixed = false;
        this.html = `
            <div class="form-group mb-0 mt-1 border-top pt-1" id="${this.containerId}">
                <h6 class="text-white mb-1">${this.title}</h6>
                <div class="form-group has-search mb-0">
                    <i class='bx bx-search-alt-2 form-control-feedback' style="margin-top: 2px"></i>
                    <input type="text" class="form-control text-white" id="${this.inputId}" autocomplete="off" placeholder="Cerca una spiegazione da collegare a questa immagine">
                </div>
                <ul class="media-search-result-container list-unstyled rounded" id="${this.resultContainerId}"></ul>
            </div>`;
    }

    search(q) {
        if (q.trim() !== "") {
            fetch('?controller=argomentiController&action=searchExpl&q=' + q.trim().replace(/\s{2,}/g, " "))
                .then(response => response.json())
                .then(data => {
                    $(this.resultContainer).html('').removeClass('shadow-lg border');
                    if (data.results.gruppi.length > 0 || data.results.domande.length > 0) {
                        $(this.resultContainer).addClass("shadow-lg border");
                        data.results.gruppi.forEach(gruppo => {
                            $(this.resultContainer).append(`
                                <li title="Argomento" data-id-gruppo="${gruppo.id.escape()}" class="${this.mediaResultListClass}" style="color: #dfe3e7 !important"><i class='bx bx-book-open' style="margin-right: 4px"></i>${gruppo.descrizione.escape().charAt(0).toUpperCase() + gruppo.descrizione.slice(1)}</li>
                            `)
                        })

                        data.results.domande.forEach(domanda => {
                            $(this.resultContainer).append(`
                                <li title="Domanda" data-id-gruppo="${domanda.id_gruppo.escape()}" data-id-domanda="${domanda.id.escape()}" class="${this.mediaResultListClass}" style="color: #dfe3e7 !important"><i class='bx bx-question-mark' style="margin-right: 4px"></i>${domanda.domanda.escape().charAt(0).toUpperCase() + domanda.domanda.slice(1)}</li>
                            `)
                        })
                    }
                });
        } else {
            $(this.resultContainer).html('').removeClass('shadow-lg border');
        }
    }

    remove() {
        $(this.container).remove();
        $(this.selectedResultContainer).remove();
    }

    select(label, id_gruppo, id_domanda) {
        $(this.container).replaceWith(`
            <div id="${this.selectedResultContainerId}" class="position-relative rounded shadow-sm mt-2 bg-dark" style="padding: 6px 10px; font-size: 16px; background: #1A233A">
                <h6 class="text-success"><i class='bx bx-list-check' style="color: inherit; margin-right: 4px; vertical-align: text-top"></i>${(id_domanda !== null && id_domanda !== "" && id_domanda !== undefined) ? 'Domanda selezionata' : 'Argomento selezionato'}</h6>    
                <input name="id_gruppo_link" type="hidden" value="${id_gruppo}">
                <input name="id_domanda_link" type="hidden" value="${(id_domanda !== null && id_domanda !== "" && id_domanda !== undefined) ? id_domanda : ''}">
                <div class="text-white pl-2">${label}</div>
                <button type="button" id="rm-selected-result" class="close" style="outline:0; top: 6px; position: absolute; right: -20px; z-index: 3;" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `)
    }

}

class Media extends JqueryClass {
    constructor(inputLabelText = "Carica un'immagine o un video") {
        super();
        this.target = ".media-uploader";
        this.inputName = "media-uploader-file";
        this.containerId = "media-uploader-container-" + this.id;
        this.container = "#" + this.containerId;
        this.inputId = 'media-uploader-file-' + this.id;
        this.input = "#" + this.inputId;
        this.inputLabel = `label[for="${this.inputId}"]`;
        this.inputLabelText = inputLabelText;
        this.removeFileButtonName = "rm-media-uploader-file" + '-' + this.id;
        this.removeFileButton = "#" + this.removeFileButtonName;
        this.mediaSearch = new MediaSearch();

        this.html = `
            <div id="">
                <h6>Aggiungi un'immagine o un video</h6>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="${this.inputName}" id="${this.inputId}" accept="image/jpg, image/jpeg, image/gif, image/png, video/mov, video/mp4">
                    <small class="form-text text-white">Grandezza massima supportata: ${Math.round(upload_max_filesize / 1e+6)}MB</small>
                    <label class="custom-file-label" for="${this.inputId}">${inputLabelText}</label>
                    <div class="invalid-feedback"></div>
                    <div class="media-search"></div>
                </div>
            </div>
        `;
    }

    upload() {
        $("input[name='media_uploader_delete']").remove();
        $(this.input).removeClass("is-invalid").attr("title", "Nessun file selezionato");
        $(this.inputLabel).text("Carica un'immagine o un video");
        $(this.removeFileButton).remove();
        let file = Array.from($(this.input)[0].files)[0];
        let exts = Array.from($(this.input).attr("accept").split(", "));
        let label = "";
        try {
            if (file.name !== "") {
                if (exts.includes(file.type)) {
                    label += file.name + ', ';
                } else {
                    throw `Errore in "${file.name.escape()}": estensione non supportata`;
                }
                let filesize = file.size;
                if (filesize > upload_max_filesize) {
                    throw `Errore in "${file.name.escape()}": dimensione file (${Math.round(filesize / 1e+6)}MB) oltre i limit previsti: ${Math.round(upload_max_filesize / 1e+6)}MB`;
                }

                label = label.substring(0, label.length - 2);
                this.fill(label);
                return file;
            }
        } catch (e) {
            $(this.input).addClass("is-invalid").val("").parent().find(".invalid-feedback").text(e);
        }

        return false;
    }

    fill(label) {
        $(this.inputLabel).text(label);
        $(this.input).attr("title", label).after(`
            <button type="button" id="${this.removeFileButtonName}" class="close" style="outline:0; top: 6px; position: absolute; right: -20px; z-index: 3;" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `)
    }
}

var media = new Media();

$(document).on("change", media.input, function() {
    let file = media.upload();
    if (!file) {
        if (!media.mediaSearch.fixed && media.mediaSearch) {
            media.mediaSearch.remove();
        }
        return false;
    }

    if (!media.mediaSearch.fixed && file.type.substring(0, file.type.indexOf("/")) != "video" && $("#link-to-container").length == 0 && $("#selected-result-container").length == 0) {
        media.mediaSearch.display();
    }
})

$(document).on("click", media.removeFileButton, function() {
    $(media.input).removeClass("is-invalid").attr("title", "Nessun file selezionato").val("");
    $(media.input).after('<input type="hidden" name="media_uploader_delete" value="">');
    $(media.inputLabel).text("Carica un'immagine o un video");
    if (!media.mediaSearch.fixed) {
        media.mediaSearch.remove();
    }
    $(this).remove();
})

$(document).on("keypress", media.mediaSearch.input, function(e) {
    media.mediaSearch.search($(this).val() + String.fromCharCode(e.keyCode));
})

$(document).on("keyup", media.mediaSearch.input, function(e) {
    if (e.keyCode == 8) {
        media.mediaSearch.search($(this).val());
    }
})

$(document).on("keydown", media.mediaSearch.input, function(e) {
    if (e.keyCode == 13) {
        media.mediaSearch.search(e.target.value);
        e.preventDefault();
    }
})

$(document).bind("paste", media.mediaSearch.input, function(e) {
    media.mediaSearch.search(e.originalEvent.clipboardData.getData('text'));
})

$(document).on("click", media.mediaSearch.mediaResultList, function() {
    var label = $(this).text();
    var id_gruppo = $(this).attr("data-id-gruppo");
    var id_domanda = $(this).attr("data-id-domanda");

    media.mediaSearch.select(label, id_gruppo, id_domanda);
})

$(document).on("click", "#rm-selected-result", function() {
    $(media.mediaSearch.selectedResultContainer).replaceWith(media.mediaSearch.html);
})