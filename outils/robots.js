#!/usr/bin/env node
/**
 * Charte graphique de l'administration burkinabè
 * Robots, collecteurs et aspirateurs : une seule liste, trois sorties
 *
 *   node outils/robots.js              écrit les fichiers
 *   node outils/robots.js --verifier   n'écrit rien, sort en 1 si un
 *                                      fichier ne correspond plus
 *
 * La liste des agents refusés doit exister à trois endroits : le
 * robots.txt que les collecteurs polis vont lire, la configuration
 * nginx et le .htaccess. Tenue à la main, elle diverge : on ajoute un
 * agent au robots.txt, on oublie le serveur, et le refus n'est plus
 * qu'une demande écrite. Elle est donc tenue ici, et recopiée.
 *
 * CE QUE CHAQUE COUCHE ARRÊTE RÉELLEMENT
 *
 *   robots.txt      Rien. C'est une demande. Les collecteurs des
 *                   grandes maisons la respectent parce qu'il leur en
 *                   coûterait de se faire prendre ; les autres la
 *                   lisent pour savoir où regarder.
 *
 *   Refus par nom   Ce qui s'annonce. Un collecteur qui déclare
 *                   GPTBot reçoit 403. Un collecteur qui se déclare
 *                   Firefox passe, et rien ici ne l'en empêche.
 *
 *   Débit limité    Ce qui insiste. C'est la seule couche qui agisse
 *                   sur un agent non déclaré, et elle protège le
 *                   serveur plus qu'elle ne protège le contenu.
 *
 *   En-tête TDM     Rien, techniquement. Elle réserve les droits de
 *                   fouille au sens du protocole TDM du W3C : sa
 *                   portée est juridique, pas technique.
 *
 * Aucune de ces couches n'empêche de lire une page publique. Un site
 * de documentation est fait pour être lu, y compris par un agent qui
 * ment sur son identité. Ce qui est atteignable, et ce que ceci fait :
 * écarter les collecteurs déclarés, réserver les droits de fouille, et
 * garder le serveur debout sous un collecteur mal réglé.
 */
"use strict";

const fs = require("fs");
const path = require("path");

const RACINE = path.join(__dirname, "..");
const HEBERGEMENT = path.join(RACINE, "hebergement");

/* ================================================== les agents */

/*
 * « robotsSeulement » distingue les jetons qui n'existent que dans le
 * robots.txt. Google-Extended et Applebot-Extended ne sont pas des
 * visiteurs : aucune requête ne porte ce nom. Ce sont des réglages que
 * Google et Apple lisent dans le robots.txt pour savoir s'ils peuvent
 * réemployer ce que Googlebot et Applebot ont déjà rapporté. Les
 * inscrire dans la configuration du serveur ne bloquerait rien et
 * laisserait croire le contraire.
 */
const GROUPES = [
  {
    cle: "entrainement",
    titre: "Collecte pour l'entraînement de modèles",
    motif:
      "Ces collecteurs ramassent les pages pour en nourrir des modèles.\n" +
      "La documentation de la charte est publique, mais sa reprise dans un\n" +
      "modèle n'est pas une publication : elle en détache le contenu de son\n" +
      "autorité et de sa version. Refusée.",
    agents: [
      { nom: "GPTBot", note: "OpenAI" },
      { nom: "ClaudeBot", note: "Anthropic" },
      { nom: "anthropic-ai", note: "Anthropic, ancien nom" },
      { nom: "Claude-Web", note: "Anthropic, ancien nom" },
      { nom: "CCBot", note: "Common Crawl, source de nombreux corpus" },
      { nom: "Google-Extended", note: "Gemini", robotsSeulement: true },
      { nom: "Applebot-Extended", note: "Apple Intelligence", robotsSeulement: true },
      { nom: "Meta-ExternalAgent", note: "Meta" },
      { nom: "FacebookBot", note: "Meta" },
      { nom: "Bytespider", note: "ByteDance" },
      { nom: "Amazonbot", note: "Amazon" },
      { nom: "PerplexityBot", note: "Perplexity" },
      { nom: "Diffbot", note: "revente de données extraites" },
      { nom: "Omgilibot", note: "revente de corpus" },
      { nom: "Omgili", note: "revente de corpus" },
      { nom: "cohere-ai", note: "Cohere" },
      { nom: "AI2Bot", note: "Allen Institute" },
      { nom: "ImagesiftBot", note: "collecte d'images" },
      { nom: "Timpibot", note: "Timpi" },
      { nom: "YouBot", note: "You.com" },
      { nom: "PanguBot", note: "Huawei" },
      { nom: "Kangaroo Bot", note: "collecte de corpus" },
      { nom: "Webzio-Extended", note: "Webz.io" },
      { nom: "SemrushBot-OCOB", note: "Semrush, collecte pour modèles" },
      { nom: "Scrapy", note: "bibliothèque d'extraction, souvent laissée telle quelle" },
    ],
  },
  {
    cle: "assistants",
    titre: "Assistants, requête déclenchée par une personne",
    motif:
      "Ceux-ci ne collectent pas : ils vont chercher une page parce qu'une\n" +
      "personne vient de la demander à un assistant. Les refuser refuse à\n" +
      "un agent de l'administration de faire lire la documentation par\n" +
      "l'outil qu'il emploie. Refusés ici parce que la commande était de\n" +
      "refuser l'IA ; c'est le groupe à rouvrir en premier si l'usage\n" +
      "interne le demande, en retirant ce bloc de la liste.",
    agents: [
      { nom: "ChatGPT-User", note: "OpenAI, à la demande d'une personne" },
      { nom: "OAI-SearchBot", note: "OpenAI, index de recherche" },
      { nom: "Claude-User", note: "Anthropic, à la demande d'une personne" },
      { nom: "Claude-SearchBot", note: "Anthropic, index de recherche" },
      { nom: "Perplexity-User", note: "Perplexity, à la demande d'une personne" },
      { nom: "MistralAI-User", note: "Mistral, à la demande d'une personne" },
      { nom: "DuckAssistBot", note: "DuckDuckGo" },
    ],
  },
  {
    cle: "aspirateurs",
    titre: "Aspirateurs de site",
    motif:
      "Outils de recopie intégrale. Ils tirent le site entier en quelques\n" +
      "secondes et servent le plus souvent à en monter une copie ailleurs.\n" +
      "Une copie de la charte sous un autre domaine est un faux site de\n" +
      "l'administration.",
    agents: [
      { nom: "HTTrack" },
      { nom: "WebCopier" },
      { nom: "Teleport" },
      { nom: "TeleportPro" },
      { nom: "WebZIP" },
      { nom: "Offline Explorer" },
      { nom: "SiteSnagger" },
      { nom: "WebReaper" },
      { nom: "Xenu" },
      { nom: "larbin" },
      { nom: "grub-client" },
    ],
  },
  {
    cle: "scanners",
    titre: "Scanners de vulnérabilité",
    motif:
      "Ils ne lisent pas le site, ils le sondent. Les refuser ne protège\n" +
      "de rien — un scanner sérieux ne s'annonce pas — mais vide le\n" +
      "journal du bruit de fond, ce qui rend visible ce qui compte.",
    agents: [
      { nom: "Nikto" },
      { nom: "sqlmap" },
      { nom: "Nmap Scripting Engine" },
      { nom: "masscan" },
      { nom: "zgrab" },
      { nom: "WPScan" },
      { nom: "Nessus" },
      { nom: "acunetix" },
      { nom: "dirbuster" },
      { nom: "gobuster" },
      { nom: "Wfuzz" },
    ],
  },
];

/*
 * Les moteurs restent autorisés, et ce n'est pas un oubli. Un système
 * de conception que personne ne trouve ne sert personne : un
 * développeur d'un ministère cherche « bouton charte burkina » avant
 * de chercher chartegraphique.gov.bf. Les refuser retirerait le site des
 * résultats de recherche sans gêner un seul collecteur d'entraînement.
 *
 * Cette liste ne sert donc qu'à la documentation : rien n'est mis en
 * place pour la faire respecter. Une liste blanche par nom d'agent
 * serait sans valeur, n'importe qui pouvant se déclarer Googlebot ;
 * la vérification se fait par résolution inverse du nom de domaine,
 * côté serveur, et seulement si le besoin s'en présente.
 */
const MOTEURS = [
  { nom: "Googlebot", note: "Google" },
  { nom: "Bingbot", note: "Microsoft" },
  { nom: "DuckDuckBot", note: "DuckDuckGo" },
  { nom: "Qwantbot", note: "Qwant" },
  { nom: "Applebot", note: "Apple, recherche seule" },
  { nom: "Slurp", note: "Yahoo" },
];

/* Chemins qu'aucun collecteur n'a de raison de parcourir. Les pages de
   documentation, elles, ont vocation à être trouvées. */
const CHEMINS_REFUSES = [
  { chemin: "/android/", note: "ressources Android, sans intérêt en recherche" },
  { chemin: "/npm/", note: "dépôt de paquets, si Verdaccio est monté sous ce chemin" },
];

const REFUSES = GROUPES.flatMap((g) => g.agents);
const NOMMABLES = REFUSES.filter((a) => !a.robotsSeulement);

/* ================================================== fabrication */

const AVERTISSEMENT =
  "Engendre par outils/robots.js. Ne pas modifier a la main :\n" +
  "la liste des agents est tenue dans cet outil, et « node\n" +
  "outils/robots.js --verifier » fait partie de la recette.";

function commentaire(texte, marque) {
  return texte.split("\n").map((l) => (l ? marque + " " + l : marque)).join("\n");
}

/* ---------------------------------------------------- robots.txt */

function robotsTxt() {
  const out = [];
  out.push(commentaire(
    "Charte graphique de l'administration burkinabe\n" +
    "https://chartegraphique.gov.bf\n\n" + AVERTISSEMENT, "#"));
  out.push("");

  for (const g of GROUPES) {
    out.push("");
    out.push(commentaire(g.titre + "\n\n" + g.motif, "#"));
    out.push("");
    for (const a of g.agents) {
      /* Le commentaire va au-dessus, jamais en fin de ligne : la RFC
         9309 l'admet, mais tous les analyseurs ne le retirent pas, et
         un nom d'agent suivi d'un commentaire mal coupe ne serait
         reconnu par personne. */
      if (a.note) out.push("# " + a.note);
      out.push("User-agent: " + a.nom);
    }
    out.push("Disallow: /");
  }

  out.push("");
  out.push(commentaire(
    "Tout le reste, moteurs de recherche compris.\n\n" +
    "La documentation a vocation a etre trouvee : la refuser aux\n" +
    "moteurs la retirerait des resultats sans gener un seul\n" +
    "collecteur d'entrainement. Seuls quelques chemins sans interet\n" +
    "en recherche sont ecartes.\n\n" +
    "Crawl-delay n'est pas lu par Google ; les autres le respectent,\n" +
    "et il suffit a etaler un parcours complet du site.", "#"));
  out.push("");
  out.push("User-agent: *");
  for (const c of CHEMINS_REFUSES) {
    out.push("# " + c.note);
    out.push("Disallow: " + c.chemin);
  }
  out.push("Crawl-delay: 5");
  out.push("");
  out.push("Sitemap: https://chartegraphique.gov.bf/sitemap.xml");
  out.push("");
  return out.join("\n");
}

/* --------------------------------------------------------- nginx */

/* Les noms d'agents partent dans une expression rationnelle : ce qui
   y a un sens particulier doit etre protege, faute de quoi « Teleport
   Pro » ou un point dans un nom elargirait silencieusement le refus. */
function echapper(nom) {
  return nom.replace(/[.*+?^${}()|[\]\\]/g, "\\$&").replace(/ /g, "\\s*");
}

function motif() {
  return NOMMABLES.map((a) => echapper(a.nom)).join("|");
}

function nginxHttp() {
  return commentaire(
    "Charte graphique de l'administration burkinabe\n" +
    "Refus des collecteurs, contexte « http »\n\n" + AVERTISSEMENT + "\n\n" +
    "A inclure dans le bloc http de nginx.conf, hors de tout bloc\n" +
    "server :\n\n" +
    "    include /etc/nginx/conf.d/faso-robots.conf;\n\n" +
    "Les regles qui s'en servent sont dans le bloc server, fournies\n" +
    "par hebergement/nginx.conf.", "#") + "\n" +
`
# ------------------------------------------------- supervision
# Les adresses inscrites ici ne sont ni refusees ni limitees.
#
# Une sonde de supervision n'envoie souvent aucun agent, et se
# ferait refuser par la regle suivante : le service serait declare
# en panne alors qu'il repond. Inscrire ici les adresses des sondes
# avant la mise en production.
geo $faso_supervision {
    default 0;
    # 10.0.0.7/32   1;
    # 10.0.0.8/32   1;
}

# ------------------------------------------------- refus par nom
# Un agent qui s'annonce est refuse. Un agent qui se declare
# navigateur passe : c'est la limite de cette couche, et c'est
# pourquoi la suivante existe.
map $http_user_agent $faso_agent_refuse {
    default                0;
    ""                     1;   # aucun agent declare
    "~*(${motif()})"   1;
}

# La supervision l'emporte : « 1 » en tete annule le refus.
map "$faso_supervision$faso_agent_refuse" $faso_collecteur {
    default   0;
    "01"      1;
}

# ------------------------------------------------- debit limite
# La seule couche qui agisse sur un collecteur non declare.
#
# Elle ne protege pas le contenu : neuf pages se copient en neuf
# requetes, et aucun reglage tenable ne l'empeche. Elle protege le
# serveur d'un collecteur mal regle, ce qui est un autre probleme
# et un vrai.
#
# Le debit vaut par adresse. Un ministere derriere une seule sortie
# nattee compte pour une adresse : c'est la raison pour laquelle la
# diffusion versionnee n'est pas limitee du tout, et pour laquelle
# la valeur des pages est large.
#
# Une cle vide exempte : c'est ainsi que la supervision echappe a la
# limitation comme elle echappe au refus.
map $faso_supervision $faso_cle_debit {
    default  $binary_remote_addr;   # dans le doute, on limite
    1        "";
}

limit_req_zone $faso_cle_debit zone=faso_pages:10m rate=60r/m;
limit_conn_zone $faso_cle_debit zone=faso_liens:10m;
`;
}

function nginxServer() {
  return `
    # Refus des collecteurs declares. Le 403 est rendu sans lire le
    # disque, et le journal garde la trace de la tentative.
    if ($faso_collecteur) {
        return 403;
    }
`;
}

function nginxPages() {
  return `
    # Le debit ne s'applique qu'ici, jamais a la diffusion versionnee :
    # les fichiers versionnes sont consommes par les navigateurs des
    # agents d'autres administrations, et les limiter par adresse
    # couperait un ministere entier derriere une sortie nattee.
    limit_req zone=faso_pages burst=30 nodelay;
    limit_conn faso_liens 24;
`;
}

/* -------------------------------------------------------- apache */

/*
 * Sur un hebergement mutualise, « Include » n'est pas disponible : les
 * regles doivent etre dans le .htaccess lui-meme. On les y ecrit entre
 * deux reperes, et on ne touche a rien d'autre du fichier.
 *
 * SetEnvIfNoCase plutot que mod_rewrite : c'est le module le plus
 * surement present, et la regle se lit. Aucune limitation de debit
 * n'est proposee ici — Apache n'en sait rien faire sans mod_evasive,
 * qui ne s'installe pas sur un hebergement mutualise. Le dire vaut
 * mieux que fournir une regle qui ne limite rien.
 */
function apacheBloc() {
  const lignes = [];
  lignes.push(commentaire(
    "Refus des collecteurs\n\n" + AVERTISSEMENT + "\n\n" +
    "Cette couche arrete ce qui s'annonce. Un collecteur qui se\n" +
    "declare navigateur passe. Sur un hebergement mutualise, il n'y a\n" +
    "pas de limitation de debit possible : le refus par nom est tout\n" +
    "ce que ce fichier peut faire.", "#"));
  lignes.push("");
  lignes.push("<IfModule mod_setenvif.c>");
  for (const g of GROUPES) {
    lignes.push("    # " + g.titre);
    for (const a of g.agents) {
      if (a.robotsSeulement) {
        lignes.push("    #   " + a.nom + " : jeton du robots.txt, aucune requete ne le porte");
        continue;
      }
      lignes.push('    SetEnvIfNoCase User-Agent "' + echapper(a.nom) +
        '" faso_collecteur');
    }
    lignes.push("");
  }
  lignes.push("    # Aucun agent declare.");
  lignes.push('    SetEnvIf User-Agent "^$" faso_collecteur');
  lignes.push("");
  lignes.push("    # Supervision : le « ! » retire le marquage pose plus haut.");
  lignes.push("    #");
  lignes.push("    # Une sonde n'envoie souvent aucun agent et se ferait refuser");
  lignes.push("    # par la regle ci-dessus : le service serait declare en panne");
  lignes.push("    # alors qu'il repond. Inscrire ici les adresses des sondes");
  lignes.push("    # avant la mise en production.");
  lignes.push('    #   SetEnvIf Remote_Addr "^10\\.0\\.0\\.7$" !faso_collecteur');
  lignes.push("</IfModule>");
  lignes.push("");
  lignes.push("<IfModule mod_authz_core.c>");
  lignes.push("    <RequireAll>");
  lignes.push("        Require all granted");
  lignes.push("        Require not env faso_collecteur");
  lignes.push("    </RequireAll>");
  lignes.push("</IfModule>");
  return lignes.join("\n");
}

/* ============================================ pose entre reperes */

const DEBUT = "# >>> robots : engendre par outils/robots.js";
const FIN = "# <<< robots";

function poser(chemin, bloc) {
  const avant = fs.readFileSync(chemin, "utf8");
  const d = avant.indexOf(DEBUT);
  const f = avant.indexOf(FIN);
  if (d < 0 || f < 0 || f < d) {
    throw new Error(path.basename(chemin) + " n'a pas les reperes " +
      DEBUT + " … " + FIN);
  }
  return avant.slice(0, d) + DEBUT + "\n" + bloc + "\n" + avant.slice(f);
}

const SORTIES = [
  { chemin: path.join(HEBERGEMENT, "robots.txt"), contenu: robotsTxt() },
  { chemin: path.join(HEBERGEMENT, "faso-robots.conf"), contenu: nginxHttp() },
  {
    chemin: path.join(HEBERGEMENT, "apache.htaccess"),
    contenu: poser(path.join(HEBERGEMENT, "apache.htaccess"), apacheBloc()),
  },
  /* Le même refus pour l'application Laravel servie par Apache : sa
     racine est public/, où hebergement/apache.htaccess ne s'applique
     pas. */
  {
    chemin: path.join(RACINE, "public", ".htaccess"),
    contenu: poser(path.join(RACINE, "public", ".htaccess"), apacheBloc()),
  },
];

/* nginx.conf porte deux blocs : le refus, en tete du bloc server, et
   la limitation, dans la location des pages. nginx-laravel.conf, qui
   sert l'application au lieu du site exporte, porte les deux memes. */
for (const fichier of ["nginx.conf", "nginx-laravel.conf"]) {
  const chemin = path.join(HEBERGEMENT, fichier);
  let texte = fs.readFileSync(chemin, "utf8");
  for (const [marque, bloc] of [
    ["refus", nginxServer()],
    ["pages", nginxPages()],
  ]) {
    const d = texte.indexOf(DEBUT + " " + marque);
    const f = texte.indexOf(FIN + " " + marque);
    if (d < 0 || f < 0 || f < d) {
      throw new Error(fichier + " n'a pas les reperes " + marque);
    }
    texte = texte.slice(0, d) + DEBUT + " " + marque + bloc +
      "    " + FIN + " " + marque + texte.slice(f + (FIN + " " + marque).length);
  }
  SORTIES.push({ chemin, contenu: texte });
}

/* ==================================================== execution */

if (process.argv.includes("--verifier")) {
  const ecarts = [];
  for (const s of SORTIES) {
    const actuel = fs.existsSync(s.chemin) ? fs.readFileSync(s.chemin, "utf8") : null;
    if (actuel !== s.contenu) ecarts.push(path.relative(RACINE, s.chemin));
  }
  if (ecarts.length) {
    console.error("  " + ecarts.join(", ") + " ne correspond plus a la liste.");
    console.error("  Relancer « node outils/robots.js ».");
    process.exit(1);
  }
  console.log("  " + NOMMABLES.length + " agents refuses, " +
    SORTIES.length + " fichiers a jour");
  process.exit(0);
}

for (const s of SORTIES) {
  fs.writeFileSync(s.chemin, s.contenu, "utf8");
  console.log("  " + path.relative(RACINE, s.chemin));
}
console.log("");
for (const g of GROUPES) {
  console.log("  " + String(g.agents.length).padStart(3) + "  " + g.titre);
}
console.log("  " + String(MOTEURS.length).padStart(3) + "  moteurs de recherche, laisses passer");
