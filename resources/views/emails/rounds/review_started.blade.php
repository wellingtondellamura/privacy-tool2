@component('mail::message')
# Olá, {{ $user->name }}

O dono do projeto **{{ $round->project->name }}** iniciou o processo de consolidação da rodada de avaliação "**{{ $round->name }}**".

**Modelo de Consenso:** {{ $round->project->consensus_model->label() }}

@if ($round->project->consensus_model === \App\Enums\ConsensusModel::EVALUATOR_CONVERGENCE)
Como o modelo escolhido é a **Convergência entre Avaliadores**, pode ser necessário que você interaja com os demais membros da equipe para debater divergências e chegar a um consenso final antes do fechamento da rodada. Acesse o sistema e deixe seus comentários nas questões onde houver discordância.
@elseif ($round->project->consensus_model === \App\Enums\ConsensusModel::OWNER_DECIDES)
Como o modelo escolhido é a **Decisão do Dono**, nenhuma ação adicional é necessária da sua parte. O dono do projeto revisará as divergências e tomará a decisão final. Você receberá uma notificação quando a rodada for fechada.
@elseif ($round->project->consensus_model === \App\Enums\ConsensusModel::MAJORITY_VOTE)
Como o modelo escolhido é a **Votação por Maioria**, o sistema calculará o consenso com base nas respostas da maioria. Nenhuma ação adicional é necessária da sua parte, a menos que ocorra um empate que exija a intervenção do dono. Apenas aguarde o fechamento oficial da rodada.
@endif

@component('mail::button', ['url' => route('rounds.show', $round->id)])
Acessar Rodada
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
