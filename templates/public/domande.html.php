    <div class="pos-f-t">
        <div class="collapse" id="menu-navbar">
            <div class="bg-warning p-4">
                <h5 class="text-dark h4"><?= htmlspecialchars($studente->nome . ' ' . $studente->cognome, ENT_QUOTES, 'UTF-8') ?></h5>
                <div class="text-dark">Patente: <?= htmlspecialchars($studente->tipo_esame, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="text-dark">Data esame: <?= !empty($studente->data_esame_teoria) ? date('d/m/Y', strtotime($studente->data_esame_teoria)) : 'non impostata' ?></div>
            </div>
        </div>
        <nav class="navbar navbar-light bg-warning">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menu-navbar" aria-controls="menu-navbar" aria-expanded="false" aria-label="Toggle menu navbar">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>
    </div>
    <div class="container">
        <div class="question-container">
            <div id="question-content">
                <h6 class="text-success w-100"><i class='bx bx-wifi'></i>Connesso...</h6>
                <h5>In attesa della domanda...</h5>
                <div class="d-flex justify-content-center">
                    <div class="spinner-border spinner-border-sm text-white" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
        <span class="text-warning mb-3 d-block" id="btn-listen-question"><i class='bx bxs-volume-full bx-md'></i></span>
        <div class="d-flex justify-content-between w-100 pb-2">
            <button type="button" class="btn btn-lg btn-secondary btn-ans mr-2" id="btn-true" disabled>VERO</button>
            <button type="button" class="btn btn-lg btn-secondary btn-ans" id="btn-false" disabled>FALSO</button>
        </div>
    </div>
    <script>
        var session_id;
        var lastQuestion = "";

        const usr = new WebSocket("wss://" + location.hostname + ":60000");
        usr.onopen = e => {
            usr.send('{ "type": "user", "id": "<?= htmlspecialchars($studente->id, ENT_QUOTES, 'UTF-8') ?>" }');
            const usrID = setInterval(() => usr.send("alive"), 1000);
            usr.onclose = () => {
                lastQuestion = "";
                $("#question-content").html(`
                    <h4 class="text-danger w-100"><i class='bx bx-wifi-off'></i>Errore di connessione...</h4>
                    <small class="border d-block rounded mt-3 p-2">Ricarica la pagina.<br>Se risulti disconnesso anche dopo aver ricaricato la pagina è possibile che il docente ti abbia rimosso dalla sessione attuale.</small>
                `);
                $('.btn-ans').prop('disabled', true);
                clearInterval(usrID);
            }

            usr.onmessage = msg => {
                let data = JSON.parse(msg.data);
                if (data.domanda) {
                    console.log(data.domanda);
                    $("#btn-false").removeClass("btn-danger");
                    $("#btn-true").removeClass("btn-success");
                    session_id = data.domanda.session_id;
                    let domanda = data.domanda.row;
                    if (domanda) {
                        lastQuestion = domanda.domanda;
                        $("#question-content").html(`${domanda.id_immagine ? `<img src="/img/segnali/${domanda.id_immagine}.jpg" alt="Immagine ${domanda.id_immagine}" class="view-img mb-3 rounded" style="max-width: 160px">` : ''} <p>${domanda.domanda.escape()}</p>`);
                        if (localStorage.getItem('session_id') && localStorage.getItem('session_id') == session_id) {
                            $('.btn-ans').prop('disabled', true);
                            if (localStorage.getItem('last_answer') === 'V') {
                                $("#btn-true").addClass("btn-success");
                            } else if (localStorage.getItem('last_answer') === 'F') {
                                $("#btn-false").addClass("btn-danger");
                            }
                            // $('#question-content').append(`<h6 class="text-muted txt-info">Hai risposto <u>${localStorage.getItem('last_answer') == 'V' ? 'VERO' : 'FALSO'}</u></h6>`);
                            $('#question-content').append(`
                                <div class="d-flex justify-content-center txt-info mt-4">
                                    <div>
                                        <small class="text-secondary">In attesa di un'altra domanda...</small>
                                        <div class="d-block mt-2 mx-auto spinner-border spinner-border-sm text-secondary" role="status"></div>
                                    </div>
                                </div>
                            `);
                        } else {
                            $('.btn-ans').prop('disabled', false);
                        }
                    } else if (domanda === false) {
                        lastQuestion = "La domanda non ha il testo, rispondi per inviare una risposta al docente";

                        $("#question-content").html(`<p>${lastQuestion}</p>`);
                        if (localStorage.getItem('session_id') && localStorage.getItem('session_id') == session_id) {
                            $('.btn-ans').prop('disabled', true);
                            if (localStorage.getItem('last_answer') === 'V') {
                                $("#btn-true").addClass("btn-success");
                            } else if (localStorage.getItem('last_answer') === 'F') {
                                $("#btn-false").addClass("btn-danger");
                            }
                            // $('#question-content').append(`<h6 class="text-muted txt-info">Hai risposto <u>${localStorage.getItem('last_answer') == 'V' ? 'VERO' : 'FALSO'}</u></h6>`);
                            $('#question-content').append(`
                                <div class="d-flex justify-content-center txt-info mt-4">
                                    <div>
                                        <small class="text-secondary">In attesa di un'altra domanda...</small>
                                        <div class="d-block mt-2 mx-auto spinner-border spinner-border-sm text-secondary" role="status"></div>
                                    </div>
                                </div>
                            `);
                        } else {
                            $('.btn-ans').prop('disabled', false);
                        }
                    }
                }
            };
        };

        $(function() {
            $("#btn-true").click(function() {
                localStorage.setItem('last_answer', 'V');
                localStorage.setItem('session_id', session_id);
                usr.send(JSON.stringify({
                    risposta: {
                        answer: 'V'
                    }
                }));
                $(this).addClass("btn-success");
            })

            $("#btn-false").click(function() {
                localStorage.setItem('last_answer', 'F');
                localStorage.setItem('session_id', session_id);
                usr.send(JSON.stringify({
                    risposta: {
                        answer: 'F'
                    }
                }));
                $(this).addClass("btn-danger");
            })

            $("#btn-listen-question").click(function() {
                speech(lastQuestion);
            })

            $('.btn-ans').click(function() {
                $('.btn-ans').prop('disabled', true);
                // $('#question-content').append(`<h6 class="text-muted txt-info">Hai risposto <u>${$(this).text().escape()}</u></h6>`);
                $('#question-content').append(`
                    <div class="d-flex justify-content-center txt-info mt-4">
                        <div>
                            <small class="text-secondary">In attesa di un'altra domanda...</small>
                            <div class="d-block mt-2 mx-auto spinner-border spinner-border-sm text-secondary" role="status"></div>
                        </div>
                    </div>
                `);
            });
        })

        function speech(text) {
            var msg = new SpeechSynthesisUtterance(text);
            window.speechSynthesis.speak(msg);
        }
    </script>
    <style>
        body {
            background-color: #212121;
            color: #fafafa;
        }

        .question-container {
            width: 100%;
            display: flex;
            align-items: center;
            min-height: 65vh;
            font-size: 1.2em;
        }

        #question-content {
            width: inherit;
            text-align: center;
        }

        #question-content>p {
            text-align: center;
            width: 100%;
        }

        .btn-ans {
            width: 100%;
            font-size: 22px;
        }

        .btn-ans:focus,
        .btn-ans:active {
            outline: none;
            box-shadow: none;
        }

        #question-text {
            font-size: 18px;
            color: #333;
            line-height: 1.3;
        }

        .bx {
            margin-right: 4px;
        }
    </style>