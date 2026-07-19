@component('mail::message')
# {{ __('email.round_review_greeting', ['name' => $user->name]) }}

{{ __('email.round_review_body', ['project' => $round->project->name, 'round' => $round->name]) }}

**{{ __('email.round_review_consensus_label') }}** {{ $round->project->consensus_model->label() }}

@if ($round->project->consensus_model === \App\Enums\ConsensusModel::EVALUATOR_CONVERGENCE)
{{ __('email.round_review_consensus_evaluator') }}
@elseif ($round->project->consensus_model === \App\Enums\ConsensusModel::OWNER_DECIDES)
{{ __('email.round_review_consensus_owner') }}
@elseif ($round->project->consensus_model === \App\Enums\ConsensusModel::MAJORITY_VOTE)
{{ __('email.round_review_consensus_majority') }}
@endif

@component('mail::button', ['url' => route('rounds.show', $round->id)])
{{ __('email.round_review_button') }}
@endcomponent

{{ __('email.round_review_thanks') }}<br>
{{ config('app.name') }}
@endcomponent
