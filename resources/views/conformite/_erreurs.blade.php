@if ($errors->any())
  <div class="fs-recap-erreurs" role="alert" tabindex="-1">
    <h2>{{ $errors->count() === 1 ? 'Une information doit être corrigée' : $errors->count().' informations doivent être corrigées' }}</h2>
    <ul>
      @foreach ($errors->messages() as $champ => $messages)
        <li><a href="#{{ $champ }}">{{ $messages[0] }}</a></li>
      @endforeach
    </ul>
  </div>
@endif
