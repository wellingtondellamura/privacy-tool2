<?php

return [
    // ProjectController
    'project_created' => 'Projeto criado com sucesso.',
    'project_updated' => 'Projeto atualizado com sucesso.',
    'project_deleted' => 'Projeto removido com sucesso.',
    'cannot_change_owner_role' => 'Não é possível alterar o papel do dono do projeto.',
    'member_role_updated' => 'Papel do membro atualizado com sucesso.',
    'project_settings_updated' => 'Configurações do projeto atualizadas com sucesso.',

    // InspectionController
    'round_required' => 'É necessário selecionar uma rodada de avaliação para iniciar uma inspeção.',
    'cannot_add_to_closed_round' => 'Não é possível adicionar inspeções a uma rodada que já está fechada.',
    'already_has_inspection_in_round' => 'Você já possui uma inspeção nesta rodada. Cada membro pode criar apenas uma inspeção por rodada.',
    'inspection_activated' => 'Inspeção iniciada e mudou para status Ativa.',
    'only_responsible_can_change' => 'Apenas o responsável pela inspeção pode mudar seu status.',
    'only_responsible_can_respond' => 'Apenas o responsável pela inspeção pode submeter respostas.',
    'evaluator_own_inspection_only' => 'Avaliadores só podem visualizar a própria inspeção.',
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
    'round_updated' => 'Rodada de avaliação atualizada com sucesso.',
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

    // Melhorias de UX/regras
    'consolidation_review_only' => 'A consolidação só pode ser feita durante a fase de revisão.',
    'consolidation_saved' => 'Resposta final consolidada.',
    'consolidation_reset' => 'Consolidação removida: a questão volta a usar a média.',
    'round_not_draft' => 'A rodada não está em rascunho.',
    'review_started' => 'Fase de revisão iniciada. Os membros foram avisados por e-mail.',
    'no_active_questionnaire' => 'Nenhuma versão ativa do questionário está disponível.',
    'already_member' => 'Este usuário já é membro do projeto.',
    'invitation_invalid' => 'Convite inválido.',
    'invitation_already_accepted' => 'Este convite já foi aceito.',
    'invitation_expired' => 'Este convite expirou. Peça ao dono do projeto para reenviá-lo.',
    'invitation_login_required' => 'Entre com a conta :email para aceitar o convite.',
    'invitation_wrong_account' => 'Este convite foi enviado para :email. Entre com essa conta para aceitá-lo.',
    'inspection_not_active' => 'A inspeção não está ativa. Não é possível enviar respostas.',
    'only_evaluators_respond' => 'Apenas avaliadores podem enviar respostas.',
    'observers_cannot_inspect' => 'Observadores não podem criar inspeções.',
    'inspection_round_not_draft' => 'Novas inspeções só podem ser criadas enquanto a rodada está em rascunho.',
    'comments_review_only' => 'Comentários só podem ser feitos durante a fase de revisão.',
    'comment_sent' => 'Comentário enviado.',
    'comment_deleted' => 'Comentário excluído.',
    'export_owner_only' => 'Apenas o dono do projeto pode exportar os dados.',
];
