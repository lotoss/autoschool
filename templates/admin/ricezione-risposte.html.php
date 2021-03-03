<table class="table table-striped table-sm text-center" id="table-ans" style="font-size: 20px; margin-top: -80px">
    <thead class="thead-dark">
        <th style="font-size: 30px; background-color: #46c34c !important; width: 45%">Vero</th>
        <th style="font-size: 30px;">
            <span id="true-count" style="color:#fff !important">0</span>-<span id="false-count" style="color:#fff !important">0</span>
            <hr class="m-0" style="border-color: #fff;">
            <span id="ans-count" style=" color:#fff !important">0</span>/<span id="users-count" style="color:#fff !important">0</span>
        </th>
        <th style="font-size: 30px; background-color: #ca0000 !important;  width: 45%">Falso</th>
    </thead>
    <tbody style="font-size: 1.3em;">
    </tbody>
</table>
<div class="position-fixed w-100 px-2 pb-2 d-flex justify-content-end" style="bottom: 0; z-index: 999; margin: 0 -1rem;">
    <button type="button" class="btn btn-lg btn-danger mr-2" id="btn-close-session" title="Chiudi la sessione attuale">Chiudi sessione</button>
    <button type="button" class="btn btn-lg btn-dark mr-2" id="btn-reload-session" title="Ricaricando la sessione verrano riammessi tutti gli studenti">Ricarica sessione</button>
    <button type="button" class="btn btn-lg btn-secondary mr-2" id="btn-view-users" title="Visualizzi tutti gli studenti connessi o che hai rimosso dalla sessione">Visualizza studenti connessi</button>
    <button type="button" class="btn btn-lg btn-primary" id="btn-extra-question" title="Invia una domanda vuota agli studenti">Domanda extra</button>
</div>
<script>
    const adm = new WebSocket("wss://" + location.hostname + ":60000");
    var falseCount = 0;
    var trueCount = 0;
    var usersCount = 0;
    var users = [];
    var ansCount = 0;

    adm.onopen = e => {
        adm.send('{ "type": "admin", "id": "<?= htmlspecialchars($autoscuola->id, ENT_QUOTES, 'UTF-8') ?>" }');
        const admID = setInterval(() => adm.send("alive"), 1000);
        adm.onclose = () => clearInterval(admID);

        adm.onmessage = msg => {
            let data = JSON.parse(msg.data);
            console.log(data);

            if (data.utente) {
                let utente = data.utente.row;
                let bool = data.utente.bool;
                let found = false;
                utente.bool = true;
                users.forEach(user => {
                    if (user.id === utente.id && bool) {
                        found = true;
                        user.bool = true;
                        return false;
                    } else if (user.id === utente.id && !bool) {
                        found = true;
                        utente.bool = false;
                        user.bool = false;
                        return false;
                    }
                })

                if (!found) {
                    users.push(utente);

                }

                usersCount = 0;
                users.forEach(user => {
                    if (user.bool) {
                        usersCount++;
                    }
                })

                $("#users-count").text(usersCount);

                refreshUsersList();
            }

            if (data.domanda) {
                let domanda = data.domanda.row;

                if (domanda) {
                    resetView();
                }
            }

            if (data.risposta) {
                ansCount++;
                $("#ans-count").text(ansCount);
                let utente = data.risposta.row;
                let risposta = data.risposta.answer;
                let ins = false;
                $("#table-ans > tbody > tr > td").each(function(index) {
                    let el = $(this);
                    if (index % 3 === 0 && risposta === "V") {
                        if (empty(el)) {
                            el.text(utente.nome.escape() + " " + utente.cognome.escape());
                            ins = true;
                            trueCount++;
                        }
                    } else if (index % 3 === 2 && risposta === "F") {
                        if (empty(el)) {
                            el.text(utente.nome.escape() + " " + utente.cognome.escape());
                            ins = true;
                            falseCount++;
                        }
                    }

                    if (ins) return false;
                })

                if (!ins) {
                    if (risposta === "V") {
                        $("#table-ans > tbody").append(`<tr><td>${utente.nome.escape() + " " + utente.cognome.escape()}</td><td></td><td></td></tr>`)
                        trueCount++;
                    } else {
                        $("#table-ans > tbody").append(`<tr><td></td><td></td><td>${utente.nome.escape() + " " + utente.cognome.escape()}</td></tr>`)
                        falseCount++;
                    }
                }

                $("#true-count").text(trueCount);
                $("#false-count").text(falseCount);
            }
        }
    }

    function resetView() {
        falseCount = 0;
        trueCount = 0;
        ansCount = 0;

        $("#true-count").text(trueCount);
        $("#false-count").text(falseCount);
        $("#ans-count").text(ansCount);
        $("#table-ans > tbody").html('');
        $("#users-count").text(usersCount);
    }

    function empty(el) {
        if (el.html() == "" && el.text() == "") {
            return true;
        }

        return false;
    }

    function refreshUsersList() {
        $("#users-list").html(`<li class="list-group-item" style="color: #fff !important">Nessun utente collegato</li>`);
        if ($("#users-list").length > 0 && users.length > 0) {
            $("#users-list").html('');
            users.forEach((user, index) => {
                $("#users-list").append(`
                    <li class="list-group-item" style="color: #fff !important">
                        ${user.nome.escape()} ${user.cognome.escape()} - ${index + 1}
                        ${user.bool  
                            ? `<button style="font-size: 20px" class="btn btn-link p-0 float-right btn-ban-user" title="Rimuovi utente per la sessione corrente" data-id="${user.id.escape()}" type="button"><i class='bx bxs-user-minus bx-md'></i></button>`
                            : '<small class="float-right text-white">Disconnnesso</small>'
                        }
                    </li>
                `);
            })
        }
    }

    const prf = new WebSocket("wss://" + location.hostname + ":60000");
    const prfID = setInterval(() => prf.send("alive"), 1000);
    prf.onclose = () => clearInterval(prfID);
    prf.onopen = e => {
        prf.send('{ "type": "prof", "id": "<?= htmlspecialchars($autoscuola->id, ENT_QUOTES, 'UTF-8') ?>" }');
    }

    $(function() {
        $("#btn-extra-question").click(function() {
            prf.send(JSON.stringify({
                domanda: {
                    session_id: genUUID(),
                    row: false
                }
            }));
            resetView();
        })

        $("#btn-reset-view").click(function() {
            resetView();
        })

        $("#btn-view-users").click(function() {
            $("#users-list-container").remove();
            $("body").append(`
                <div class="w-100 h-100 position-fixed" style="top: 0; background-color: #00000086; z-index: 99999; padding: 0 10px;" id="users-list-container">
                    <div class="shadow" style="max-width: 700px; margin: 130px auto 20px; font-size: 24px;">
                        <ul class="list-group" id="users-list"></ul>
                    </div>
                </div>
            `)

            refreshUsersList();
        })

        $(document).on("click", ".btn-ban-user", function() {
            let that = $(this);

            $(this).html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span class="sr-only">Loading...</span>
            `)

            adm.send(JSON.stringify({
                ban: {
                    id: that.attr("data-id")
                }
            }))

            users.forEach(user => {
                if (user.id == $(this).attr("data-id")) {
                    user.bool = false;
                    return false;
                }
            })

            //refreshUsersList();
        })

        $("#btn-reload-session").click(function() {
            adm.send("close");
            location.reload();
        })

        $(document).mousedown(function(e) {
            if ($("#users-list-container").length > 0 && $(e.target).attr("id") != "users-list" && $(e.target).parents("#users-list-container").length == 0 && $(e.target).attr("id") != "btn-view-users") {
                $("#users-list-container").remove();
            }
        })

        $("#btn-close-session").click(function() {
            adm.send("close");
            usersCount = 0;
            users = [];
            resetView();
            $(this).text("Apri sessione");
            $(this).removeClass("btn-danger");
            $(this).addClass("btn-success");
            $(this).attr("id", "#btn-open-session");
        })

        $(document).on("click", "#btn-open-session", function() {
            location.reload();
        })
    })

    function showCustomAlert(msg) {
        let alertHtml = `<div class="custom-alert">${msg}</div>`;
        $("body").append(alertHtml);
    }
</script>
<style>
    html .content.app-content {
        overflow: overlay !important;
    }

    .table-sm thead>tr>th {
        padding: 0 !important;
    }

    .custom-alert {
        position: absolute;
        top: -100px;
        opacity: 0;
        z-index: -1;
        transition: all 0.2 ease-in-out;
        background-color: #000;
        border-radius: 5px;
        padding: 5px;
        width: fit-content;
    }
</style>