<?php

return [
    // ProjectController
    'project_created' => 'Proyecto creado correctamente.',
    'project_updated' => 'Proyecto actualizado correctamente.',
    'project_deleted' => 'Proyecto eliminado correctamente.',
    'cannot_change_owner_role' => 'No es posible cambiar el rol del propietario del proyecto.',
    'member_role_updated' => 'Rol del miembro actualizado correctamente.',
    'project_settings_updated' => 'Configuración del proyecto actualizada correctamente.',

    // InspectionController
    'round_required' => 'Es necesario seleccionar una ronda de evaluación para iniciar una inspección.',
    'cannot_add_to_closed_round' => 'No es posible agregar inspecciones a una ronda que ya está cerrada.',
    'already_has_inspection_in_round' => 'Ya tiene una inspección en esta ronda. Cada miembro puede crear solo una inspección por ronda.',
    'inspection_activated' => 'Inspección iniciada y cambiada al estado Activo.',
    'only_responsible_can_change' => 'Solo el responsable de la inspección puede cambiar su estado.',
    'only_responsible_can_respond' => 'Solo el responsable de la inspección puede enviar respuestas.',
    'evaluator_own_inspection_only' => 'Los evaluadores solo pueden visualizar su propia inspección.',
    'inspection_closed' => 'Inspección finalizada e instantáneas generadas.',

    // InvitationController
    'invitation_sent' => 'Invitación enviada correctamente a :email.',
    'invitation_accepted' => 'Invitación aceptada correctamente.',
    'invitation_declined' => 'Invitación rechazada/cancelada correctamente.',
    'invitation_resent' => 'Invitación reenviada correctamente a :email.',

    // ResultController
    'no_results_for_user' => 'No se encontraron resultados para este usuario.',
    'inspection_must_be_closed' => 'La inspección debe estar finalizada para ver los resultados del equipo.',
    'consolidated_not_found' => 'Resultados consolidados no encontrados.',
    'round_consolidated_not_found' => 'Resultado consolidado de la ronda no encontrado.',
    'inspections_same_project' => 'Las inspecciones deben pertenecer al mismo proyecto.',
    'both_inspections_closed' => 'Ambas inspecciones deben estar finalizadas.',
    'rounds_same_project' => 'Las rondas deben pertenecer al mismo proyecto.',
    'both_rounds_closed' => 'Ambas rondas deben estar finalizadas.',

    // EvaluationRoundController
    'round_created' => 'Ronda de evaluación creada correctamente.',
    'round_updated' => 'Ronda de evaluación actualizada correctamente.',
    'round_already_closed' => 'Esta ronda ya ha sido cerrada.',
    'round_closed' => 'Ronda cerrada correctamente.',

    // EvaluationRoundPublicationController
    'round_published' => 'Ronda publicada correctamente en el directorio.',
    'publication_not_found' => 'Publicación no encontrada.',
    'publication_updated' => 'Visibilidad de la publicación actualizada.',
    'publication_removed' => 'Publicación eliminada del directorio.',

    // RoundBadgeController
    'badge_generated' => 'Insignia generada correctamente.',
    'badge_revoked' => 'Insignia revocada.',
    'badge_style_updated' => 'Estilo de la insignia actualizado.',

    // DataExportController
    'no_projects_to_export' => 'No tiene proyectos para exportar.',
    'zip_creation_failed' => 'No fue posible crear el archivo ZIP.',
];
