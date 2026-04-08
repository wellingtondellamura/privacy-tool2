<?php

return [
    // ProjectController
    'project_created' => 'Projeto criado com sucesso.',
    'project_updated' => 'Projeto atualizado com sucesso.',
    'project_deleted' => 'Projeto removido com sucesso.',
    'cannot_change_owner_role' => 'Não é possível alterar o papel do dono do projeto.',
    'member_role_updated' => 'Papel do membro atualizado com sucesso.',

    // InspectionController
    'round_required' => 'É necessário selecionar uma rodada de avaliação para iniciar uma inspeção.',
    'cannot_add_to_closed_round' => 'Não é possível adicionar inspeções a uma rodada que já está fechada.',
    'inspection_activated' => 'Inspeção iniciada e mudou para status Ativa.',
    'only_responsible_can_change' => 'Apenas o responsável pela inspeção pode mudar seu status.',
    'inspection_closed' => 'Inspeção finalizada e instantâneos gerados.',

    // InvitationController
    'invitation_sent' => 'Convite enviado com sucesso para :email.',
    'invitation_accepted' => 'Convite aceito com sucesso.',
    'invitation_declined' => 'Convite recusado/cancelado com sucesso.',
    'invitation_resent' => 'Convite reenviado com sucesso para :email.',

    // ResultController
    'no_results_for_user' => 'Nenhum resultado encontrado para este usuário.',
    'inspection_must_be_closed' => 'A inspeção deve estar concluída para ver resultados da equipe.',
    'consolidated_not_found' => 'Resultados consolidados não encontrados.',
    'round_consolidated_not_found' => 'Resultado consolidado da rodada não encontrado.',
    'inspections_same_project' => 'As inspeções devem pertencer ao mesmo projeto.',
    'both_inspections_closed' => 'Ambas as inspeções devem estar concluídas.',
    'rounds_same_project' => 'As rodadas devem pertencer ao mesmo projeto.',
    'both_rounds_closed' => 'Ambas as rodadas devem estar concluídas.',

    // EvaluationRoundController
    'round_created' => 'Rodada de avaliação criada com sucesso.',
    'round_already_closed' => 'Esta rodada já foi fechada.',
    'round_closed' => 'Rodada fechada com sucesso.',

    // EvaluationRoundPublicationController
    'round_published' => 'Rodada publicada com sucesso no diretório.',
    'publication_not_found' => 'Publicação não encontrada.',
    'publication_updated' => 'Visibilidade da publicação atualizada.',
    'publication_removed' => 'Publicação removida do diretório.',

    // RoundBadgeController
    'badge_generated' => 'Selo gerado com sucesso.',
    'badge_revoked' => 'Selo revogado.',
    'badge_style_updated' => 'Estilo do selo atualizado.',

    // DataExportController
    'no_projects_to_export' => 'Você não possui projetos para exportar.',
    'zip_creation_failed' => 'Não foi possível criar o arquivo ZIP.',
];
