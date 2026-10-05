<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{!! $titre !!}</title>
<meta name="description" content="{!! $description !!}">
<!-- Pose le theme retenu avant le premier rendu, pour eviter
     le clignotement clair d'une page reglee en sombre. -->
<script src="{{ ressource('assets/js/faso-amorce.js') }}"></script>
<link rel="stylesheet" href="{{ ressource('assets/css/tokens.css') }}">
<link rel="stylesheet" href="{{ ressource('assets/css/faso.css') }}">
<link rel="stylesheet" href="{{ ressource('assets/css/icones.css') }}">
<link rel="stylesheet" href="{{ ressource('assets/css/docs.css') }}">
<link rel="icon" href="{{ ressource('assets/img/favicon.svg') }}">
<link rel="alternate icon" href="{{ ressource('assets/img/favicon-32.png') }}" sizes="32x32">
<link rel="apple-touch-icon" href="{{ ressource('assets/img/favicon-180.png') }}">
@stack('tete')
</head>
<body>

@yield('corps')
</body>
</html>
