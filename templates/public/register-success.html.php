<div class="panel-container mt-5">
    <?php if (!$user->email_verificata) : ?>
        <div class="panel border rounded p-3 shadow-sm">
            <h5>Benvenuto <?= $user->nome ?>!</h5>
            <p>
                Ti abbiamo appena inviato una mail all'indirizzo email che hai utilizzato per registrati poco fa.
                <br>In quest'ultima troverai un link per l'attivazione dell'account, che dovrai seguire e
                successivamente si aprirà una pagina di login da dove potrai finalmente effettuare l'accesso a questo sito.
            </p>
        </div>
        <div class="p-3 border rounded mt-3">
            Non hai ricevuto la mail? <button class="btn btn-link" id="send-auth-email-code">Invia di nuovo</button>
            <div class="text-success spinner-border spinner-border-sm ml-2" id="load-email-request" style="display: none" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <i id="ok-status-icon" class="far fa-check-circle text-success ml-2" style="display: none"></i>
        </div>
    <?php else : ?>
        <div class="panel border rounded p-3 shadow-sm">
            <h5>Ciao <?= $user->nome ?>!</h5>
            <p>
                Il tuo account è già attivo! <a href="/login">Clicca qui per accedere</a>
            </p>
        </div>
    <?php endif; ?>
</div>
<script>
    $(function() {
        $("#send-auth-email-code").click(function() {
            if (!timer) {
                var userId = new URLSearchParams(window.location.search).get('id');
                $("#load-email-request").fadeIn(100);
                fetch(`/send-auth-mail?user_id=${userId}`)
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                    })
                    .then(data => {
                        $(".alert-danger").remove();
                        $("#load-email-request").hide();
                        if (data.error) {
                            $("#send-auth-email-code").parent().before(`
                            <div class="alert alert-danger mt-3" role="alert">
                                ${data.error}
                            </div>`)
                        } else {
                            $("#send-auth-email-code").prop('disabled', true);
                            $("#ok-status-icon").fadeIn(200)
                            setTimeout(() => {
                                $("#ok-status-icon").fadeOut(200);
                            }, 2000);

                            $("#send-auth-email-code").after(`
                                <span>fra <span id="timer"></span> secondi</span>
                            `)
                            $("#timer").text('60');
                            timer = window.setInterval(function() {
                                setTimer();
                            }, 1000); // every second
                        }
                    })
                    .catch(error => console.warn('Si è verificato un errore'))
            }
        })

        var seconds = 60;
        var timer;

        function setTimer() {
            if (seconds < 60) { // I want it to say 1:00, not 60
                $("#timer").text(seconds);
            }
            if (seconds > 0) { // so it doesn't go to -1
                seconds--;
            } else {
                clearInterval(timer);
                $("#timer").parent().remove();
                seconds = 60;
                timer = null;
                $("#send-auth-email-code").prop('disabled', false);
            }
        }
    })
</script>