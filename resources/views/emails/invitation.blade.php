@component('mail::message')
# Convite para colaboração

Você foi convidado para participar do projeto **{{ $project->name }}** na ferramenta Privacy Tool.

Seu nível de acesso será: **{{ $invitation->role === 'evaluator' ? 'Avaliador' : 'Observador' }}**.

@if($hasAccount)
Como você já possui uma conta em nosso sistema, basta realizar o login e aceitar o convite através do seu Dashboard.

@component('mail::button', ['url' => route('login')])
Fazer Login e Aceitar
@endcomponent
@else
Para aceitar o convite e participar do projeto, você precisará criar uma conta gratuita usando este endereço de e-mail.

@component('mail::button', ['url' => route('register')])
Criar Conta e Aceitar
@endcomponent
@endif

Caso você não tenha solicitado ou não conheça este projeto, nenhuma ação é necessária e este convite irá expirar.

Obrigado,<br>
A equipe do {{ config('app.name') }}
@endcomponent
