// Política de Privacidade, versão 1.0. A versão em português prevalece sobre as traduções.
// Marcadores {controller}, {controller_details}, {contact}, {dpo} e {url} são substituídos a partir de config/legal.php.
export default {
    intro: [
        'Esta Política de Privacidade descreve quais dados pessoais são tratados pelo Mitra Privacy Tool, disponível em {url}, para quais finalidades, com quais bases legais, com quem são compartilhados, por quanto tempo são mantidos e como o titular pode exercer seus direitos, nos termos da Lei nº 13.709/2018 (Lei Geral de Proteção de Dados Pessoais, LGPD).',
    ],
    sections: [
        {
            title: 'Controlador e encarregado',
            blocks: [
                'O controlador dos dados pessoais tratados na plataforma é {controller}{controller_details}. O encarregado pelo tratamento de dados pessoais é {dpo}.',
                'Dentro de cada projeto, o proprietário decide quem é convidado, quais avaliações são visíveis aos membros, se os dados são exportados e se o resultado consolidado é publicado. Essas decisões são de responsabilidade do proprietário do projeto, que deve observar a LGPD no uso que faz dos dados de outros membros.',
            ],
        },
        {
            title: 'Dados pessoais tratados',
            blocks: [
                'São tratados os dados pessoais definidos da seguinte forma:',
                {
                    list: [
                        { label: 'Dados de cadastro', text: 'nome, endereço de e-mail, senha (armazenada apenas na forma de resumo criptográfico, que não permite recuperá-la), idioma preferido, data de confirmação do e-mail e data e hora do aceite dos Termos de Uso e desta política.' },
                        { label: 'Dados opcionais de perfil', text: 'perfil (estudante, profissional ou pesquisador) e vínculo institucional ou profissional, informados apenas se o usuário assim desejar.' },
                        { label: 'Dados de uso da plataforma', text: 'projetos criados ou de que o usuário participa e o respectivo papel, respostas aos questionários, observações, comentários de revisão, decisões de consolidação, publicações realizadas e data do último acesso.' },
                        { label: 'Dados de pessoas convidadas', text: 'endereço de e-mail informado pelo proprietário de um projeto para envio de convite, mesmo que a pessoa ainda não tenha conta na plataforma.' },
                        { label: 'Dados técnicos', text: 'endereço IP, identificação do navegador (user agent), identificador da sessão e horário da última atividade. Em caso de erro ou falha, são registrados também o endereço acessado, os parâmetros da requisição e a identificação do usuário autenticado, com exclusão de senhas, tokens de segurança e cookies.' },
                    ],
                },
                'A plataforma não solicita dados pessoais sensíveis (art. 5º, II, da LGPD) nem dados de crianças ou adolescentes. Os campos de texto livre destinam-se a registrar informações sobre o software avaliado e não devem conter esse tipo de dado.',
            ],
        },
        {
            title: 'Finalidades e bases legais',
            blocks: [
                'Os dados pessoais são tratados para as finalidades a seguir, cada uma com a respectiva base legal prevista no art. 7º da LGPD:',
                {
                    list: [
                        { label: 'Criação da conta, autenticação e funcionamento da plataforma', text: 'gestão de projetos, convites, inspeções, revisão, consolidação, resultados e exportação. Base legal: execução de contrato (art. 7º, V).' },
                        { label: 'Envio de e-mails de serviço', text: 'confirmação de e-mail, redefinição de senha e convites para projetos. Base legal: execução de contrato (art. 7º, V) e, no caso de pessoas convidadas que ainda não possuem conta, legítimo interesse (art. 7º, IX) em viabilizar a colaboração solicitada pelo proprietário do projeto.' },
                        { label: 'Segurança e diagnóstico de falhas', text: 'prevenção de acessos indevidos, controle de sessões e investigação de erros. Base legal: legítimo interesse (art. 7º, IX).' },
                        { label: 'Registro do aceite dos termos', text: 'comprovação da concordância do usuário com as condições de uso. Base legal: exercício regular de direitos (art. 7º, VI).' },
                        { label: 'Caracterização do público da plataforma', text: 'uso do perfil e do vínculo institucional opcionais para estatísticas agregadas, que não identificam pessoas. Base legal: consentimento (art. 7º, I), manifestado pelo preenchimento voluntário desses campos e revogável a qualquer momento pela sua remoção na página de perfil.' },
                    ],
                },
                'Os dados não são utilizados para publicidade, não são vendidos e não são usados na construção de perfis comportamentais. As pontuações calculadas pela plataforma referem-se ao software avaliado e não a pessoas, de modo que não há decisões automatizadas sobre os titulares no sentido do art. 20 da LGPD.',
            ],
        },
        {
            title: 'Compartilhamento de dados',
            blocks: [
                'Os dados pessoais são acessados ou compartilhados apenas nas situações descritas a seguir:',
                {
                    list: [
                        { label: 'Membros do mesmo projeto', text: 'veem o nome e o e-mail dos demais membros. As respostas e observações individuais são visíveis ao proprietário e, se ele assim configurar o projeto, aos demais membros.' },
                        { label: 'Proprietário do projeto', text: 'pode exportar todos os dados do projeto, o que inclui nome, e-mail, respostas e observações de cada membro.' },
                        { label: 'Diretório público e selo', text: 'exibem somente resultados consolidados, conforme a seção 6 dos Termos de Uso, sem identificação dos avaliadores.' },
                        { label: 'Administradores da plataforma', text: 'pessoas autorizadas pelo controlador, que acessam os dados na medida necessária para operação, suporte e segurança, sob dever de confidencialidade.' },
                        { label: 'Operadores', text: 'provedores de hospedagem da aplicação e do banco de dados e provedor de envio de e-mails, que tratam os dados apenas segundo as instruções do controlador.' },
                        { label: 'Google Fonts', text: 'as páginas da plataforma carregam fontes tipográficas dos servidores da Google LLC, que recebem o endereço IP e a identificação do navegador do visitante ao fornecer esses arquivos.' },
                        { label: 'Autoridades', text: 'mediante requisição legal, ordem judicial ou para o exercício regular de direitos em processo.' },
                    ],
                },
            ],
        },
        {
            title: 'Transferência internacional',
            blocks: [
                'O carregamento de fontes pela Google LLC e, a depender dos provedores contratados, a hospedagem e o envio de e-mails podem envolver o tratamento de dados em servidores fora do Brasil. Nesses casos, a transferência ocorre nas hipóteses do art. 33 da LGPD, com a adoção de provedores que ofereçam garantias de proteção compatíveis com a legislação brasileira.',
            ],
        },
        {
            title: 'Prazos de retenção',
            blocks: [
                'Os dados pessoais são mantidos apenas pelo tempo necessário às finalidades descritas, conforme os prazos a seguir:',
                {
                    list: [
                        { label: 'Dados de cadastro, de perfil e de uso', text: 'enquanto a conta existir. Com a exclusão da conta, são eliminados conforme a seção 10 dos Termos de Uso.' },
                        { label: 'Convites', text: 'até a remoção do convite pelo proprietário ou a exclusão do projeto.' },
                        { label: 'Sessões', text: 'até o encerramento ou a expiração da sessão, após o qual são removidas automaticamente.' },
                        { label: 'Registros de erros e falhas', text: 'por até 90 dias.' },
                        { label: 'Cópias de segurança', text: 'dados excluídos podem permanecer em cópias de segurança até que sejam sobrescritas no ciclo regular de backup, sem uso para qualquer outra finalidade.' },
                    ],
                },
                'Dados poderão ser mantidos por prazo superior quando necessário ao cumprimento de obrigação legal ou ao exercício regular de direitos (art. 16 da LGPD).',
            ],
        },
        {
            title: 'Direitos do titular',
            blocks: [
                'O titular dos dados pode exercer os direitos previstos no art. 18 da LGPD. Parte deles está disponível diretamente na plataforma, nos termos a seguir:',
                {
                    list: [
                        { label: 'Acesso e correção', text: 'na página de perfil, onde podem ser consultados e alterados nome, e-mail, idioma, perfil e vínculo institucional.' },
                        { label: 'Portabilidade', text: 'pela opção de exportar todos os dados, que gera um arquivo com os projetos de que o usuário participa. Nos projetos de terceiros, o arquivo contém apenas os dados do próprio usuário.' },
                        { label: 'Eliminação', text: 'pela exclusão da conta, na página de perfil.' },
                        { label: 'Revogação do consentimento', text: 'pela remoção do perfil e do vínculo institucional na página de perfil.' },
                    ],
                },
                'Os demais direitos, como confirmação da existência de tratamento, informação sobre compartilhamento, anonimização, bloqueio, oposição a tratamento baseado em legítimo interesse e revisão de decisões, podem ser exercidos por solicitação ao encarregado, pelo endereço {contact}. A solicitação será respondida em até 15 dias, e poderá ser solicitada confirmação de identidade antes do atendimento. Pessoas convidadas que não possuem conta podem, pelo mesmo canal, pedir a remoção de seu e-mail.',
                'O titular também pode apresentar reclamação à Autoridade Nacional de Proteção de Dados (ANPD).',
            ],
        },
        {
            title: 'Segurança',
            blocks: [
                'São adotadas medidas técnicas e administrativas para proteger os dados pessoais, entre elas o armazenamento de senhas por resumo criptográfico, a comunicação cifrada (HTTPS), o controle de acesso por papel em cada projeto, a confirmação do e-mail no cadastro, a proteção contra requisições forjadas (CSRF) e a omissão de senhas e tokens nos registros de falhas. Nenhum sistema é imune a incidentes. Caso ocorra um incidente de segurança que possa acarretar risco ou dano relevante aos titulares, o controlador comunicará a ANPD e os titulares afetados, conforme o art. 48 da LGPD.',
            ],
        },
        {
            title: 'Cookies',
            blocks: [
                'A plataforma utiliza apenas cookies necessários ao seu funcionamento, sem cookies de publicidade ou de análise de navegação. Por serem indispensáveis, esses cookies não dependem de consentimento. São eles:',
                {
                    list: [
                        { label: 'Cookie de sessão', text: 'mantém o usuário autenticado e guarda preferências como o idioma. Expira após 2 horas sem atividade.' },
                        { label: 'XSRF-TOKEN', text: 'protege os formulários contra requisições forjadas. Tem a mesma duração da sessão.' },
                        { label: 'Cookie "lembrar de mim"', text: 'criado apenas quando o usuário marca essa opção no login, para manter o acesso entre visitas. Dura cerca de 400 dias ou até que o usuário saia da conta.' },
                    ],
                },
                'Quando um site de terceiros incorpora o selo da plataforma, o navegador do visitante desse site faz uma requisição à plataforma, que recebe o endereço IP e a identificação do navegador, como em qualquer acesso a um site. O selo não cria cookies e não identifica o visitante. O bloqueio de cookies pelo navegador impede o login na plataforma.',
            ],
        },
        {
            title: 'Alterações desta política',
            blocks: [
                'Esta política pode ser revisada para refletir mudanças na plataforma ou na legislação. A versão vigente e a data da última atualização são indicadas no início do documento. Alterações relevantes serão comunicadas por e-mail ou por aviso na plataforma, e será solicitado novo aceite quando a alteração ampliar o tratamento de dados pessoais.',
            ],
        },
        {
            title: 'Contato',
            blocks: [
                'Dúvidas sobre esta política e solicitações relativas aos dados pessoais devem ser encaminhadas ao encarregado pelo endereço {contact}.',
            ],
        },
    ],
};
