
//[WIP]: Rimuovi doppia riposta, (Controlla il session id), Ban (Non fa), Struttura generale (Brutta)
//[Dependencies]: colors express mysql2 ws
const colors = require("colors/safe");
const express = require("express");
const https = require("https");
const fs = require("fs");

//| Pagina Web
const app = express();
app.use((req, res) => res.sendFile("/index.html", { root: __dirname }));
module.exports = https.createServer({
    key: fs.readFileSync("./utils/res/key.pem"),
    cert: fs.readFileSync("./utils/res/cert.pem")
}, app).listen(process.env.PORT || 60000);


//| Server
require("./utils/server.js");

console.clear();
console.log(colors.bgWhite(colors.black("Acceso.")));