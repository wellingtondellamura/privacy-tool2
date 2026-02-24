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
            // Section 1: Pessoas/Atores (7 questions: 3 + 4)
            [
                'name' => 'Pessoas/Atores',
                'categories' => [
                    [
                        'name' => 'Existência e Qualidade da Informação',
                        'questions' => [
                            'Informações sobre os atores tais como: Nome, endereço, telefone, e-mail e responsável pela empresa?',
                            'Informações que indicam quais são as agências de proteção de dados que regulamentam o uso dos dados pessoais pelos atores?',
                            'Informações sobre o papel (função) de cada ator no uso dos dados pessoais?',
                        ],
                    ],
                    [
                        'name' => 'Formato de Apresentação',
                        'questions' => [
                            'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações dos atores.',
                            'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise dos atores envolvidos no uso dos dados pessoais.',
                            'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia de grandes volumes de textos.',
                            'As informações existentes descartam a necessidade do indivíduo buscar informações em outras fontes.',
                        ],
                    ],
                ],
            ],
            // Section 2: Propósito de uso (10 questions: 6 + 4)
            [
                'name' => 'Propósito de uso',
                'categories' => [
                    [
                        'name' => 'Existência e Qualidade da Informação',
                        'questions' => [
                            'Descrição do objetivo de uso dos dados pessoais.',
                            'Informação sobre a lei/regulamentação que torna o uso dos dados pessoais legal.',
                            'Informações sobre quais dados pessoais serão utilizados para atingir os objetivos apontados.',
                            'Informação do ator responsável legal pelo uso dos dados pessoais.',
                            'Informações sobre a existência, ou não, da utilização ou processamento de dados pessoais feitas exclusivamente por computador, sem a supervisão humana.',
                            'Informações sobre o período de manipulação dos dados pessoais para o propósito indicado.',
                        ],
                    ],
                    [
                        'name' => 'Formato de Apresentação',
                        'questions' => [
                            'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre o(s) propósito(s) de uso dos dados.',
                            'Simplicidade, objetividade e relevância das informações, de forma a auxiliar efetivamente na análise do(s) propósito(s) de uso dos dados pessoais.',
                            'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.',
                            'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.',
                        ],
                    ],
                ],
            ],
            // Section 3: Dados pessoais (10 questions: 6 + 4)
            [
                'name' => 'Dados pessoais',
                'categories' => [
                    [
                        'name' => 'Existência e Qualidade da Informação',
                        'questions' => [
                            'Informações de quais dados pessoais são utilizados.',
                            'Descrição de como os dados pessoais são compostos (detalhes que possam explicar melhor os dados pessoais).',
                            'Informações sobre a origem dos dados (dispositivos, compra de terceiros, compartilhamento etc).',
                            'Em caso de obrigatoriedade da disponibilização dos dados pelos indivíduos, informações sobre o que pode ocorrer no caso da não coleta dos dados.',
                            'Informações sobre o objetivo do uso do dado pessoal e como (qual processo) é feito com o dado pessoal.',
                            'Informações sobre a permissão concedida pelo indivíduo para o uso dos dados pessoais.',
                        ],
                    ],
                    [
                        'name' => 'Formato de Apresentação',
                        'questions' => [
                            'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre os dados pessoais manipulados.',
                            'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise do propósito de uso os dados pessoais.',
                            'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.',
                            'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.',
                        ],
                    ],
                ],
            ],
            // Section 4: Compartilhamento (12 questions: 8 + 4)
            [
                'name' => 'Compartilhamento',
                'categories' => [
                    [
                        'name' => 'Existência e Qualidade da Informação',
                        'questions' => [
                            'Informações de quais dados pessoais são transferidos ou compartilhados com terceiros.',
                            'Informações sobre o motivo da transferência e/ou compartilhamento dos dados pessoais.',
                            'Informações sobre a base legal (lei/regulamentação) que garante a legalidade do compartilhamento dos dados.',
                            'Dados completos do destinatário dos dados pessoais, de forma que permita a identificação e o contato com o destinatário.',
                            'Dados da organização que monitora o uso dos dados pessoais no país ou região do destinatário, de forma que permita a identificação e o contato com o órgão.',
                            'Relação de quais dados foram transferidos ou compartilhados e como foram obtidos.',
                            'Informações para relembrar como você permitiu e/ou autorizou o compartilhamento dos dados pessoais.',
                            'Informações sobre os eventos que causam a transferência/compartilhamento dos dados pessoais.',
                        ],
                    ],
                    [
                        'name' => 'Formato de Apresentação',
                        'questions' => [
                            'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre a transferência/compartilhamento dos dados.',
                            'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise da transferência/compartilhamento dos dados.',
                            'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.',
                            'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.',
                        ],
                    ],
                ],
            ],
            // Section 5: Agenciamento (7 questions: 3 + 4)
            [
                'name' => 'Agenciamento',
                'categories' => [
                    [
                        'name' => 'Existência e Qualidade da Informação',
                        'questions' => [
                            'Informações de como o indivíduo pode solicitar cópia de seus dados, alteração de permissão de uso dos dados, realizar uma reclamação ou exercer qualquer direito sobre os seus dados.',
                            'Informações sobre meios de contato, telefones, e-mails sobre os atores envolvidos no uso dos dados pessoais.',
                            'Informações e/ou recursos para o indivíduo solicitar cópia de seus dados, alteração de permissão de uso dos dados, realizar uma reclamação ou exercer qualquer direito sobre os seus dados diretamente no software, sem a necessidade de entrar em contato.',
                        ],
                    ],
                    [
                        'name' => 'Formato de Apresentação',
                        'questions' => [
                            'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre agências de controle e ações para questionar ou verificar o uso dos dados.',
                            'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise das agências de controle e ações para questionar ou verificar o uso dos dados.',
                            'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.',
                            'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.',
                        ],
                    ],
                ],
            ],
        ];
    }
}
