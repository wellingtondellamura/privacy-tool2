// Termos de Uso, versão 1.0. A versão em português prevalece sobre as traduções.
// Marcadores {controller}, {contact} e {url} são substituídos a partir de config/legal.php.
export default {
    intro: [
        'Estes Termos de Uso regulam o acesso e a utilização do Mitra Privacy Tool, plataforma disponível em {url} e mantida por {controller}, doravante denominado "mantenedor". A Política de Privacidade, que descreve como os dados pessoais são tratados, integra estes termos.',
    ],
    sections: [
        {
            title: 'Aceitação dos termos',
            blocks: [
                'A criação de uma conta exige a aceitação expressa destes Termos de Uso e da Política de Privacidade. A data e a hora do aceite são registradas na conta do usuário. Caso não concorde com alguma das condições aqui previstas, o usuário não deve criar uma conta ou, se já a possuir, pode excluí-la a qualquer momento, conforme descrito na seção 10.',
                'A plataforma destina-se a pessoas com 18 anos ou mais. Ao aceitar estes termos, o usuário declara possuir capacidade civil para fazê-lo.',
            ],
        },
        {
            title: 'Descrição do serviço',
            blocks: [
                'O Mitra Privacy Tool é uma plataforma de apoio à avaliação da transparência no tratamento de dados pessoais em software, baseada no Método Mitra. A ferramenta é destinada a estudantes, pesquisadores, profissionais e equipes de desenvolvimento, e oferece os recursos a seguir:',
                {
                    list: [
                        { label: 'Projetos', text: 'para organizar a avaliação de um software e reunir as pessoas envolvidas, com os papéis de proprietário, avaliador e observador;' },
                        { label: 'Rodadas de avaliação e inspeções', text: 'para que cada avaliador responda individualmente ao questionário do método, com posterior revisão e consolidação das respostas;' },
                        { label: 'Resultados', text: 'com pontuações individuais e consolidadas, comparação entre rodadas e histórico de evolução;' },
                        { label: 'Diretório público e selo', text: 'para que o proprietário de um projeto divulgue, se assim decidir, o resultado consolidado de uma rodada encerrada;' },
                        { label: 'Exportação', text: 'para obter uma cópia dos dados de projetos em formato aberto (JSON).' },
                    ],
                },
                'A plataforma é gratuita, não tem finalidade comercial e é mantida exclusivamente para fins de pesquisa acadêmica.',
                'O mantenedor pode alterar, incluir ou remover funcionalidades ao longo do tempo. Alterações que afetem de forma relevante o uso da plataforma serão comunicadas conforme a seção 11.',
            ],
        },
        {
            title: 'Natureza dos resultados',
            blocks: [
                'As pontuações, classificações e medalhas geradas pela plataforma expressam o julgamento dos avaliadores de um projeto, obtido pela aplicação dos critérios do Método Mitra a uma versão específica do software, em um momento específico. Esses resultados não constituem certificação, auditoria, parecer jurídico nem atestado de conformidade com a Lei Geral de Proteção de Dados Pessoais (LGPD, Lei nº 13.709/2018) ou com qualquer outra norma.',
                'O mantenedor não revisa, não valida e não endossa as avaliações realizadas pelos usuários. Decisões tomadas com base nos resultados são de responsabilidade exclusiva de quem as toma.',
            ],
        },
        {
            title: 'Cadastro e conta',
            blocks: [
                'Para utilizar a plataforma, o usuário deve informar nome, endereço de e-mail válido e senha, e confirmar o endereço de e-mail por meio do link enviado. O perfil (estudante, profissional ou pesquisador) e o vínculo institucional são opcionais. Cabe ao usuário:',
                {
                    list: [
                        'fornecer informações verdadeiras e mantê-las atualizadas;',
                        'manter a senha em sigilo e não compartilhar a conta com outras pessoas, já que cada conta é pessoal e intransferível;',
                        'comunicar ao mantenedor, pelo canal indicado na seção 13, qualquer uso não autorizado da conta de que tenha conhecimento.',
                    ],
                },
                'As ações realizadas a partir da conta são atribuídas ao seu titular, salvo prova de acesso indevido para o qual o titular não tenha contribuído.',
            ],
        },
        {
            title: 'Projetos, papéis e conteúdo do usuário',
            blocks: [
                'O usuário que cria um projeto torna-se seu proprietário. O proprietário define quem participa do projeto, atribui os papéis de avaliador ou observador, controla a visibilidade das avaliações individuais entre os membros, decide sobre a consolidação das respostas e sobre a publicação dos resultados. Ao convidar uma pessoa, o proprietário informa o endereço de e-mail dela à plataforma, que envia o convite. O proprietário deve convidar apenas pessoas com quem tenha relação legítima no contexto da avaliação.',
                'Respostas, observações, comentários de revisão e demais conteúdos inseridos na plataforma permanecem sob a titularidade de quem os produziu. Ao inseri-los, o usuário autoriza o mantenedor a armazená-los, processá-los e exibi-los na medida necessária para o funcionamento da plataforma, o que inclui a exibição aos demais membros do projeto e, no caso de resultados consolidados, a publicação decidida pelo proprietário.',
                'Os campos de texto livre devem ser usados para registrar informações sobre o software avaliado. Não devem ser inseridos dados pessoais de terceiros sem necessidade, nem dados pessoais sensíveis, como origem racial ou étnica, convicção religiosa, opinião política, dados de saúde ou de vida sexual.',
            ],
        },
        {
            title: 'Publicação no Diretório público e selo',
            blocks: [
                'Somente o proprietário pode publicar uma rodada encerrada no Diretório público e gerar o selo para incorporação em sites de terceiros. A publicação exibe o nome do projeto, o nome da rodada, a versão do software avaliado, as pontuações consolidadas, a medalha obtida e o número de avaliadores. Não são exibidas as respostas individuais, as observações nem a identidade dos avaliadores.',
                'Ao publicar, o proprietário declara que a avaliação foi conduzida de boa-fé, segundo o método, e que a divulgação não viola direitos de terceiros, inclusive direitos sobre marcas e nomes de produtos. A responsabilidade pelo conteúdo publicado é do proprietário que o publicou.',
                'A publicação pode ser retirada e o selo pode ser revogado a qualquer momento pelo proprietário. Após a revogação, o selo deixa de ser exibido nos sites em que foi incorporado. Cópias, capturas de tela ou registros feitos por terceiros enquanto o conteúdo esteve público estão fora do controle do mantenedor.',
            ],
        },
        {
            title: 'Condutas vedadas',
            blocks: [
                'Na utilização da plataforma, é vedado ao usuário:',
                {
                    list: [
                        'publicar avaliações sabidamente falsas ou realizadas com o objetivo de prejudicar ou favorecer indevidamente um software, uma organização ou uma pessoa;',
                        'apresentar resultados da plataforma como certificação oficial ou atestado de conformidade legal;',
                        'inserir conteúdo ilícito, ofensivo ou discriminatório, ou que viole direitos de terceiros;',
                        'convidar pessoas para fins alheios à avaliação, como o envio de mensagens não solicitadas;',
                        'tentar acessar contas, projetos ou dados para os quais não tenha autorização;',
                        'interferir no funcionamento da plataforma, inclusive por meio de acessos automatizados em volume que comprometa sua disponibilidade, ou explorar vulnerabilidades em vez de comunicá-las ao mantenedor.',
                    ],
                },
                'Vulnerabilidades de segurança identificadas de boa-fé devem ser comunicadas pelo canal indicado na seção 13.',
            ],
        },
        {
            title: 'Propriedade intelectual',
            blocks: [
                'O código-fonte do Mitra Privacy Tool é distribuído como software livre sob a licença MIT, cujos termos prevalecem sobre estes no que se refere ao uso, à cópia e à modificação do código. O Método Mitra, os questionários e os textos explicativos que acompanham a plataforma pertencem a seus respectivos autores, e sua reprodução deve citar a fonte.',
                'O nome e a identidade visual do Mitra Privacy Tool não podem ser usados de forma que sugira vínculo, patrocínio ou endosso do mantenedor sem sua autorização. O uso do selo gerado pela plataforma, nos termos da seção 6, é permitido.',
            ],
        },
        {
            title: 'Disponibilidade e limitação de responsabilidade',
            blocks: [
                'Por se tratar de serviço gratuito mantido para fins de pesquisa, a plataforma é disponibilizada no estado em que se encontra. O mantenedor emprega esforços razoáveis para mantê-la disponível e segura, mas não garante funcionamento ininterrupto ou livre de erros, e pode suspendê-la temporariamente para manutenção. Recomenda-se ao usuário exportar periodicamente os dados de seus projetos.',
                'Nos limites permitidos pela legislação aplicável, o mantenedor não responde por:',
                {
                    list: [
                        'decisões tomadas com base nos resultados das avaliações, conforme a seção 3;',
                        'conteúdo inserido ou publicado pelos usuários;',
                        'danos decorrentes de indisponibilidade temporária ou de perda de dados causada por fatores fora de seu controle razoável;',
                        'uso indevido da conta resultante da falta de sigilo da senha por parte do usuário.',
                    ],
                },
            ],
        },
        {
            title: 'Exclusão de conta e suspensão',
            blocks: [
                'O usuário pode excluir sua conta a qualquer momento, na página de perfil, mediante confirmação da senha. A exclusão é definitiva e produz os efeitos a seguir:',
                {
                    list: [
                        'os projetos de que o usuário é proprietário são excluídos, com todas as rodadas, inspeções, respostas, resultados, publicações e selos, inclusive o conteúdo produzido pelos demais membros desses projetos;',
                        'nos projetos de terceiros, são excluídas a participação do usuário, suas respostas, observações e comentários;',
                        'nas publicações e consolidações de projetos de terceiros, a referência ao usuário é removida, e o conteúdo agregado permanece.',
                    ],
                },
                'Antes de excluir a conta, recomenda-se exportar os dados e avisar os membros dos projetos de que o usuário é proprietário.',
                'O mantenedor pode suspender ou encerrar contas que violem estes termos, mediante comunicação ao usuário, salvo quando a comunicação prévia comprometer a apuração ou a segurança da plataforma.',
            ],
        },
        {
            title: 'Alterações destes termos',
            blocks: [
                'Estes termos podem ser revisados. A versão vigente e a data da última atualização são indicadas no início do documento. Alterações relevantes serão comunicadas por e-mail ou por aviso na plataforma com antecedência mínima de 15 dias de sua entrada em vigor. Caso não concorde com a nova versão, o usuário pode excluir sua conta antes dessa data. Quando a alteração ampliar o tratamento de dados pessoais ou criar novas obrigações ao usuário, será solicitado novo aceite.',
            ],
        },
        {
            title: 'Legislação aplicável',
            blocks: [
                'Estes termos são regidos pela legislação brasileira, em especial pela Lei nº 12.965/2014 (Marco Civil da Internet) e pela Lei nº 13.709/2018 (LGPD). Como o uso da plataforma é gratuito e sem finalidade econômica, não se estabelece relação de consumo entre o mantenedor e os usuários. Fica eleito o foro do domicílio do mantenedor para dirimir eventuais controvérsias decorrentes destes termos.',
            ],
        },
        {
            title: 'Contato',
            blocks: [
                'Dúvidas sobre estes termos, comunicações de uso indevido e relatos de vulnerabilidades podem ser enviados para {contact}.',
            ],
        },
    ],
};
