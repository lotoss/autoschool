<div class="d-flex justify-content-center" style="font-size: 30px;">
    <div class="bg-secondary mb-1 px-2 rounded-pill"><span id="true-count" style="color:#fff !important">0</span> - <span id="false-count" style="color:#fff !important">0</span></div>
</div>
<table class="table table-striped table-bordered" id="table-ans" style="font-size: 30px">
    <thead class="text-center">
        <th style="font-size: 45px; color: #20c997 !important; width: 50%">Vero</th>
        <th style="font-size: 45px; color: #ff5b5c !important;  width: 50%">Falso</th>
    </thead>
    <tbody>
        <tr>
            <td class="text-center" colspan="2" id="no-ans-feedback">Nessuna risposta</td>
        </tr>
    </tbody>
</table>
<script>
    const adm = new WebSocket("wss://" + location.hostname + ":60000");
    var falseCount = 0;
    var trueCount = 0;

    adm.onopen = e => {
        adm.send('{ "type": "admin", "id": "<?= htmlspecialchars($autoscuola->id, ENT_QUOTES, 'UTF-8') ?>" }');
        const admID = setInterval(() => adm.send("alive"), 1000);
        adm.onclose = () => clearInterval(admID);

        adm.onmessage = msg => {
            var domanda = JSON.parse(msg.data).domanda?.row;
            var utente = JSON.parse(msg.data).risposta?.row;
            var risposta = JSON.parse(msg.data).risposta?.answer;

            if (domanda) {
                falseCount = 0;
                trueCount = 0;

                $("#true-count").text(trueCount);
                $("#false-count").text(falseCount);
            }

            if (risposta) {
                if ($("#no-ans-feedback").length > 0) $("#table-ans > tbody").html("<tr><td></td><td></td></tr>");
                let ins = false;
                $("#table-ans > tbody > tr > td").each(function(index) {
                    let el = $(this);
                    if (index % 2 === 0 && risposta === "V") {
                        if (empty(el)) {
                            el.text(utente.nome.escape() + " " + utente.cognome.escape());
                            ins = true;
                            trueCount++;
                        }
                    } else if (index % 2 === 1 && risposta === "F") {
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
                        $("#table-ans > tbody").append(`<tr><td>${utente.nome.escape() + " " + utente.cognome.escape()}</td><td></td></tr>`)
                        trueCount++;
                    } else {
                        $("#table-ans > tbody").append(`<tr><td></td><td>${utente.nome.escape() + " " + utente.cognome.escape()}</td></tr>`)
                        falseCount++;
                    }
                }

                $("#true-count").text(trueCount);
                $("#false-count").text(falseCount);
            }
        }
    }

    function empty(el) {
        if (el.html() == "" && el.text() == "") {
            return true;
        }

        return false;
    }
</script>