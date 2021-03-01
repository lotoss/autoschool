
const { scuola, tag } = require("./lib.js");
const colors = require("colors/safe");
const { Server } = require("ws");

new Server({ server: require("../index.js") }).on("connection", async socket => {
    try
    {
        //| Connessione
        const meta = await scuola.add(socket);
        if (!meta) return socket.close();
        const { id, container, type } = meta;
        container.print(socket, "Connesso...", "magenta");

        //| Reinvio dati sessione corrente
        const { domanda } = container;
        if (domanda)
            socket.send(domanda);
        if (type == "admin")
            for (const msg of Object.values(container.risposte))
                socket.send(msg);

        //| Ricezione messaggi
        socket.on("message", async msg => {
            if (msg == "alive");
            else if (msg == "close" && type == "admin")
            {
                //| Chiusura forzata test
                container.print(socket, "Chiusura...", "brightRed");
                container.close(); // La chiusura di tutti i socket attiverà l'evento di reset di base
            }
            else
            {
                //| Reindirizzamento del messaggio
                const obj = JSON.parse(msg);
                if (obj.ban && type == "admin")
                {
                    const { id, bool = true } = obj.ban;
                    container.ban(id, bool);
                    container.print(socket, `${ bool ? "Bannato " : "Sbannato" } ${ colors.gray("[" + colors.cyan(id) + "]") }`, "brightRed");
                }
                else
                {
                    container.print(socket, obj)
                    if (obj.domanda) obj.domanda.row ??= await scuola.first("SELECT * FROM domande WHERE id = ? AND id_gruppo = ? LIMIT 1", [ obj.domanda.id_domanda, obj.domanda.id_gruppo ]);
                    else if (obj.risposta) obj.risposta.row = meta.row;
                    container.send(id, obj);
                }
            }
        });
    }
    catch (e)
    {
        tag("errore", "bgRed");
        console.error(e);
        tag("/errore", "bgRed");
    }
});