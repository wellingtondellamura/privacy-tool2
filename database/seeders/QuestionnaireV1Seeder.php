<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Question;
use App\Models\QuestionnaireVersion;
use App\Models\Section;
use Illuminate\Database\Seeder;

class QuestionnaireV1Seeder extends Seeder
{
    public function run(): void
    {
        $version = QuestionnaireVersion::create([
            'version_number' => 1,
            'is_active' => true,
        ]);

        $sectionsData = $this->getSectionsData();

        foreach ($sectionsData as $sectionOrder => $sectionData) {
            $section = Section::create([
                'questionnaire_version_id' => $version->id,
                'name' => $sectionData['name'],
                'order' => $sectionOrder + 1,
                'response_profile' => $sectionData['response_profile'],
            ]);

            foreach ($sectionData['categories'] as $catOrder => $catData) {
                $category = Category::create([
                    'section_id' => $section->id,
                    'name' => $catData['name'],
                    'order' => $catOrder + 1,
                ]);

                foreach ($catData['questions'] as $qOrder => $questionText) {
                    Question::create([
                        'category_id' => $category->id,
                        'text' => $questionText,
                        'order' => $qOrder + 1,
                    ]);
                }
            }
        }
    }

    private function getSectionsData(): array
    {
        return [
            [
                'name' => ['pt_BR' => 'Existência e Qualidade da Informação', 'en' => 'Information Existence and Quality'],
                'response_profile' => 'information_quality',
                'categories' => [
                    [
                        'name' => ['pt_BR' => 'Pessoas/Atores', 'en' => 'People/Actors'],
                        'questions' => [
                            ['pt_BR' => 'Informações sobre os atores tais como: Nome, endereço, telefone, e-mail e responsável pela empresa?', 'en' => 'Information about the actors such as: Name, address, phone, email, and company representative?'],
                            ['pt_BR' => 'Informações que indicam quais são as agências de proteção de dados que regulamentam o uso dos dados pessoais pelos atores?', 'en' => 'Information indicating which data protection agencies regulate the use of personal data by the actors?'],
                            ['pt_BR' => 'Informações sobre o papel (função) de cada ator no uso dos dados pessoais?', 'en' => 'Information about the role (function) of each actor in the use of personal data?'],
                        ],
                    ],
                    [
                        'name' => ['pt_BR' => 'Propósito de uso', 'en' => 'Purpose of Use'],
                        'questions' => [
                            ['pt_BR' => 'Descrição do objetivo de uso dos dados pessoais.', 'en' => 'Description of the purpose of use of personal data.'],
                            ['pt_BR' => 'Informação sobre a lei/regulamentação que torna o uso dos dados pessoais legal.', 'en' => 'Information about the law/regulation that makes the use of personal data legal.'],
                            ['pt_BR' => 'Informações sobre quais dados pessoais serão utilizados para atingir os objetivos apontados.', 'en' => 'Information about which personal data will be used to achieve the stated objectives.'],
                            ['pt_BR' => 'Informação do ator responsável legal pelo uso dos dados pessoais.', 'en' => 'Information about the actor legally responsible for the use of personal data.'],
                            ['pt_BR' => 'Informações sobre a existência, ou não, da utilização ou processamento de dados pessoais feitas exclusivamente por computador, sem a supervisão humana.', 'en' => 'Information about whether or not personal data is used or processed exclusively by computer, without human supervision.'],
                            ['pt_BR' => 'Informações sobre o período de manipulação dos dados pessoais para o propósito indicado.', 'en' => 'Information about the period of personal data handling for the indicated purpose.'],
                        ],
                    ],
                    [
                        'name' => ['pt_BR' => 'Dados pessoais', 'en' => 'Personal Data'],
                        'questions' => [
                            ['pt_BR' => 'Informações de quais dados pessoais são utilizados.', 'en' => 'Information about which personal data is used.'],
                            ['pt_BR' => 'Descrição de como os dados pessoais são compostos (detalhes que possam explicar melhor os dados pessoais).', 'en' => 'Description of how personal data is composed (details that can better explain the personal data).'],
                            ['pt_BR' => 'Informações sobre a origem dos dados (dispositivos, compra de terceiros, compartilhamento etc).', 'en' => 'Information about the origin of the data (devices, third-party purchase, sharing, etc.).'],
                            ['pt_BR' => 'Em caso de obrigatoriedade da disponibilização dos dados pelos indivíduos, informações sobre o que pode ocorrer no caso da não coleta dos dados.', 'en' => 'In case of mandatory data provision by individuals, information about what may happen if the data is not collected.'],
                            ['pt_BR' => 'Informações sobre o objetivo do uso do dado pessoal e como (qual processo) é feito com o dado pessoal.', 'en' => 'Information about the purpose of using personal data and how (what process) it is done with the personal data.'],
                            ['pt_BR' => 'Informações sobre a permissão concedida pelo indivíduo para o uso dos dados pessoais.', 'en' => 'Information about the permission granted by the individual for the use of personal data.'],
                        ],
                    ],
                    [
                        'name' => ['pt_BR' => 'Compartilhamento', 'en' => 'Sharing'],
                        'questions' => [
                            ['pt_BR' => 'Informações de quais dados pessoais são transferidos ou compartilhados com terceiros.', 'en' => 'Information about which personal data is transferred or shared with third parties.'],
                            ['pt_BR' => 'Informações sobre o motivo da transferência e/ou compartilhamento dos dados pessoais.', 'en' => 'Information about the reason for the transfer and/or sharing of personal data.'],
                            ['pt_BR' => 'Informações sobre a base legal (lei/regulamentação) que garante a legalidade do compartilhamento dos dados.', 'en' => 'Information about the legal basis (law/regulation) that ensures the legality of data sharing.'],
                            ['pt_BR' => 'Dados completos do destinatário dos dados pessoais, de forma que permita a identificação e o contato com o destinatário.', 'en' => 'Complete data of the personal data recipient, allowing identification and contact with the recipient.'],
                            ['pt_BR' => 'Dados da organização que monitora o uso dos dados pessoais no país ou região do destinatário, de forma que permita a identificação e o contato com o órgão.', 'en' => 'Data of the organization that monitors the use of personal data in the recipient\'s country or region, allowing identification and contact with the agency.'],
                            ['pt_BR' => 'Relação de quais dados foram transferidos ou compartilhados e como foram obtidos.', 'en' => 'List of which data was transferred or shared and how it was obtained.'],
                            ['pt_BR' => 'Informações para relembrar como você permitiu e/ou autorizou o compartilhamento dos dados pessoais.', 'en' => 'Information to remind how you allowed and/or authorized the sharing of personal data.'],
                            ['pt_BR' => 'Informações sobre os eventos que causam a transferência/compartilhamento dos dados pessoais.', 'en' => 'Information about the events that cause the transfer/sharing of personal data.'],
                        ],
                    ],
                    [
                        'name' => ['pt_BR' => 'Agenciamento', 'en' => 'Agency'],
                        'questions' => [
                            ['pt_BR' => 'Informações de como o indivíduo pode solicitar cópia de seus dados, alteração de permissão de uso dos dados, realizar uma reclamação ou exercer qualquer direito sobre os seus dados.', 'en' => 'Information on how the individual can request a copy of their data, change data usage permission, file a complaint, or exercise any right over their data.'],
                            ['pt_BR' => 'Informações sobre meios de contato, telefones, e-mails sobre os atores envolvidos no uso dos dados pessoais.', 'en' => 'Information about contact means, phone numbers, and emails of the actors involved in the use of personal data.'],
                            ['pt_BR' => 'Informações e/ou recursos para o indivíduo solicitar cópia de seus dados, alteração de permissão de uso dos dados, realizar uma reclamação ou exercer qualquer direito sobre os seus dados diretamente no software, sem a necessidade de entrar em contato.', 'en' => 'Information and/or resources for the individual to request a copy of their data, change data usage permission, file a complaint, or exercise any right over their data directly in the software, without needing to make contact.'],
                        ],
                    ],
                ],
            ],
            [
                'name' => ['pt_BR' => 'Formato de Apresentação', 'en' => 'Presentation Format'],
                'response_profile' => 'presentation_format',
                'categories' => [
                    [
                        'name' => ['pt_BR' => 'Pessoas/Atores', 'en' => 'People/Actors'],
                        'questions' => [
                            ['pt_BR' => 'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações dos atores.', 'en' => 'Design elements (texts, figures, photos, etc.) used to present information about the actors.'],
                            ['pt_BR' => 'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise dos atores envolvidos no uso dos dados pessoais.', 'en' => 'Simplicity, objectivity, and relevance of information to effectively assist in analyzing the actors involved in the use of personal data.'],
                            ['pt_BR' => 'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia de grandes volumes de textos.', 'en' => 'Ease of access to information so as not to require the individual to perform complex searches or analyze/read large volumes of text.'],
                            ['pt_BR' => 'As informações existentes descartam a necessidade do indivíduo buscar informações em outras fontes.', 'en' => 'The existing information eliminates the need for the individual to search for information from other sources.'],
                        ],
                    ],
                    [
                        'name' => ['pt_BR' => 'Propósito de uso', 'en' => 'Purpose of Use'],
                        'questions' => [
                            ['pt_BR' => 'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre o(s) propósito(s) de uso dos dados.', 'en' => 'Design elements (texts, figures, photos, etc.) used to present information about the purpose(s) of data use.'],
                            ['pt_BR' => 'Simplicidade, objetividade e relevância das informações, de forma a auxiliar efetivamente na análise do(s) propósito(s) de uso dos dados pessoais.', 'en' => 'Simplicity, objectivity, and relevance of information to effectively assist in analyzing the purpose(s) of personal data use.'],
                            ['pt_BR' => 'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.', 'en' => 'Ease of access to information so as not to require the individual to perform complex searches or analyze/read large volumes of text.'],
                            ['pt_BR' => 'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.', 'en' => 'The existing information eliminates the need for the user to search for information from other sources.'],
                        ],
                    ],
                    [
                        'name' => ['pt_BR' => 'Dados pessoais', 'en' => 'Personal Data'],
                        'questions' => [
                            ['pt_BR' => 'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre os dados pessoais manipulados.', 'en' => 'Design elements (texts, figures, photos, etc.) used to present information about the personal data handled.'],
                            ['pt_BR' => 'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise do propósito de uso os dados pessoais.', 'en' => 'Simplicity, objectivity, and relevance of information to effectively assist in analyzing the purpose of personal data use.'],
                            ['pt_BR' => 'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.', 'en' => 'Ease of access to information so as not to require the individual to perform complex searches or analyze/read large volumes of text.'],
                            ['pt_BR' => 'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.', 'en' => 'The existing information eliminates the need for the user to search for information from other sources.'],
                        ],
                    ],
                    [
                        'name' => ['pt_BR' => 'Compartilhamento', 'en' => 'Sharing'],
                        'questions' => [
                            ['pt_BR' => 'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre a transferência/compartilhamento dos dados.', 'en' => 'Design elements (texts, figures, photos, etc.) used to present information about data transfer/sharing.'],
                            ['pt_BR' => 'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise da transferência/compartilhamento dos dados.', 'en' => 'Simplicity, objectivity, and relevance of information to effectively assist in analyzing data transfer/sharing.'],
                            ['pt_BR' => 'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.', 'en' => 'Ease of access to information so as not to require the individual to perform complex searches or analyze/read large volumes of text.'],
                            ['pt_BR' => 'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.', 'en' => 'The existing information eliminates the need for the user to search for information from other sources.'],
                        ],
                    ],
                    [
                        'name' => ['pt_BR' => 'Agenciamento', 'en' => 'Agency'],
                        'questions' => [
                            ['pt_BR' => 'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre agências de controle e ações para questionar ou verificar o uso dos dados.', 'en' => 'Design elements (texts, figures, photos, etc.) used to present information about control agencies and actions to question or verify data use.'],
                            ['pt_BR' => 'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise das agências de controle e ações para questionar ou verificar o uso dos dados.', 'en' => 'Simplicity, objectivity, and relevance of information to effectively assist in analyzing control agencies and actions to question or verify data use.'],
                            ['pt_BR' => 'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas or análise/leia grandes volumes de textos.', 'en' => 'Ease of access to information so as not to require the individual to perform complex searches or analyze/read large volumes of text.'],
                            ['pt_BR' => 'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.', 'en' => 'The existing information eliminates the need for the user to search for information from other sources.'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
