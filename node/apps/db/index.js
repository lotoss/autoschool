
//[Dependencies]: jsdom mysql2 form-data node-fetch
const fs = require("fs");
const fetch = require("node-fetch");
const FormData = require("form-data");
const { JSDOM } = require("jsdom");
const state = (str, col, ...args) => console.log(`\x1b[${ col }m${ str }\x1b[0m`, ...args);

async function selector(opts)
{
    var url = opts.url, body;
    const { get, post } = opts;
    if (get)
    {
        url = new URL(url);
        for (const key in get) url.searchParams.set(key, get[key]);
        url = url.href;
    }
    if (post)
    {
        body = new FormData();
        for (const key in post) body.append(key, post[key]);
    }
    const page = await fetch(url, {
        headers: { "User-Agent": "Node-Fetch" }, // Evita l'errore 403 (Forbidden)
        method: "post",
        body
    });
    return Object.assign(Array.from(new JSDOM(await page.text()).window.document.querySelectorAll(opts.query)), { status: page.status });
}

async function dex(opts)
{
    const result = await selector(opts);
    if (result.status == 200)
    {
        var text = "";
        const { post } = opts;
        for (const e of result)
            if (e.tagName == "HR") break;
            else text += (text == "" ? "" : "\n") + e.textContent.trim();
        out.push({ id: post.question_category_id * 1000 + post.question_id, text });
    }
    else if (result.status == 500)
    {
        retry.push(opts);
        return false;
    }
    return true;
}

const out = [];
const retry = [];
state("start", 94);
selector({
    url: "https://www.quizpatente3d.it/domande-ufficiali-b",
    query: "div.passed-block.mt15 div.row a"
}).then(async html => {

    //| Descrizioni
    await Promise.all(html.map(async (x, n) => {
        const length = parseInt((await selector({ url: x.href, query: "input[type='number']" }))[0].placeholder.match(/(\d+)\)$/)[1]);
        await Promise.all(Array.from({ length }, async (_, i) => {
            await dex({
                url: "https://www.quizpatente3d.it/questions/searchsubcategory",
                query: ".panel:nth-child(3) .panel-body *",
                post: {
                    question_id: i + 1,
                    category_id: 2,
                    question_category_id: 10 + (n + 1)
                }
            });
        }));
    }));

    //| Tentativi di risoluzione errori
    const l = retry.length;
    state("errors:", 91, l);
    while(retry.length)
    {
        const opts = retry.shift(); // Coda: Prende dall'inizio e butta gli errori, da "dex()", sul fondo
        if (await dex(opts)) console.log(l - retry.length, "/", l);
        else state("err", 95, opts.post);
    }

    //| Scrittura file
    state("loaded", 93, out);
    fs.writeFileSync("out.json", JSON.stringify(out, null, 2));
}).catch(e => { throw e; });