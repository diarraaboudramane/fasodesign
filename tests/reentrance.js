// Retenir une suggestion ne doit pas reveiller le filtrage, sinon
// la liste se rouvre juste apres avoir ete fermee.

const fs = require("fs");
const src = fs.readFileSync(
  require("path").join(__dirname, "..", "assets", "js", "faso.js"),
  "utf8");

globalThis.Event = class { constructor(type) { this.type = type; } };

const debut = src.indexOf("var ECRITURE_SCRIPT");
const fin = src.indexOf("\n  }", src.indexOf("function signalerSaisie")) + 4;
eval(src.slice(debut, fin).replace(/^ {2}/gm, ""));

let filtrages = 0;
let recus = [];

// Champ minimal qui rejoue nos deux ecouteurs reels
const champ = {
  value: "",
  dispatchEvent(ev) {
    recus.push(ev.type);
    if (ev.type === "input") {
      // écouteur de l'autocomplétion, tel qu'il est désormais écrit
      if (ECRITURE_SCRIPT) return true;
      filtrages++;
    }
    return true;
  },
};

// 1. Frappe réelle : le filtrage doit se déclencher
champ.dispatchEvent(new Event("input"));
const apresFrappe = filtrages;

// 2. Suggestion retenue : la charte écrit puis signale
recus = [];
champ.value = "Ouagadougou";
signalerSaisie(champ);
const apresChoix = filtrages;

let ko = 0;
console.log("  frappe réelle              -> filtrages :", apresFrappe);
console.log("  suggestion retenue         -> filtrages :", apresChoix - apresFrappe);
console.log("  événements émis au choix   :", recus.join(", "));
console.log("  drapeau après émission     :", ECRITURE_SCRIPT);

if (apresFrappe !== 1) { console.log("  ECHEC : une frappe réelle doit filtrer"); ko++; }
if (apresChoix !== apresFrappe) { console.log("  ECHEC : la liste se rouvre après sélection"); ko++; }
if (recus.join(",") !== "input,change") { console.log("  ECHEC : les deux événements ne sont pas émis"); ko++; }
if (ECRITURE_SCRIPT !== false) { console.log("  ECHEC : le drapeau reste levé"); ko++; }

console.log();
console.log(ko ? `  ${ko} echec(s)`
  : "  Le cadriciel est notifié, nos écouteurs ne le sont pas.");
process.exit(ko ? 1 : 0);
