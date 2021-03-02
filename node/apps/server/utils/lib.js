
const colors = require("colors/safe");
const mysql = require("mysql2/promise");
const util = require("util");

//| Formatta una stringa per limitare la sua lunghezza a "k"
function truncate(str, k)
{
    const length = str.length;
    if (length < k) str += " ".repeat(k - length);
    else if (length > k) str = str.substr(0, k - 3) + "...";
    return str;
}

//| Stampa un messaggio generico
function tag(str, num, outer, inner = "yellow", autoclose = true)
{
    console.log(
        colors.gray("<") +
        colors[outer ?? num](str) +
        (
            outer != null ?
            (   
                colors.gray("[") +
                colors[inner](num) +
                colors.gray("]")
            ) : ""
        ) +
        colors.gray((autoclose ? " /" : "") + ">")
    );
}

//| Immagazzina una scuola
class scuola
{
    //| Operazioni globali
    static db = null;
    static data = {};

    static async first(sql, args)
    {
        if (!scuola.db) // Ripristina eventualmente la connessione al 'DataBase'
        {
            scuola.db = await mysql.createConnection({
                host: "81.88.52.143",
                user: "to477gpz",
                password: "RosiGay1",
                database: "to477gpz_scuolaguida"
            });
            scuola.db.on("error", () => scuola.db = null);
        }
        const [ result ] = await scuola.db.execute(sql, args);
        return result?.length && result[0];
    }

    static head({ id, type, container: { key } })
    {
        return colors.white(
            colors.cyan(type.toLowerCase().padEnd(5, " ")) +
            colors.gray(`[${ colors.brightGreen(truncate(id, 8)) }]`) +
            "@" +
            colors.yellow(truncate(key, 8)) +
            ">"
        );
    }

    static async add(sock, meta)
    {
        const row = meta.row = await scuola.first(`SELECT * FROM ${ meta.type == "admin" || meta.type == "prof" ? "autoscuole" : "studenti" } WHERE id = ? LIMIT 1`, [ meta.id ]);
        if (!row) return tag("unknown", "brightMagenta") ?? null;;
        const key = row.id_autoscuola ?? row.id;
        const container = meta.container = scuola.data[key] ??= new scuola(key);
        if (container.banned.has(meta.id)) return tag("banned", meta.id, "brightMagenta", "brightGreen") ?? null;
        container.clients.set(sock, meta);
        meta.head = scuola.head(meta);
        return meta;
    }

    //| Operazioni su scuola singola
    risposte = {};
    domanda = null;
    banned = new Set();
    clients = new Map();
    
    constructor(key) { this.key = key }
    
    get size()
    {
        return this.clients.size
    }
    
    close()
    {
        for (const [sock] of this.clients) sock.close();
    }

    remove(sock)
    {
        this.clients.delete(sock);
        if (this.size == 0)
        {
            delete scuola.data[this.key];
            tag("reset", this.key, "brightRed");
        }
    }


    print(sock, msg, col = null)
    {
        console.log(
            this.clients.get(sock).head,
            col
            ? colors[col](msg)
            : util.inspect(msg, true, null, true)
        );
    }

    ban(id, bool)
    {
        if (bool)
        {
            this.banned.add(id);
            for (const [sock, meta] of this.clients) 
                if (meta.id == id)
                    sock.close();
        }
        else this.banned.delete(id);
    }

    send(id, obj)
    {
        const msg = JSON.stringify(obj);
        if (obj.risposta)
            if (!this.risposte[id])
                this.risposte[id] = msg;
            else return;
        else if (obj.domanda) [this.domanda, this.risposte] = [msg, {}];
        else if (obj.utente);
        else return;
        for (const [sock, { type }] of this.clients) if (type == "admin" || (type == "user" && obj.domanda))
            sock.send(msg);
    }
};

module.exports = { scuola, tag, truncate };