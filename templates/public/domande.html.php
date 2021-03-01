    <nav class="navbar navbar-dark bg-dark">
        <span class="navbar-brand">
            <i class='bx bxs-user'></i>
            <?= htmlspecialchars($studente->nome . ' ' . $studente->cognome . ' - ' . $studente->tipo_esame, ENT_QUOTES, 'UTF-8') ?></span>
    </nav>
    <div class="px-4" style="min-height: 400px;">
        <div class="container position-relative p-0" style="height: 75vh; max-width: 500px">
            <div class="question-container">
                <div class="question-content text-center mt-3" id="question-content">
                    <p>In attesa della domanda...</p>
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border spinner-border-sm text-secondary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="position-absolute w-100 d-flex justify-content-between" style="bottom: 0px">
                <button class="btn btn-success btn-ans mr-2" id="btn-true" disabled>VERO</button>
                <button class="btn btn-danger btn-ans" id="btn-false" disabled>FALSO</button>
            </div>
        </div>
    </div>
    <script>
        var session_id;

        const usr = new WebSocket("wss://" + location.hostname + ":60000");
        usr.onopen = e => {
            usr.send('{ "type": "user", "id": "<?= htmlspecialchars($studente->id, ENT_QUOTES, 'UTF-8') ?>" }');
            const usrID = setInterval(() => usr.send("alive"), 1000);
            usr.onclose = () => clearInterval(usrID);

            usr.onmessage = msg => {
                //ricevo domanda
                var socketData = JSON.parse(msg.data);
                session_id = socketData.domanda.session_id;
                fetch('/getdomanda?id_domanda=' + socketData.domanda.id_domanda + '&id_gruppo=' + socketData.domanda.id_gruppo)
                    .then(response => response.json())
                    .then(data => {
                        $("#question-content").html(`${data.domanda.id_immagine ? `<img src="/img/segnali/${data.domanda.id_immagine}.jpg" alt="Immagine ${data.domanda.id_immagine}" class="view-img w-50 mb-3" style="max-width: 150px">` : ''} <p>${data.domanda.domanda.escape()}</p>`);

                        if (localStorage.getItem('session_id') && localStorage.getItem('session_id') == socketData.domanda.session_id) {
                            $('.btn-ans').prop('disabled', true);
                            $('#question-content').append(`<h6 class="text-muted txt-info">Hai risposto <u>${localStorage.getItem('last_answer') == 'V' ? 'VERO' : 'FALSO'}</u></h6>`);
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
                    });
            };
        };

        $(function() {
            $("#btn-true").click(function() {
                localStorage.setItem('last_answer', 'V');
                localStorage.setItem('session_id', session_id);
                usr.send(JSON.stringify({
                    risposta: {
                        answer: 'V',
                        session_id
                    }
                }));
            })

            $("#btn-false").click(function() {
                localStorage.setItem('last_answer', 'F');
                localStorage.setItem('session_id', session_id);
                usr.send(JSON.stringify({
                    risposta: {
                        answer: 'F',
                        session_id
                    }
                }));
            })

            $('.btn-ans').click(function() {
                $('.btn-ans').prop('disabled', true);
                $('#question-content').append(`<h6 class="text-muted txt-info">Hai risposto <u>${$(this).text().escape()}</u></h6>`);
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
    </script>
    <style>
        .question-container {
            position: relative;
            margin: 2vh 0;
            height: 55vh;
            width: 100%;
        }

        .question-content {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            transform: translate(-50%, -50%);
        }

        .btn-ans {
            width: 90%;
            font-size: 22px;
            left: 0;
            border-radius: 8px;
            padding: 12px 15px;
        }

        #question-text {
            font-size: 18px;
            color: #333;
            line-height: 1.3;
        }
    </style>