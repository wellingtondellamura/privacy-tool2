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

                foreach ($catData['questions'] as $qOrder => $qData) {
                    Question::create([
                        'category_id' => $category->id,
                        'text' => $qData['text'],
                        'order' => $qOrder + 1,
                        'tooltip' => $qData['tooltip'],
                        'good_practice_example' => $qData['good_practice_example'],
                        'bad_practice_example' => $qData['bad_practice_example'],
                    ]);
                }
            }
        }
    }

    private function getSectionsData(): array
    {
        return [
            [
                'name' => [
                    'pt_BR' => 'Existência e Qualidade da Informação',
                    'en' => 'Information Existence and Quality',
                ],
                'response_profile' => 'information_quality',
                'categories' => [
                    [
                        'name' => [
                            'pt_BR' => 'Pessoas/Atores',
                            'en' => 'People/Actors',
                            'es' => 'Personas/Actores',
                        ],
                        'questions' => [
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre os atores tais como: Nome, endereço, telefone, e-mail e responsável pela empresa?',
                                    'en' => 'Information about the actors such as: Name, address, phone, email, and company representative?',
                                    'es' => 'Información sobre los actores tales como: ¿Nombre, dirección, teléfono, correo electrónico y responsable de la empresa?',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar a informação: Tanto em sites brasileiros quanto americanos, essas informações costumam (ou deveria) ficar nas seções "Política de Privacidade" (Privacy Policy), "Aviso de Privacidade" (Privacy Notice) ou "Fale Conosco" (Contact Us).
Como julgar (Dica Prática): Marque Suficiente se o software fornece a identificação completa: nome da empresa, endereço físico, telefone, um e-mail de contato e cita o Encarregado de Dados/DPO.
Marque Insuficiente se a empresa disponibiliza apenas um formulário de contato genérico (sem telefone ou endereço) ou apenas um e-mail vago como contato@empresa.com, pois a informação está presente, mas é incompleta ou pouco clara.
Marque Inexistente se não há nenhuma menção sobre quem controla os dados.',
                                    'en' => 'Where to find the information: On both Brazilian and American websites, this information is usually (or should be) found in the “Privacy Policy,” “Privacy Notice,” or “Contact Us” sections.
How to evaluate (Practical Tip): Rate as “Sufficient” if the software provides complete identification: company name, physical address, phone number, a contact email address, and mentions the Data Protection Officer (DPO).
Select “Insufficient” if the company provides only a generic contact form (without a phone number or address) or only a vague email address such as contato@empresa.com, since the information is present but incomplete or unclear.
Select “Nonexistent” if there is no mention of who controls the data.',
                                    'es' => 'Dónde buscar la información: Tanto en sitios web brasileños como internacionales, esta información suele (o debería) encontrarse en las secciones "Política de Privacidad" (Privacy Policy), "Aviso de Privacidad" (Privacy Notice) o "Contáctenos" (Contact Us).
Cómo evaluar (Consejo Práctico): Marque Suficiente si el software proporciona la identificación completa: nombre de la empresa, dirección física, teléfono, un correo de contacto y menciona al Delegado de Protección de Datos/DPO.
Marque Insuficiente si la empresa proporciona solo un formulario de contacto genérico (sin teléfono o dirección) o solo un correo vago como contacto@empresa.com, ya que la información está presente, pero es incompleta o poco clara.
Marque Inexistente si no hay ninguna mención sobre quién controla los datos.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Apresentação dos metadados completos de identificação (nome, endereço, telefone e outros dados de contato)
É fundamental declarar de forma clara qual é o papel ou a tarefa que esse actor exerce no tratamento dos dados pessoais.

Exemplo de Boa Prática:
Controlador de Dados (Controller): Empresa de Tecnologia S.A. 
Função: Responsável por definir a coleta e o uso dos seus dados pessoais. 
Endereço: Avenida Principal, 1000 - São Paulo, SP. 
Telefone: (11) 9999-9999

Encarregado de Proteção de Dados / DPO (Protection Office): João Silva 
E-mail direto para assuntos de privacidade: privacidade@empresadetecnologia.com.br

Apresentar as informações dessa maneira é considerado o formato ideal porque não apenas lista os dados de contato do responsável, mas também cumpre a diretriz específica do TR-Model de classificar o tipo do ator na operação, indicando explicitamente se ele atua como o Controlador (Controller), o Escritório/Encarregado de Proteção de Dados (Protection Office) ou um Destinatário (Recipient).',
                                    'en' => 'Provision of complete identifying metadata (name, address, phone number, and other contact information)
It is essential to clearly state the role or task that this party performs in the processing of personal data.

Example of Best Practice:
Data Controller: Technology Company, Inc. 
Role: Responsible for determining the collection and use of your personal data. 
Address: Main Avenue, 1000 - São Paulo, SP. 
Phone: (11) 9999-9999

Data Protection Officer (DPO): João Silva 
Direct email for privacy matters: privacidade@empresadetecnologia.com.br

Presenting the information in this manner is considered the ideal format because it not only lists the contact information of the responsible party but also complies with the specific TR-Model guideline to classify the type of actor in the operation, explicitly indicating whether they act as the Controller, the Data Protection Office, or a Recipient.',
                                    'es' => 'Presentación de los metadatos completos de identificación (nombre, dirección, teléfono y otros datos de contacto)
Es fundamental declarar de forma clara cuál es el rol o la tarea que este actor ejerce en el tratamiento de los datos personales.

Ejemplo de Buena Práctica:
Controlador de Datos (Controller): Empresa de Tecnología S.A. 
Función: Responsable de definir la recopilación y el uso de sus datos personales. 
Dirección: Avenida Principal, 1000 - São Paulo, SP. 
Teléfono: (11) 9999-9999

Delegado de Protección de Datos / DPO (Protection Office): João Silva 
Correo directo para asuntos de privacidad: privacidad@empresadetecnologia.com.br

Presentar la información de esta manera se considera el formato ideal porque no solo enumera los datos de contacto del responsable, sino que también cumple la directriz específica del TR-Model de clasificar el tipo de actor en la operación, indicando explícitamente si actúa como el Controlador (Controller), la Oficina/Delegado de Protección de Datos (Protection Office) o un Destinatario (Recipient).',
                                ],
                                'good_practice_example_raw' => '',
                                'bad_practice_example' => [
                                    'pt_BR' => 'A aplicação não fornece os metadados mínimos exigidos (nome, endereço, telefone, e-mail) ou não deixa claro qual é o papel daquele ator (ex: Controlador ou Encarregado de Dados).

Exemplo 1: Informação Insuficiente (Vale 50 pontos)
"Nós nos preocupamos com seus dados. Para qualquer dúvida sobre sua privacidade ou sobre esta política, envie um e-mail para contato@empresa.com."

Por que é uma má prática? A informação até existe, mas é considerada "incompleta, muito genérica ou pouco clara"
Faltam dados cruciais definidos pelo TR-Model, como o endereço físico da empresa, um número de telefone, a identificação clara de quem é o responsável (DPO) e a definição explícita do papel da empresa como "Controladora".

Exemplo 2: Informação Inexistente (Vale 0 pontos)
O aplicativo possui uma Política de Privacidade que apenas descreve que coleta cookies e dados de navegação, mas no final da página disponibiliza apenas um formulário em branco escrito "Fale Conosco", sem mencionar o nome da empresa responsável pelos dados em lugar nenhum.
Por que é uma má prática? Neste caso, a informação sobre os atores não está disponível na aplicação. 
O titular dos dados não tem como saber legalmente quem é a empresa que está processando suas informações e não possui canais diretos e identificáveis (como telefone ou e-mail do encarregado) para exercer seus direitos de privacidade.',
                                    'en' => 'The application does not provide the minimum required metadata (name, address, phone number, email) or does not make clear what that party’s role is (e.g., Data Controller or Data Processor).

Example 1: Insufficient Information (Worth 50 points)
“We care about your data. If you have any questions about your privacy or this policy, please email contato@empresa.com.”

Why is this a bad practice? The information is present, but it is considered “incomplete, too generic, or unclear.”
Crucial details defined by the TR-Model are missing, such as the company’s physical address, a phone number, clear identification of who is responsible (DPO), and an explicit definition of the company’s role as a “Data Controller.”

Example 2: Missing Information (0 points)
The app has a Privacy Policy that merely states it collects cookies and browsing data, but at the bottom of the page, it only provides a blank form labeled “Contact Us,” without mentioning the name of the company responsible for the data anywhere.
Why is this a bad practice? In this case, information about the parties involved is not available in the app. 
The data subject has no legal way of knowing which company is processing their information and lacks direct, identifiable channels (such as a phone number or email address for the person in charge) to exercise their privacy rights.',
                                    'es' => 'La aplicación no proporciona los metadatos mínimos exigidos (nombre, dirección, teléfono, correo) o no deja claro cuál es el rol de ese actor (ej: Controlador o Encargado de Datos).

Ejemplo 1: Información Insuficiente (Vale 50 puntos)
"Nos preocupamos por sus datos. Para cualquier duda sobre su privacidad o sobre esta política, envíe un correo a contacto@empresa.com."

¿Por qué es una mala práctica? La información existe, pero se considera "incompleta, muy genérica o poco clara"
Faltan datos cruciales definidos por el TR-Model, como la dirección física de la empresa, un número de teléfono, la identificación clara de quién es el responsable (DPO) y la definición explícita del rol de la empresa como "Controladora".

Ejemplo 2: Información Inexistente (Vale 0 puntos)
La aplicación tiene una Política de Privacidad que solo describe que recopila cookies y datos de navegación, pero al final de la página solo proporciona un formulario en blanco titulado "Contáctenos", sin mencionar el nombre de la empresa responsable de los datos en ningún lado.
¿Por qué es una mala práctica? En este caso, la información sobre los actores no está disponible en la aplicación. 
El titular de los datos no tiene forma legal de saber qué empresa está procesando su información y no posee canales directos e identificables (como teléfono o correo del encargado) para ejercer sus derechos de privacidad.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações que indicam quais são as agências de proteção de dados que regulamentam o uso dos dados pessoais pelos atores?',
                                    'en' => 'Information indicating which data protection agencies regulate the use of personal data by the actors?',
                                    'es' => '¿Información que indica cuáles son las agencias de protección de datos que regulan el uso de los datos personales por parte de los actores?',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar a informação: Nas seções de conformidade, base legal ou nos direitos do titular da Política de Privacidade.
Como julgar: Marque Suficiente se cita explicitamente a autoridade reguladora (ex: ANPD no Brasil, DPC na Irlanda, etc.) com links ou meios de contato. Marque Insuficiente se apenas diz "obedecemos às leis locais" sem especificar qual órgão supervisiona. Marque Inexistente se não há qualquer menção a autoridades de regulação.',
                                    'en' => 'Where to find the information: In compliance, legal basis, or user rights sections of the Privacy Policy.
How to evaluate: Rate as "Sufficient" if it explicitly names the regulatory authority (e.g., ANPD in Brazil, DPC in Ireland) with links or contact info. Rate as "Insufficient" if it vague states "we comply with local laws" without naming the agency. Rate as "Nonexistent" if there is no mention of regulatory bodies.',
                                    'es' => 'Dónde buscar la información: En las secciones de cumplimiento, base legal o en los derechos del titular de la Política de Privacidad.
Cómo evaluar: Marque Suficiente si cita explícitamente a la autoridad reguladora (ej: AEPD en España, ANPD en Brasil, etc.) con enlaces o medios de contacto. Marque Insuficiente si solo dice "obedecemos las leyes locales" sin especificar qué organismo supervisa. Marque Inexistente si no hay ninguna mención a autoridades reguladoras.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'A empresa declara: "Nossas operações são supervisionadas pela Autoridade Nacional de Proteção de Dados (ANPD). Caso queira registrar uma reclamação, você pode acessar o portal oficial deles em anpd.gov.br ou enviar um e-mail para anpd@anpd.gov.br."',
                                    'en' => 'The company states: "Our data processing practices are regulated by the Data Protection Commission (DPC). You can file complaints directly with them via their portal at dataprotection.ie or by emailing info@dataprotection.ie."',
                                    'es' => 'La empresa declara: "Nuestras operaciones son supervisadas por la Agencia Española de Protección de Datos (AEPD). Si desea registrar una queja, puede acceder a su portal oficial en aepd.es o enviar un correo a contacto@aepd.es."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Processamos dados sob a regulamentação governamental aplicável à nossa sede." (Não diz qual é a agência).
Exemplo 2 (Inexistente): Nenhuma menção a agências ou órgãos fiscalizadores de privacidade.',
                                    'en' => 'Example 1 (Insufficient): "We process data under the applicable government regulations at our headquarters." (Does not name the agency).
Example 2 (Nonexistent): Absolutely no mention of regulatory or supervisory privacy bodies.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Procesamos datos bajo la regulación gubernamental aplicable a nuestra sede." (No menciona cuál es la agencia).
Ejemplo 2 (Inexistente): Ninguna mención a agencias u organismos supervisores de privacidad.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre o papel (função) de cada actor no uso dos dados pessoais?',
                                    'en' => 'Information about the role (function) of each actor in the use of personal data?',
                                    'es' => '¿Información sobre el rol (función) de cada actor en el uso de los datos personales?',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar a informação: Seções de definições ou atribuições de responsabilidade.
Como julgar: Marque Suficiente se define claramente quem atua como Controlador, Processador/Operador, Destinatário ou Encarregado. Marque Insuficiente se cita terceiros ou parceiros mas não define o papel jurídico/técnico deles no fluxo. Marque Inexistente se não define a função de nenhum ator.',
                                    'en' => 'Where to find the information: In definitions or responsibility assignment sections.
How to evaluate: Rate as "Sufficient" if it clearly defines who acts as Controller, Processor, Recipient, or DPO. Rate as "Insufficient" if it mentions third parties but fails to specify their legal or technical role. Rate as "Nonexistent" if roles are not defined.',
                                    'es' => 'Dónde buscar la información: Secciones de definiciones o asignación de responsabilidades.
Cómo evaluar: Marque Suficiente si define claramente quién actúa como Controlador, Procesador/Encargado, Destinatario o DPO. Marque Insuficiente si menciona terceros o socios pero no define su rol jurídico/técnico en el flujo. Marque Inexistente si no define la función de ningún actor.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Apresentar em tabela:
Controlador: Empresa ABC Ltda. (define as finalidades)
Operador: Serviço de Nuvem XYZ (hospeda o banco de dados)
Encarregado (DPO): Marcos Silva (atende os titulares).',
                                    'en' => 'Presenting in a table:
Data Controller: ABC Company Ltd. (defines purposes)
Data Processor: XYZ Cloud Hosting (hosts the database)
DPO: Marcos Silva (handles user requests).',
                                    'es' => 'Presentar en tabla:
Controlador: Empresa ABC Ltda. (define los fines)
Encargado: Servicio de Nube XYZ (aloja la base de datos)
Delegado (DPO): Marcos Silva (atiende a los titulares).',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Seus dados podem ser compartilhados com nossos parceiros tecnológicos." (Sem definir quem é o operador ou controlador).
Exemplo 2 (Inexistente): Sem qualquer definição de papéis ou funções.',
                                    'en' => 'Example 1 (Insufficient): "We share details with technological partners." (Without specifying controller or processor roles).
Example 2 (Nonexistent): No roles or definitions specified.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Sus datos pueden ser compartidos con nuestros socios tecnológicos." (Sin definir quién es el encargado o controlador).
Ejemplo 2 (Inexistente): Sin ninguna definición de roles o funciones.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'pt_BR' => 'Propósito de uso',
                            'en' => 'Purpose of Use',
                            'es' => 'Propósito de uso',
                        ],
                        'questions' => [
                            [
                                'text' => [
                                    'pt_BR' => 'Descrição do objetivo de uso dos dados pessoais.',
                                    'en' => 'Description of the purpose of use of personal data.',
                                    'es' => 'Descripción del objetivo de uso de los datos personales.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção "Como usamos seus dados" ou "Propósitos de tratamento".
Como julgar: Suficiente: Lista detalhadamente cada finalidade específica vinculando-a aos respectivos dados. Insuficiente: Finalidades muito amplas e genéricas como "melhorar sua experiência" ou "fins comerciais". Inexistente: Sem qualquer menção aos objetivos.',
                                    'en' => 'Where to find: In sections like "How we use your data" or "Purposes of processing".
How to evaluate: Sufficient: Lists detailed, specific purposes mapped to data types. Insufficient: Extremely broad and generic purposes like "improving your experience" or "business purposes". Nonexistent: No description of goals.',
                                    'es' => 'Dónde buscar: En la sección "Cómo usamos sus datos" o "Fines del tratamiento".
Cómo evaluar: Suficiente: Enumera detalladamente cada fin específico vinculándolo a los datos respectivos. Insuficiente: Fines muy amplios y genéricos como "mejorar su experiencia" o "fines comerciales". Inexistente: Sin ninguna mención a los objetivos.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Coletamos seu e-mail especificamente para enviar nossa newsletter semanal e confirmar transações financeiras. O seu histórico de compras é usado para gerar recomendações personalizadas no seu feed."',
                                    'en' => '"We collect your email address strictly to send our monthly newsletter and confirm financial transactions. Your purchase history is analyzed to display personalized recommendations on your feed."',
                                    'es' => '"Recopilamos su correo electrónico específicamente para enviar nuestro boletín semanal y confirmar transacciones financieras. Su historial de compras se usa para generar recomendaciones personalizadas en su feed."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Coletamos seus dados para fins gerais da plataforma e publicidade comercial." (Muito genérico).
Exemplo 2 (Inexistente): A política não explica por que coleta ou usa os dados.',
                                    'en' => 'Example 1 (Insufficient): "We collect your data for general platform improvements and commercial advertisements." (Too generic).
Example 2 (Nonexistent): The policy does not explain the reasons for collecting or using data.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Recopilamos sus datos para fines generales de la plataforma y publicidad comercial." (Muy genérico).
Ejemplo 2 (Inexistente): La política no explica por qué recopila o usa los datos.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informação sobre a lei/regulamentação que torna o uso dos dados pessoais legal.',
                                    'en' => 'Information about the law/regulation that makes the use of personal data legal.',
                                    'es' => 'Información sobre la ley/reglamentación que hace legal el uso de los datos personales.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção "Bases Legais" ou "Fundamentação jurídica".
Como julgar: Suficiente: Aponta as bases legais (ex: consentimento, execução de contrato, legítimo interesse) correlacionadas com a LGPD ou GDPR. Insuficiente: Apenas cita a lei genericamente (ex: "estamos em conformidade com a LGPD") sem vincular à base específica do tratamento. Inexistente: Sem menção a bases legais.',
                                    'en' => 'Where to find: In the "Legal Bases" or "Lawful basis for processing" section.
How to evaluate: Sufficient: Explicitly names the legal bases (consent, performance of contract, legitimate interest) mapped to the processing. Insufficient: Vaguely mentions compliance with a law overall without identifying specific bases. Nonexistent: No legal basis cited.',
                                    'es' => 'Dónde buscar: En la sección "Bases Legales" o "Fundamentación jurídica".
Cómo evaluar: Suficiente: Señala las bases legales (ej: consentimiento, ejecución de contrato, interés legítimo) correlacionadas con la normativa. Insuficiente: Solo cita la ley genéricamente (ej: "cumplimos con el RGPD") sin vincular a la base específica del tratamiento. Inexistente: Sin mención a bases legales.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"O tratamento de seu nome e CPF é realizado com base na Execução de Contrato (Art. 7º, V da LGPD). O envio de e-mails promocionais é feito sob Consentimento (Art. 7º, I da LGPD)."',
                                    'en' => '"Processing of your name and payment info is based on the Performance of a Contract (Art. 6(1)(b) of GDPR). Promotional emails are sent based on your Consent (Art. 6(1)(a) of GDPR)."',
                                    'es' => '"El tratamiento de su nombre e identificación fiscal se realiza con base en la Ejecución de un Contrato. El envío de correos promocionales se realiza bajo Consentimiento explícito."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Respeitamos toda a legislação de privacidade brasileira vigente no tratamento de dados." (Não aponta a base específica).
Exemplo 2 (Inexistente): Sem qualquer justificativa ou base jurídica.',
                                    'en' => 'Example 1 (Insufficient): "We comply with all relevant global privacy laws in our processing." (Lacks specific lawful bases mapping).
Example 2 (Nonexistent): No legal references or bases whatsoever.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Respetamos toda la legislación de privacidad vigente en el tratamiento de datos." (No señala la base específica).
Ejemplo 2 (Inexistente): Sin ninguna justificación o base jurídica.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre quais dados pessoais serão utilizados para atingir os objetivos apontados.',
                                    'en' => 'Information about which personal data will be used to achieve the stated objectives.',
                                    'es' => 'Información sobre qué datos personales se utilizarán para alcanzar los objetivos señalados.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na tabela de finalidades ou seções de dados coletados.
Como julgar: Suficiente: Mapeia de forma explícita a relação (ex: Finalidade X exige Dados Y e Z). Insuficiente: Lista os dados de um lado e as finalidades do outro, sem cruzá-los claramente. Inexistente: Sem correlação.',
                                    'en' => 'Where to find: In purposes table or data collection section.
How to evaluate: Sufficient: Explicitly maps the relation (e.g., Purpose X requires Data Y and Z). Insufficient: Lists data types and lists purposes separately without clarifying which data is used for which purpose. Nonexistent: No mapping.',
                                    'es' => 'Dónde buscar: En la tabla de fines o secciones de datos recopilados.
Cómo evaluar: Suficiente: Mapea de forma explícita la relación (ej: Finalidad X requiere Datos Y y Z). Insuficiente: Enumera los datos por un lado y los fines por el otro, sin cruzarlos claramente. Inexistente: Sin correlación.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Tabela relacionando:
Finalidade: "Entrega de Produtos" -> Dados Usados: "Nome completo, Endereço de entrega, Telefone".
Finalidade: "Segurança do login" -> Dados Usados: "IP, Dados do dispositivo".',
                                    'en' => 'Mapping table:
Purpose: "Product Delivery" -> Data Used: "Full name, Delivery address, Phone".
Purpose: "Account Security" -> Data Used: "IP address, Device characteristics".',
                                    'es' => 'Tabla relacionando:
Finalidad: "Entrega de Productos" -> Datos Usados: "Nombre completo, Dirección de entrega, Teléfono".
Finalidad: "Seguridad del inicio de sesión" -> Datos Usados: "IP, Datos del dispositivo".',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Coletamos dados cadastrais e de navegação. Usamos dados para entregar produtos e exibir publicidade." (Não diz quais dados vão para qual finalidade).
Exemplo 2 (Inexistente): Sem relação.',
                                    'en' => 'Example 1 (Insufficient): "We collect registration and browsing data. We use data to ship orders and display ads." (Doesn\'t specify which data goes to which activity).
Example 2 (Nonexistent): No linkage whatsoever.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Recopilamos datos de registro y de navegación. Usamos datos para entregar productos y mostrar publicidad." (No dice qué datos van para qué finalidad).
Ejemplo 2 (Inexistente): Sin relación.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informação do ator responsável legal pelo uso dos dados pessoais.',
                                    'en' => 'Information about the actor legally responsible for the use of personal data.',
                                    'es' => 'Información del actor responsable legal por el uso de los datos personales.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Seções de responsabilidades, termos de uso ou introdução da Política de Privacidade.
Como julgar: Suficiente: Declara explicitamente qual entidade jurídica (Razão Social, CNPJ) é a controladora responsável legal. Insuficiente: Cita apenas a marca fantasia ou nome informal sem dados legais (ex: "nossa plataforma"). Inexistente: Sem identificação de responsável legal.',
                                    'en' => 'Where to find: Liability, terms of service, or introduction of the Privacy Policy.
How to evaluate: Sufficient: Explicitly states which legal entity (Corporate Name, tax ID) is the Data Controller. Insufficient: Mentions only the brand name or informal name (e.g., "our app") without legal registry data. Nonexistent: No legal controller identified.',
                                    'es' => 'Dónde buscar: Secciones de responsabilidades, términos de uso o introducción de la Política de Privacidad.
Cómo evaluar: Suficiente: Declara explícitamente qué entidad jurídica (Razón Social, NIF/CIF) es la controladora responsable legal. Insuficiente: Solo cita el nombre comercial o nombre informal sin datos legales (ej: "nuestra plataforma"). Inexistente: Sin identificación del responsable legal.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"A pessoa jurídica responsável pelo tratamento dos seus dados (Controladora) é a Alfa Soluções de Software Ltda., inscrita sob o CNPJ nº 00.000.000/0001-00."',
                                    'en' => '"The legal entity responsible for processing your data (Data Controller) is Alpha Software Solutions LLC, registered with tax ID 00-0000000."',
                                    'es' => '"La persona jurídica responsable del tratamiento de sus datos (Controladora) es Alpha Soluciones de Software S.L., registrada bajo el NIF B-00000000."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Esta política é mantida pelo time da PrivacyApp." (Marca fantasia sem dados legais).
Exemplo 2 (Inexistente): Nenhuma identificação de quem é o responsável legal pela guarda dos dados.',
                                    'en' => 'Example 1 (Insufficient): "This policy is maintained by the PrivacyApp team." (Brand name only, missing official corporate registration).
Example 2 (Nonexistent): No entity identified as legally responsible for the data.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Esta política es mantenida por el equipo de PrivacyApp." (Nombre comercial sin datos legales).
Ejemplo 2 (Inexistente): Ninguna identificación de quién es el responsable legal por la custodia de los datos.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre a existência, ou não, da utilização ou processamento de dados pessoais feitas exclusivamente por computador, sem a supervisão humana.',
                                    'en' => 'Information about whether or not personal data is used or processed exclusively by computer, without human supervision.',
                                    'es' => 'Información sobre la existencia, o no, del uso o procesamiento de datos personales de forma exclusivamente automatizada, sin supervisión humana.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Seções sobre decisões automatizadas, inteligência artificial ou perfilamento (profiling).
Como julgar: Suficiente: Explica claramente se há decisões 100% automatizadas (ex: análise de crédito automatizada), descrevendo a lógica e os direitos de revisão. Insuficiente: Apenas diz que usa algoritmos sem explicar os impactos ou se há intervenção humana. Inexistente: Sem menção a processos automatizados.',
                                    'en' => 'Where to find: Sections discussing automated decision-making, artificial intelligence, or profiling.
How to evaluate: Sufficient: Explains clearly if there are 100% automated decisions (e.g., credit scoring), detailing the logic and user rights to request manual review. Insufficient: Vaguely mentions using algorithms without explaining outcomes or human involvement. Nonexistent: No mention of automated decisions.',
                                    'es' => 'Dónde buscar: Secciones sobre decisiones automatizadas, inteligencia artificial o elaboración de perfiles (profiling).
Cómo evaluar: Suficiente: Explica claramente si hay decisiones 100% automatizadas (ej: análisis de crédito automatizado), describiendo la lógica y los derechos de revisión. Insuficiente: Solo dice que usa algoritmos sin explicar los impactos o si hay intervención humana. Inexistente: Sin mención a procesos automatizados.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Realizamos análise automática de crédito via algoritmo para liberação instantânea de limite. Essa decisão é exclusivamente computacional. Caso discorde, você pode solicitar a revisão humana entrando em contato no e-mail revisao@empresa.com."',
                                    'en' => '"We perform automated credit risk scoring using algorithms to determine loan approval. This decision is entirely automated. You have the right to request a manual review by contacting review@company.com."',
                                    'es' => '"Realizamos un análisis automático de crédito mediante un algoritmo para la aprobación instantánea de límites. Esta decisión es exclusivamente computacional. Si no está de acuerdo, puede solicitar la revisión humana contactando al correo revision@empresa.com."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Usamos algoritmos avançados para melhorar suas ofertas." (Não deixa claro se a decisão é 100% automatizada e se afeta direitos).
Exemplo 2 (Inexistente): Sem informações sobre o tema.',
                                    'en' => 'Example 1 (Insufficient): "We use advanced algorithms to optimize offers." (Doesn\'t specify if decisions are purely automated and how users can challenge them).
Example 2 (Nonexistent): No mention of algorithms or automated processing.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Usamos algoritmos avanzados para mejorar sus ofertas." (No deja claro si la decisión es 100% automatizada y si afecta derechos).
Ejemplo 2 (Inexistente): Sin información sobre el tema.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre o período de manipulação dos dados pessoais para o propósito indicado.',
                                    'en' => 'Information about the period of personal data handling for the indicated purpose.',
                                    'es' => 'Información sobre el período de manipulación de los datos personales para el propósito indicado.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção "Retenção de dados" ou "Por quanto tempo guardamos seus dados".
Como julgar: Suficiente: Indica prazos específicos (ex: 5 anos, duração da conta) ou critérios claros para definição. Insuficiente: Termos vagos como "guardamos pelo tempo que julgarmos necessário". Inexistente: Sem prazos.',
                                    'en' => 'Where to find: In "Data Retention" or "How long we store your data" sections.
How to evaluate: Sufficient: Specifies concrete retention periods (e.g., 5 years, duration of account) or clear evaluation criteria. Insufficient: Vague terms like "we store data for as long as we see fit". Nonexistent: No retention info.',
                                    'es' => 'Dónde buscar: En la sección "Retención de datos" o "Cuánto tiempo conservamos sus datos".
Cómo evaluar: Suficiente: Indica plazos específicos (ej: 5 años, duración de la cuenta) o criterios claros para su definición. Insuficiente: Términos vagos como "conservamos por el tiempo que consideremos necesario". Inexistente: Sin plazos.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Seus dados cadastrais são armazenados por até 5 anos após o encerramento do contrato para atender obrigações fiscais e regulatórias. Logs de acesso são excluídos após 6 meses (exigência do Marco Civil da Internet)."',
                                    'en' => '"Your account profile data is stored for up to 5 years after account termination to meet tax and regulatory obligations. Access logs are deleted automatically after 6 months (under local regulations)."',
                                    'es' => '"Sus datos de registro se almacenan hasta por 5 años después de la terminación del contrato para cumplir con obligaciones fiscales y regulatorias. Los registros de acceso se eliminan después de 6 meses (exigencia legal)."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Armazenamos seus dados por tempo indeterminado ou conforme considerarmos necessário."
Exemplo 2 (Inexistente): Sem qualquer seção de retenção ou prazos.',
                                    'en' => 'Example 1 (Insufficient): "We store your information indefinitely or for as long as we consider it useful."
Example 2 (Nonexistent): No data retention policy details are stated.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Almacenamos sus datos por tiempo indefinido o según consideremos necesario."
Ejemplo 2 (Inexistente): Sin ninguna sección de retención o plazos.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'pt_BR' => 'Dados pessoais',
                            'en' => 'Personal Data',
                        ],
                        'questions' => [
                            [
                                'text' => [
                                    'pt_BR' => 'Informações de quais dados pessoais são utilizados.',
                                    'en' => 'Information about which personal data is used.',
                                    'es' => 'Información sobre qué datos personales se utilizan.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Seção "Dados Coletados" ou "Quais dados tratamos".
Como julgar: Suficiente: Lista as categorias e itens de dados detalhadamente (ex: nome, CPF, geolocalização, dados bancários). Insuficiente: Agrupa tudo em termos amplos sem discriminar (ex: "dados cadastrais e outras informações úteis"). Inexistente: Sem lista.',
                                    'en' => 'Where to find: "Data Collected" or "Which data we process" section.
How to evaluate: Sufficient: Lists categories and specific items of data collected (e.g., name, national identifier, geolocation, financial logs). Insufficient: Groups everything under generic headers without detail (e.g., "sign-up info and other useful data"). Nonexistent: No list.',
                                    'es' => 'Dónde buscar: Sección "Datos Recopilados" o "Qué datos tratamos".
Cómo evaluar: Suficiente: Enumera las categorías y elementos de datos detalladamente (ej: nombre, NIF/CIF, geolocalización, datos bancarios). Insuficiente: Agrupa todo en términos amplios sin discriminar (ej: "datos de registro y otra información útil"). Inexistente: Sin lista.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Coletamos os seguintes dados pessoais: 1. Identificação (Nome, CPF); 2. Contato (E-mail, telefone); 3. Financeiros (Dados do cartão criptografados, histórico de pagamentos)."',
                                    'en' => '"We collect the following personal data: 1. Identification (Full name, Tax ID); 2. Contact details (Email, phone); 3. Financial details (Encrypted card data, payment history)."',
                                    'es' => '"Recopilamos los siguientes datos personales: 1. Identificación (Nombre, DNI/NIF); 2. Contacto (Correo, teléfono); 3. Financieros (Datos de tarjeta encriptados, historial de pagos)."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Coletamos as informações que você nos fornece ao usar a aplicação." (Vago, não detalha quais dados).
Exemplo 2 (Inexistente): Sem especificação dos dados tratados.',
                                    'en' => 'Example 1 (Insufficient): "We collect the information you give us when using our app." (Too vague, does not specify data items).
Example 2 (Nonexistent): No details or specifications of what data is collected.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Recopilamos la información que nos proporciona al usar la aplicación." (Vago, no detalla qué datos).
Ejemplo 2 (Inexistente): Sin especificación de los datos tratados.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Descrição de como os dados pessoais são compostos (detalhes que possam explicar melhor os dados pessoais).',
                                    'en' => 'Description of how personal data is composed (details that can better explain the personal data).',
                                    'es' => 'Descripción de cómo se componen los datos personales (detalles que puedan explicar mejor los datos personales).',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Definições ou seções detalhadas de dados.
Como julgar: Suficiente: Explica a composição granular de dados complexos (ex: "Localização é composta por latitude, longitude, data e hora"). Insuficiente: Apenas cita termos técnicos como "metadados do dispositivo" sem descrever o que há dentro. Inexistente: Sem detalhes de composição.',
                                    'en' => 'Where to find: Definitions or detailed data explanation sections.
How to evaluate: Sufficient: Explains the granular composition of complex data (e.g., "Location consists of latitude, longitude, date, and time"). Insufficient: Vaguely lists technical terms like "device metadata" without detailing what it contains. Nonexistent: No composition details.',
                                    'es' => 'Dónde buscar: Definiciones o secciones detalladas de datos.
Cómo evaluar: Suficiente: Explica la composición granular de datos complejos (ej: "Ubicación se compone de latitud, longitud, fecha y hora"). Insuficiente: Solo cita términos técnicos como "metadatos del dispositivo" sin describir qué contiene. Inexistente: Sin detalles de composición.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"O dado de Geolocalização é composto por: Latitude + Longitude + Precisão do sinal do GPS + Data/Hora do registro."',
                                    'en' => '"Geolocation data is composed of: Latitude + Longitude + GPS signal accuracy + Timestamp of the record."',
                                    'es' => '"El dato de Geolocalización se compone de: Latitud + Longitud + Precisión de la señal GPS + Fecha/Hora del registro."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Coletamos geolocalização e dados de rede." (Não explica o que compõe esses dados).
Exemplo 2 (Inexistente): Nenhuma explicação.',
                                    'en' => 'Example 1 (Insufficient): "We collect geolocation and network logs." (No definition of what values these data sets contain).
Example 2 (Nonexistent): No breakdown or definitions of the data categories.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Recopilamos geolocalización y datos de red." (No explica qué compone estos datos).
Ejemplo 2 (Inexistente): Ninguna explicación.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre a origem dos dados (dispositivos, compra de terceiros, compartilhamento etc).',
                                    'en' => 'Information about the origin of the data (devices, third-party purchase, sharing, etc.).',
                                    'es' => 'Información sobre el origen de los datos (dispositivos, compra a terceros, compartición, etc.).',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção "Como coletamos seus dados" ou "Origem dos dados".
Como julgar: Suficiente: Identifica se os dados vêm diretamente do titular, de parceiros, de fontes públicas ou de sensores/cookies. Insuficiente: Diz apenas que coleta "de forma direta e indireta" de forma genérica. Inexistente: Não menciona a origem.',
                                    'en' => 'Where to find: In "How we collect your data" or "Data sources" sections.
How to evaluate: Sufficient: Identifies if data is collected directly from the user, from partners, from public databases, or automatically via cookies/sensors. Insufficient: States generically "we collect data directly and indirectly". Nonexistent: No source information.',
                                    'es' => 'Dónde buscar: En la sección "Cómo recopilamos sus datos" u "Origen de los datos".
Cómo evaluar: Suficiente: Identifica si los datos provienen directamente del titular, de socios, de fuentes públicas o de sensores/cookies. Insuficiente: Solo dice que recopila "de forma directa e indirecta" de manera genérica. Inexistente: No menciona el origen.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Coletamos seus dados cadastrais diretamente quando você se registra. Dados de navegação são coletados automaticamente do seu dispositivo (via cookies) e dados de crédito são obtidos de birôs como o Serasa."',
                                    'en' => '"Your registration details are collected directly from you. Browsing data is gathered automatically from your device (via cookies), and credit scores are sourced from credit bureaus such as Experian."',
                                    'es' => '"Recopilamos sus datos de registro directamente cuando se inscribe. Los datos de navegación se recopilan automáticamente de su dispositivo (vía cookies) y los datos de crédito se obtienen de burós como Equifax."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Coletamos informações de várias fontes disponíveis no mercado." (Não especifica as origens).
Exemplo 2 (Inexistente): Sem informação sobre a origem dos dados.',
                                    'en' => 'Example 1 (Insufficient): "We collect data from various market sources." (Fails to specify the actual origins of the data).
Example 2 (Nonexistent): No source information provided.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Recopilamos información de varias fuentes disponibles en el mercado." (No especifica los orígenes).
Ejemplo 2 (Inexistente): Sin información sobre el origen de los datos.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Em caso de obrigatoriedade da disponibilização dos dados pelos indivíduos, informações sobre o que pode ocorrer no caso da não coleta dos dados.',
                                    'en' => 'In case of mandatory data provision by individuals, information about what may happen if the data is not collected.',
                                    'es' => 'En caso de que sea obligatorio que los individuos proporcionen los datos, información sobre qué puede ocurrir si no se recopilan.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Nas telas de cadastro ou em seções sobre "Consequências da não disponibilização".
Como julgar: Suficiente: Informa quais dados são obrigatórios e as consequências (ex: "Sem o CPF, não poderemos emitir a nota fiscal e concluir a compra"). Insuficiente: Apenas marca com asterisco (*) sem explicar os impactos reais no serviço. Inexistente: Sem avisos.',
                                    'en' => 'Where to find: On signup screens or in sections about "Consequences of not providing data".
How to evaluate: Sufficient: Details which data is mandatory and what happens if not provided (e.g., "Without your SSN, we cannot issue the invoice and complete the purchase"). Insufficient: Uses asterisks (*) next to fields without explaining the consequences. Nonexistent: No warnings.',
                                    'es' => 'Dónde buscar: En las pantallas de registro o en secciones sobre "Consecuencias de no proporcionar datos".
Cómo evaluar: Suficiente: Informa qué datos son obligatorios y sus consecuencias (ej: "Sin el NIF/CIF, no podremos emitir la factura y concluir la compra"). Insuficiente: Solo marca con asterisco (*) sin explicar los impactos reales en el servicio. Inexistente: Sin avisos.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"O fornecimento do seu endereço de entrega é obrigatório. Caso escolha não fornecê-lo, a entrega dos produtos físicos não poderá ser realizada, e sua compra será cancelada."',
                                    'en' => '"Providing your delivery address is mandatory. If you choose not to provide it, we cannot ship physical products, and your transaction will be cancelled."',
                                    'es' => '"Es obligatorio proporcionar su dirección de entrega. Si elige no proporcionarla, no podremos realizar la entrega de los productos físicos y su compra será cancelada."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Campos marcados com * são obrigatórios." (Não explica o impacto e o motivo técnico).
Exemplo 2 (Inexistente): O usuário simplesmente é bloqueado sem nenhuma mensagem explicativa.',
                                    'en' => 'Example 1 (Insufficient): "Fields marked with * are required." (Lacks explanation of the consequences and technical reasons).
Example 2 (Nonexistent): The application blocks the flow without any explanatory message.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Los campos marcados con * son obligatorios." (No explica el impacto ni el motivo técnico).
Ejemplo 2 (Inexistente): El usuario simplemente es bloqueado sin ningún mensaje explicativo.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre o objetivo do uso do dado pessoal e como (qual processo) é feito com o dado pessoal.',
                                    'en' => 'Information about the purpose of using personal data and how (what process) it is done with the personal data.',
                                    'es' => 'Información sobre el objetivo del uso del dato personal y cómo (qué proceso) se realiza con el dato personal.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção de processamento, tratamento ou segurança.
Como julgar: Suficiente: Detalha o processo (ex: "os dados de cartão são criptografados usando TLS e enviados ao gateway de pagamento X"). Insuficiente: Diz que "processa dados para pagamento" de forma vaga. Inexistente: Sem menção ao processo técnico.',
                                    'en' => 'Where to find: In the processing, handling, or security sections.
How to evaluate: Sufficient: Details the process (e.g., "card details are encrypted using TLS and securely sent to payment gateway X"). Insufficient: States vaguely "we process data to handle payment". Nonexistent: No technical process detail.',
                                    'es' => 'Dónde buscar: En la sección de procesamiento, tratamiento o seguridad.
Cómo evaluar: Suficiente: Detalla el proceso (ej: "los datos de la tarjeta se cifran usando TLS y se envían a la pasarela de pago X"). Insuficiente: Dice que "procesa datos para pago" de forma vaga. Inexistente: Sin mención al proceso técnico.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Sua senha passa por um processo de hash criptográfico antes do armazenamento no banco de dados, impossibilitando que funcionários ou terceiros leiam o texto original."',
                                    'en' => '"Your password goes through a cryptographic hashing process before database storage, ensuring that neither staff nor third parties can view the plain text password."',
                                    'es' => '"Su contraseña pasa por un proceso de hash criptográfico antes de su almacenamiento en la base de datos, imposibilitando que empleados o terceros lean el texto original."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Processamos sua senha de forma segura." (Muito superficial).
Exemplo 2 (Inexistente): Sem qualquer informação sobre como os dados são tecnicamente tratados.',
                                    'en' => 'Example 1 (Insufficient): "We process your password securely." (Too superficial).
Example 2 (Nonexistent): No details about how the data is handled technically.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Procesamos su contraseña de forma segura." (Muy superficial).
Ejemplo 2 (Inexistente): Sin ninguna información sobre cómo se tratan técnicamente los datos.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre a permissão concedida pelo indivíduo para o uso dos dados pessoais.',
                                    'en' => 'Information about the permission granted by the individual for the use of personal data.',
                                    'es' => 'Información sobre el permiso concedido por el individuo para el uso de los datos personales.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Central de Privacidade, histórico de consentimento ou painel de configurações.
Como julgar: Suficiente: Exibe quando e como o consentimento foi dado e oferece opção fácil de revogar. Insuficiente: Diz que o consentimento foi aceito mas não mostra data, histórico ou botão de revogação. Inexistente: Sem histórico ou controle.',
                                    'en' => 'Where to find: Privacy Center, consent history, or settings panel.
How to evaluate: Sufficient: Shows when and how consent was given, providing an easy way to revoke it. Insufficient: States consent was accepted but shows no date, history, or revoke buttons. Nonexistent: No history or control features.',
                                    'es' => 'Dónde buscar: Centro de Privacidad, historial de consentimiento o panel de configuración.
Cómo evaluar: Suficiente: Muestra cuándo y cómo se dio el consentimiento y ofrece una opción fácil para revocarlo. Insuficiente: Dice que se aceptó el consentimiento pero no muestra fecha, historial ni botón de revocación. Inexistente: Sin historial ni control.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Histórico de Permissões: Você autorizou a Geolocalização em 12/03/2026 às 14:35 via aplicativo móvel. [Botão: Revogar Permissão]"',
                                    'en' => '"Consent History: You authorized Geolocation tracking on March 12, 2026, at 14:35 via our mobile app. [Button: Revoke Consent]"',
                                    'es' => '"Historial de Permisos: Autorizó la Geolocalización el 12/03/2026 a las 14:35 a través de la aplicación móvil. [Botón: Revocar Permiso]"',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Você aceitou nossos termos ao se cadastrar." (Sem data ou controle de revogação individual).
Exemplo 2 (Inexistente): Não exibe histórico ou controle de permissão.',
                                    'en' => 'Example 1 (Insufficient): "You accepted our terms during signup." (No dates or individual settings to manage).
Example 2 (Nonexistent): No consent history or options to revoke permission are available.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Aceptó nuestros términos al registrarse." (Sin fecha ni control de revocación individual).
Ejemplo 2 (Inexistente): No muestra historial ni control de permiso.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'pt_BR' => 'Compartilhamento',
                            'en' => 'Sharing',
                            'es' => 'Compartición de datos',
                        ],
                        'questions' => [
                            [
                                'text' => [
                                    'pt_BR' => 'Informações de quais dados pessoais são transferidos ou compartilhados com terceiros.',
                                    'en' => 'Information about which personal data is transferred or shared with third parties.',
                                    'es' => 'Información sobre qué datos personales se transfieren o comparten con terceros.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção "Compartilhamento com Terceiros" da política.
Como julgar: Suficiente: Identifica exatamente quais dados (ex: e-mail, telefone) são compartilhados. Insuficiente: Diz apenas que compartilha "dados necessários para as transações". Inexistente: Sem menção a compartilhamento.',
                                    'en' => 'Where to find: In the "Third-Party Sharing" section of the policy.
How to evaluate: Sufficient: Identifies exactly which data categories (e.g., email, phone) are shared. Insufficient: States vaguely that it shares "data required to run transactions". Nonexistent: No mention of sharing.',
                                    'es' => 'Dónde buscar: En la sección "Compartición con Terceros" de la política.
Cómo evaluar: Suficiente: Identifica exactamente qué datos (ej: correo, teléfono) se comparten. Insuficiente: Solo dice que comparte "datos necesarios para las transacciones". Inexistente: Sin mención a la compartición de datos.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Compartilhamos seu nome, CPF e endereço com empresas de entrega (transportadoras). Não compartilhamos seu histórico de compras."',
                                    'en' => '"We share your full name, tax ID, and shipping address with courier partners. We do not share your purchase history."',
                                    'es' => '"Compartimos su nombre, NIF/CIF y dirección con empresas de entrega (transportistas). No compartimos su historial de compras."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Podemos compartilhar seus dados cadastrais com parceiros comerciais." (Não diz quais dados específicos).
Exemplo 2 (Inexistente): A política omite o fato de que compartilha dados.',
                                    'en' => 'Example 1 (Insufficient): "We may share registration logs with commercial partners." (Does not specify the data items).
Example 2 (Nonexistent): The policy hides or omits the fact that sharing takes place.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Podemos compartir sus datos de registro con socios comerciales." (No dice qué datos específicos).
Ejemplo 2 (Inexistente): La política omite el hecho de que comparte datos.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre o motivo da transferência e/ou compartilhamento dos dados pessoais.',
                                    'en' => 'Information about the reason for the transfer and/or sharing of personal data.',
                                    'es' => 'Información sobre el motivo de la transferencia y/o compartición de los datos personales.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção de compartilhamento.
Como julgar: Suficiente: Detalha o motivo/necessidade (ex: "para processar o pagamento com cartão"). Insuficiente: Usa termos genéricos como "para melhorar o serviço". Inexistente: Sem motivos.',
                                    'en' => 'Where to find: In the sharing details section.
How to evaluate: Sufficient: Details the exact business reason/need (e.g., "to process credit card payments"). Insufficient: Uses generic phrases like "to improve our integration". Nonexistent: No reasons listed.',
                                    'es' => 'Dónde buscar: En la sección de compartición.
Cómo evaluar: Suficiente: Detalla el motivo/necesidad (ej: "para procesar el pago con tarjeta"). Insuficiente: Usa términos genéricos como "para mejorar el servicio". Inexistente: Sin motivos.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Compartilhamos seu e-mail com a plataforma de e-mail marketing Sendgrid exclusivamente para gerenciar o disparo dos nossos informativos semanais."',
                                    'en' => '"We share your email address with Sendgrid strictly for managing the delivery of our weekly newsletters."',
                                    'es' => '"Compartimos su correo con la plataforma de marketing por correo Sendgrid exclusivamente para gestionar el envío de nuestros boletines semanales."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Compartilhamos dados com terceiros para fins operacionais diversos." (Falta especificidade).
Exemplo 2 (Inexistente): Sem justificativas de compartilhamento.',
                                    'en' => 'Example 1 (Insufficient): "We share data with third parties for miscellaneous operational purposes." (Lacks specificity).
Example 2 (Nonexistent): No justification for sharing is provided.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Compartimos datos con terceros para diversos fines operativos." (Falta especificidad).
Ejemplo 2 (Inexistente): Sin justificaciones de compartición.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre a base legal (lei/regulamentação) que garante a legalidade do compartilhamento dos dados.',
                                    'en' => 'Information about the legal basis (law/regulation) that ensures the legality of data sharing.',
                                    'es' => 'Información sobre la base legal (ley/reglamentación) que garantiza la legalidad de la compartición de los datos.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção de compartilhamento ou bases legais.
Como julgar: Suficiente: Cita a base legal específica (ex: cumprimento de obrigação legal) que autoriza o compartilhamento. Insuficiente: Cita apenas a conformidade da lei de forma genérica. Inexistente: Sem menção jurídica.',
                                    'en' => 'Where to find: In the sharing or legal bases section.
How to evaluate: Sufficient: Cites the specific lawful basis (e.g., legal obligation compliance) authorizing the transfer. Insufficient: States vaguely that sharing complies with applicable law. Nonexistent: No legal citations.',
                                    'es' => 'Dónde buscar: En la sección de compartición o bases legales.
Cómo evaluar: Suficiente: Cita la base legal específica (ej: cumplimiento de obligación legal) que autoriza la compartición. Insuficiente: Solo cita el cumplimiento de la ley de forma genérica. Inexistente: Sin mención jurídica.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"O compartilhamento de dados com a Receita Federal é obrigatório com base no Cumprimento de Obrigação Legal (Art. 7º, II da LGPD)."',
                                    'en' => '"Sharing data with tax authorities is required under Legal Obligation (Art. 6(1)(c) of GDPR) for tax compliance."',
                                    'es' => '"La compartición de datos con las autoridades fiscales es obligatoria con base en el Cumplimiento de una Obligación Legal."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "O compartilhamento respeita as normas federais vigentes." (Não aponta a base específica).
Exemplo 2 (Inexistente): Sem justificativa jurídica.',
                                    'en' => 'Example 1 (Insufficient): "Sharing is performed in accordance with federal acts." (Does not map specific legal bases).
Example 2 (Nonexistent): No legal basis cited for data sharing.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "La compartición respeta las normas vigentes." (No señala la base específica).
Ejemplo 2 (Inexistente): Sin justificación jurídica.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Dados completos do destinatário dos dados pessoais, de forma que permita a identificação e o contato com o destinatário.',
                                    'en' => 'Complete data of the personal data recipient, allowing identification and contact with the recipient.',
                                    'es' => 'Datos completos del destinatario de los datos personales, de forma que permita la identificación y el contacto con él.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Seção de parceiros, operadores ou destinatários de dados.
Como julgar: Suficiente: Identifica a empresa destinatária com Razão Social, CNPJ ou site/contato oficial. Insuficiente: Cita categorias de destinatários de forma vaga (ex: "empresas de nuvem e ferramentas de e-mail"). Inexistente: Oculta quem são os destinatários.',
                                    'en' => 'Where to find: Partners, processors, or recipients section.
How to evaluate: Sufficient: Identifies the recipient corporate entity with Official Name, registration, and website or contact. Insufficient: Lists only vague categories (e.g., "cloud hosts and analytical platforms"). Nonexistent: Hides the identity of recipients.',
                                    'es' => 'Dónde buscar: Sección de socios, encargados o destinatarios de datos.
Cómo evaluar: Suficiente: Identifica a la empresa destinataria con Razón Social, NIF/CIF o sitio web/contacto oficial. Insuficiente: Cita categorías de destinatarios de forma vaga (ej: "empresas de nube y herramientas de correo"). Inexistente: Oculta quiénes son los destinatarios.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Os dados de entrega são compartilhados com: Transportadora Rápida S.A., CNPJ 11.111.111/0001-11, contato@transportadorarapida.com.br."',
                                    'en' => '"Shipping data is shared with: FastDelivery Logistics Inc., registration ID 99-8888, contact@fastdelivery.com."',
                                    'es' => '"Los datos de entrega se comparten con: Transportista Rápida S.A., NIF A-11111111, contacto@transportistarapida.es."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Compartilhamos dados com prestadores de serviço logístico terceirizados." (Não cita quem são).
Exemplo 2 (Inexistente): Sem listar nenhum parceiro ou destinatário.',
                                    'en' => 'Example 1 (Insufficient): "We share data with third-party logistics service providers." (Does not name the providers).
Example 2 (Nonexistent): No names of partners or recipients are listed.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Compartimos datos con proveedores de servicios logísticos externos." (No nombra quiénes son).
Ejemplo 2 (Inexistente): Sin listar ningún socio o destinatario.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Dados da organização que monitora o uso dos dados pessoais no país ou região do destinatário, de forma que permita a identificação e o contato com o órgão.',
                                    'en' => 'Data of the organization that monitors the use of personal data in the recipient\'s country or region, allowing identification and contact with the agency.',
                                    'es' => 'Datos de la organización que supervisa el uso de los datos personales en el país o región del destinatario, de forma que permita la identificación y el contacto con ella.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Seção sobre transferências internacionais ou conformidade jurídica.
Como julgar: Suficiente: Informa a autoridade reguladora do destinatário internacional (ex: CNIL na França, GDPR europeu) com contato. Insuficiente: Diz que o destinatário segue regras internacionais de privacidade sem citar o órgão fiscalizador. Inexistente: Sem menção.',
                                    'en' => 'Where to find: International transfers or legal compliance sections.
How to evaluate: Sufficient: Identifies the supervisory authority of the foreign recipient (e.g., CNIL in France, ICO in UK) with contact details. Insufficient: Mentions that the recipient follows international privacy frameworks without naming the supervisor. Nonexistent: No mention.',
                                    'es' => 'Dónde buscar: Sección sobre transferencias internacionales o cumplimiento jurídico.
Cómo evaluar: Suficiente: Informa sobre la autoridad reguladora del destinatario internacional (ej: AEPD en España, RGPD europeo) con contacto. Insuficiente: Dice que el destinatario sigue reglas internacionales de privacidad sin citar al organismo supervisor. Inexistente: Sin mención.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Nossos servidores secundários ficam na Irlanda, cuja proteção é fiscalizada pela Data Protection Commission (DPC), acessível em dataprotection.ie."',
                                    'en' => '"Our cloud servers are hosted in Germany, monitored by the Federal Commissioner for Data Protection and Freedom of Information (BfDI), accessible at bfdi.bund.de."',
                                    'es' => '"Nuestros servidores secundarios están en Irlanda, cuya protección está supervisada por la Comisión de Protección de Datos (DPC), accesible en dataprotection.ie."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Transferimos dados para parceiros no exterior que cumprem os padrões internacionais." (Sem apontar o órgão local daquele país).
Exemplo 2 (Inexistente): Sem dados regulatórios internacionais.',
                                    'en' => 'Example 1 (Insufficient): "We transfer data to overseas partners complying with global standards." (Without identifying the regulatory agency).
Example 2 (Nonexistent): No international transfer regulatory information.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Transferimos datos a socios en el extranjero que cumplen con los estándares internacionales." (Sin señalar el organismo local de ese país).
Ejemplo 2 (Inexistente): Sin datos regulatorios internacionales.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Relação de quais dados foram transferidos ou compartilhados e como foram obtidos.',
                                    'en' => 'List of which data was transferred or shared and how it was obtained.',
                                    'es' => 'Relación de qué datos fueron transferidos o compartidos y cómo se obtuvieron.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção de compartilhamento.
Como julgar: Suficiente: Relaciona de forma expressa quais dados são compartilhados e a forma como foram adquiridos (ex: "compartilhamos o telefone coletado em nosso cadastro"). Insuficiente: Cita que compartilha sem dizer como obteve o dado. Inexistente: Sem relação.',
                                    'en' => 'Where to find: In the sharing sections.
How to evaluate: Sufficient: Details which data categories are shared and how they were originally collected (e.g., "we share the phone number collected during registration"). Insufficient: Mentions sharing but does not clarify the data collection source. Nonexistent: No correlation.',
                                    'es' => 'Dónde buscar: En la sección de compartición.
Cómo evaluar: Suficiente: Relaciona de forma expresa qué datos se comparten y la forma en que fueron adquiridos (ej: "compartimos el teléfono recopilado en nuestro registro"). Insuficiente: Cita que comparte sin decir cómo obtuvo el dato. Inexistente: Sin relación.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Compartilhamos com o parceiro de entrega o Nome e Endereço que você informou voluntariamente na finalização da compra."',
                                    'en' => '"We share with our delivery partner the Name and Address that you voluntarily filled out during checkout."',
                                    'es' => '"Compartimos con el socio de entrega el Nombre y la Dirección que usted proporcionó voluntariamente al finalizar la compra."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Compartilhamos dados cadastrais com parceiros." (Não deixa claro como obteve os dados compartilhados).
Exemplo 2 (Inexistente): Sem histórico ou menção sobre a procedência do dado compartilhado.',
                                    'en' => 'Example 1 (Insufficient): "We share registration records with partners." (Does not state how the shared data was obtained).
Example 2 (Nonexistent): No history or description of the origin of shared data.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Compartimos registros de datos con socios." (No aclara cómo se obtuvieron los datos compartidos).
Ejemplo 2 (Inexistente): Sin historial o descripción del origen del dato compartido.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações para relembrar como você permitiu e/ou autorizou o compartilhamento dos dados pessoais.',
                                    'en' => 'Information to remind how you allowed and/or authorized the sharing of personal data.',
                                    'es' => 'Información para recordar cómo permitió y/o autorizó la compartición de los datos personales.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Painel de privacidade ou página de consentimento do usuário.
Como julgar: Suficiente: Exibe uma visualização ou resumo de onde e quando o usuário deu a permissão (ex: "consentimento via checkbox na tela X"). Insuficiente: Informa que foi autorizado, mas sem detalhes contextuais ou visuais do consentimento original. Inexistente: Sem lembretes ou histórico.',
                                    'en' => 'Where to find: Privacy dashboard or user consent history pages.
How to evaluate: Sufficient: Displays a summary or mockup of where/when the user gave permission (e.g., "consent given via checkbox on screen X"). Insufficient: Tells you it was authorized but gives no contextual or visual details of the original consent event. Nonexistent: No consent history or reminders.',
                                    'es' => 'Dónde buscar: Panel de privacidad o página de historial de consentimiento del usuario.
Cómo evaluar: Suficiente: Muestra un resumen o visualización de dónde y cuándo el usuario dio permiso (ej: "consentimiento dado mediante casilla en la pantalla X"). Insuficiente: Informa que fue autorizado, pero no da detalles contextuales o visuales del evento de consentimiento original. Inexistente: Sin historial ni recordatorios.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Você autorizou o compartilhamento de cookies para anúncios na tela inicial da primeira navegação, clicando em \'Aceitar Todos\'. [Ver Histórico]"',
                                    'en' => '"You authorized cookie sharing for ads on the home page during your first visit by clicking \'Accept All\'. [View History]"',
                                    'es' => '"Usted autorizó la compartición de cookies para anuncios en la página de inicio durante su primera visita haciendo clic en \'Aceptar Todos\'. [Ver Historial]"',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Seu compartilhamento está ativo." (Não ajuda o usuário a lembrar de como ou quando autorizou).
Exemplo 2 (Inexistente): Sem qualquer painel ou lembrete de consentimento.',
                                    'en' => 'Example 1 (Insufficient): "Your data sharing is active." (Does not help the user remember how or when they gave consent).
Example 2 (Nonexistent): No dashboard or consent log available.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Su compartición de datos está activa." (No ayuda al usuario a recordar cómo o cuándo dio su consentimiento).
Ejemplo 2 (Inexistente): No hay un panel o registro de consentimientos disponible.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre os eventos que causam a transferência/compartilhamento dos dados pessoais.',
                                    'en' => 'Information about the events that cause the transfer/sharing of personal data.',
                                    'es' => 'Información sobre los eventos que causan la transferencia/compartición de los datos personales.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Seções de termos de compartilhamento da política.
Como julgar: Suficiente: Identifica o gatilho/evento gerador (ex: "os dados são enviados ao parceiro assim que a compra é aprovada"). Insuficiente: Diz que compartilha "periodicamente" ou de forma vaga. Inexistente: Não detalha os gatilhos.',
                                    'en' => 'Where to find: Sharing terms sections of the policy.
How to evaluate: Sufficient: Identifies the trigger/generating event (e.g., "data is sent to the shipping partner as soon as payment is approved"). Insufficient: Mentions sharing "periodically" or "when needed" without specific triggers. Nonexistent: No details.',
                                    'es' => 'Dónde buscar: Secciones de términos de compartición de la política.
Cómo evaluar: Suficiente: Identifica el desencadenante/evento generador (ej: "los datos se envían al socio de envío tan pronto como se aprueba el pago"). Insuficiente: Menciona que comparte "periódicamente" o "cuando sea necesario" sin desencadenantes específicos. Inexistente: Sin detalles.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"A transferência de dados cadastrais ocorre no momento em que você solicita a cotação de frete clicando no botão \'Calcular Frete\'."',
                                    'en' => '"Data transfer to our partner occurs when you request a shipping quote by clicking the \'Calculate Shipping\' button."',
                                    'es' => '"La transferencia de datos a nuestro socio ocurre cuando solicita una cotización de envío haciendo clic en el botón \'Calcular Envío\'."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Enviamos dados a parceiros operacionais no decorrer da execução do serviço." (Falta clareza do gatilho).
Exemplo 2 (Inexistente): Sem explicação de quando e por qual ação o dado é compartilhado.',
                                    'en' => 'Example 1 (Insufficient): "We send logs to operational partners as part of running the service." (Unclear trigger).
Example 2 (Nonexistent): No explanation of when or by what action data sharing is triggered.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Enviamos registros a socios operativos como parte de la ejecución del servicio." (Desencadenante poco claro).
Ejemplo 2 (Inexistente): Sin explicación de cuándo o por qué acción se desencadena la compartición de datos.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'pt_BR' => 'Agenciamento',
                            'en' => 'Agency',
                            'es' => 'Agenciamiento',
                        ],
                        'questions' => [
                            [
                                'text' => [
                                    'pt_BR' => 'Informações de como o indivíduo pode solicitar cópia de seus dados, alteração de permissão de uso dos dados, realizar uma reclamação ou exercer qualquer direito sobre os seus dados.',
                                    'en' => 'Information on how the individual can request a copy of their data, change data usage permission, file a complaint, or exercise any right over their data.',
                                    'es' => 'Información sobre cómo el individuo puede solicitar una copia de sus datos, cambiar el permiso de uso de los datos, presentar una queja o ejercer cualquier derecho sobre sus datos.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Seção "Seus Direitos" da política ou central de ajuda.
Como julgar: Suficiente: Fornece instruções claras e passo a passo (ex: e-mail direto do DPO ou menu do painel) para exercer os direitos (cópia, exclusão, correção). Insuficiente: Apenas lista os direitos legais sem explicar os meios práticos de requerimento. Inexistente: Sem instruções ou canais.',
                                    'en' => 'Where to find: In the "Your Rights" section of the policy or Help Center.
How to evaluate: Sufficient: Provides clear, step-by-step instructions (e.g., DPO email or dashboard menu) on how to exercise rights (access, deletion, correction). Insufficient: Lists the legal rights but does not explain how to actually request them. Nonexistent: No instructions or channels.',
                                    'es' => 'Dónde buscar: Sección "Sus Derechos" de la política o centro de ayuda.
Cómo evaluar: Suficiente: Proporciona instrucciones claras y paso a paso (ej: correo directo del DPO o menú del panel) para ejercer los derechos (copia, eliminación, corrección). Insuficiente: Solo enumera los derechos legales sin explicar los medios prácticos para solicitarlos. Inexistente: Sin instrucciones ni canales.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Para solicitar uma cópia dos seus dados ou a exclusão da sua conta, envie um e-mail para dpo@empresa.com com o assunto \'Direitos LGPD\' ou preencha o formulário em nosso Painel de Privacidade."',
                                    'en' => '"To request a copy of your data or account deletion, email dpo@company.com with the subject \'GDPR Rights\' or use the request form in our Privacy Center."',
                                    'es' => '"Para solicitar una copia de sus datos o la eliminación de su cuenta, envíe un correo a dpo@empresa.com con el asunto \'Derechos de Privacidad\' o complete el formulario en nuestro Panel de Privacidad."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Você tem o direito de solicitar a portabilidade e exclusão dos seus dados nos termos da lei." (Não explica como fazer).
Exemplo 2 (Inexistente): Sem canais ou orientações para exercício de direitos.',
                                    'en' => 'Example 1 (Insufficient): "You have the right to request portability and deletion of your data under the law." (Does not explain how).
Example 2 (Nonexistent): No channels or guidelines to exercise user rights.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Usted tiene derecho a solicitar la portabilidad y eliminación de sus datos según la ley." (No explica cómo hacerlo).
Ejemplo 2 (Inexistente): Sin canales ni orientaciones para el ejercicio de derechos.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações sobre meios de contato, telefones, e-mails sobre os atores envolvidos no uso dos dados pessoais.',
                                    'en' => 'Information about contact means, phone numbers, and emails of the actors involved in the use of personal data.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: No cabeçalho, rodapé ou na seção de contato da política.
Como julgar: Suficiente: Disponibiliza múltiplos canais de contato diretos e específicos para privacidade (e-mail, telefone e endereço do DPO). Insuficiente: Cita apenas um canal genérico de SAC sem tratamento especial de dados. Inexistente: Sem informações de contato.',
                                    'en' => 'Where to find: In the header, footer, or contact section of the policy.
How to evaluate: Sufficient: Provides multiple direct and specific channels for privacy matters (DPO email, phone, address). Insufficient: Lists only a generic customer service email. Nonexistent: No contact info.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Dúvidas sobre privacidade? Fale com nosso DPO João Silva: Telefone (11) 3333-3333, E-mail dpo@empresa.com, Endereço Rua Central 10, São Paulo."',
                                    'en' => '"Questions about privacy? Contact our DPO John Smith: Phone +1 (555) 0199, Email dpo@company.com, Address 10 Main St, New York."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Para qualquer dúvida, acesse nossa central geral de atendimento ao cliente." (Não direciona a privacidade).
Exemplo 2 (Inexistente): Sem contato disponibilizado.',
                                    'en' => 'Example 1 (Insufficient): "For any inquiries, contact our general support center." (No specific privacy contact).
Example 2 (Nonexistent): No contact information is provided at all.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Informações e/ou recursos para o indivíduo solicitar cópia de seus dados, alteração de permissão de uso dos dados, realizar uma reclamação ou exercer qualquer direito sobre os seus dados diretamente no software, sem a necessidade de entrar em contato.',
                                    'en' => 'Information and/or resources for the individual to request a copy of their data, change data usage permission, file a complaint, or exercise any right over their data directly in the software, without needing to make contact.',
                                    'es' => 'Información y/o recursos para que el individuo solicite una copia de sus datos, cambie el permiso de uso de los datos, presente una queja o ejerza cualquier derecho sobre sus datos directamente en el software, sin necesidad de contactar.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Nas configurações da conta, painel de privacidade ou menu de perfil.
Como julgar: Suficiente: Oferece ferramentas self-service integradas (ex: botão "Download dos meus dados" ou "Excluir Conta"). Insuficiente: Traz apenas instruções escritas de envio de e-mail sem funcionalidades diretas no sistema. Inexistente: Sem suporte no software.',
                                    'en' => 'Where to find: In account settings, privacy panel, or profile menu.
How to evaluate: Sufficient: Offers integrated self-service tools (e.g., "Download my data" or "Delete Account" buttons). Insufficient: Provides only written instructions to send an email, without direct system functionality. Nonexistent: No self-service support.',
                                    'es' => 'Dónde buscar: En la configuración de la cuenta, panel de privacidad o menú de perfil.
Cómo evaluar: Suficiente: Ofrece herramientas de autoservicio integradas (ej: botón "Descargar mis datos" o "Eliminar Cuenta"). Insuficiente: Solo proporciona instrucciones escritas para enviar un correo electrónico sin funcionalidades directas en el sistema. Inexistente: Sin soporte en el software.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Interface do usuário contendo botões como: "[Baixar Relatório Completo de Dados (JSON/PDF)]" e "[Excluir meus dados e minha conta definitivamente]".',
                                    'en' => 'User interface containing options like: "[Download Complete Data Report (JSON/PDF)]" and "[Permanently Delete My Account and Data]".',
                                    'es' => 'Interfaz de usuario que contiene botones como: "[Descargar Informe Completo de Datos (JSON/PDF)]" y "[Eliminar mis datos y mi cuenta definitivamente]".',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Para obter seus dados, envie uma carta registrada à nossa sede." (Processo offline burocrático).
Exemplo 2 (Inexistente): Nenhuma ferramenta ou funcionalidade no sistema para gerenciar dados.',
                                    'en' => 'Example 1 (Insufficient): "To access your data, mail a signed request to our office." (Bureaucratic offline process).
Example 2 (Nonexistent): No features or tools in the system to manage your personal data.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "Para obtener sus datos, envíe una carta certificada a nuestra sede." (Proceso offline burocrático).
Ejemplo 2 (Inexistente): Ninguna herramienta o funcionalidad en el sistema para gestionar datos.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name' => [
                    'pt_BR' => 'Formato de Apresentação',
                    'en' => 'Presentation Format',
                ],
                'response_profile' => 'presentation_format',
                'categories' => [
                    [
                        'name' => [
                            'pt_BR' => 'Pessoas/Atores',
                            'en' => 'People/Actors',
                            'es' => 'Personas/Actores',
                        ],
                        'questions' => [
                            [
                                'text' => [
                                    'pt_BR' => 'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações dos atores.',
                                    'en' => 'Design elements (texts, figures, photos, etc.) used to present information about the actors.',
                                    'es' => 'Elementos de diseño (textos, figuras, fotos, etc.) utilizados para presentar información sobre los actores.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na interface visual de identificação da empresa e DPO.
Como julgar: Suficiente: Usa elements visuais (ícones, logos, tabelas organizadas) que destacam o papel de cada ator de forma imediata. Insuficiente: Apresentado em texto corrido e denso, sem marcadores ou diagramação visual. Inexistente: Sem layout de apresentação.',
                                    'en' => 'Where to find: In the visual interface displaying company and DPO information.
How to evaluate: Sufficient: Uses visual elements (icons, badges, styled tables) to instantly highlight roles. Insufficient: Presented in dense, unstructured plain text without visual formatting. Nonexistent: No presentation layout.',
                                    'es' => 'Dónde buscar: In the visual interface displaying company and DPO information.
Cómo evaluar: Suficiente: Uses visual elements (icons, badges, styled tables) to instantly highlight roles. Insuficiente: Presented in dense, unstructured plain text without visual formatting. Inexistente: No presentation layout.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Utilização de cartões visuais (cards) distintos para "Controlador", "Encarregado (DPO)" e "Operador", contendo ícones ilustrativos e tipografia limpa.',
                                    'en' => 'Using distinct visual cards for "Controller", "DPO" and "Processor" with illustrative icons and clean typography.',
                                    'es' => 'Uso de tarjetas visuales distintas para "Responsable", "DPO" y "Encargado", con iconos ilustrativos y tipografía limpia.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): Um bloco monolítico de texto em fonte muito pequena enterrado na base da página de termos de uso.
Exemplo 2 (Inexistente): Sem formatação ou ausência de dados cadastrais.',
                                    'en' => 'Example 1 (Insufficient): A monolithic block of text in small print buried at the very bottom of a terms and conditions page.
Example 2 (Nonexistent): No formatting or complete lack of actor details.',
                                    'es' => 'Ejemplo 1 (Insuficiente): A monolithic block of text in small print buried at the very bottom of a terms and conditions page.
Ejemplo 2 (Inexistente): No formatting or complete lack of actor details.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise dos atores envolvidos no uso dos dados pessoais.',
                                    'en' => 'Simplicity, objectivity, and relevance of information to effectively assist in analyzing the actors involved in the use of personal data.',
                                    'es' => 'Simplicidad, objetividad y relevancia de la información para asistir eficazmente en el análisis de los actores involucrados en el uso de los datos personales.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Nos textos de descrição dos atores.
Como julgar: Suficiente: As descrições são curtas, diretas ao ponto e explicam o papel de cada um de forma compreensível a leigos. Insuficiente: Linguagem excessivamente jurídica ou termos complexos sem explicação. Inexistente: Texto irrelevante ou confuso.',
                                    'en' => 'Where to find: In the text descriptions of the actors.
How to evaluate: Sufficient: Explanations are short, direct, and explain the roles in a layperson-friendly manner. Insufficient: Overly legalistic jargon or complex terminology without clarification. Nonexistent: Text is irrelevant or highly confusing.',
                                    'es' => 'Dónde buscar: In the text descriptions of the actors.
Cómo evaluar: Suficiente: Explanations are short, direct, and explain the roles in a layperson-friendly manner. Insuficiente: Overly legalistic jargon or complex terminology without clarification. Inexistente: Text is irrelevant or highly confusing.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Controlador: A nossa empresa. Nós decidimos quais dados coletar para que você realize compras." (Curto, claro e objetivo).',
                                    'en' => '"Data Controller: Our company. We decide which data is collected so you can complete purchases." (Short, clear, and direct).',
                                    'es' => '"Responsable del Tratamiento: Nuestra empresa. Decidimos qué datos recopilar para que pueda realizar compras." (Corto, claro y directo).',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "O polo ativo detentor das atribuições de controladoria, conforme facultado pelas prerrogativas legais..." (Excesso de jargão legal).
Exemplo 2 (Inexistente): Descrição ausente ou incompreensível.',
                                    'en' => 'Example 1 (Insufficient): "The active subject executing controller competencies under administrative frameworks..." (Extreme legal jargon).
Example 2 (Nonexistent): Description missing or impossible to understand.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "The active subject executing controller competencies under administrative frameworks..." (Extreme legal jargon).
Ejemplo 2 (Inexistente): Description missing or impossible to understand.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia de grandes volumes de textos.',
                                    'en' => 'Ease of access to information so as not to require the individual to perform complex searches or analyze/read large volumes of text.',
                                    'es' => 'Facilidad de acceso a la información para no exigir que el individuo realice búsquedas complejas o analice/lea grandes volúmenes de texto.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na estrutura de navegação do site/aplicativo.
Como julgar: Suficiente: A informação está a no máximo 1 ou 2 cliques (ex: link "Privacidade" fixo no menu ou rodapé). Insuficiente: Exige navegar por várias páginas ou abrir documentos PDF externos. Inexistente: Dificultado ao extremo.',
                                    'en' => 'Where to find: In the navigation structure of the app/website.
How to evaluate: Sufficient: Info is accessible in 1 or 2 clicks max (e.g., sticky "Privacy" link in footer/header). Insufficient: Requires navigating through multiple menus or opening external PDF documents. Nonexistent: Blocked or hidden.',
                                    'es' => 'Dónde buscar: In the navigation structure of the app/website.
Cómo evaluar: Suficiente: Info is accessible in 1 or 2 clicks max (e.g., sticky "Privacy" link in footer/header). Insuficiente: Requires navigating through multiple menus or opening external PDF documents. Inexistente: Blocked or hidden.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Link direto no rodapé de todas as páginas intitulado "Dados do DPO e Contato de Privacidade", abrindo uma gaveta (drawer) ou janela pop-up rápida.',
                                    'en' => 'A direct link in the footer of all pages titled "DPO Details & Privacy Contact", opening a drawer or quick modal window.',
                                    'es' => 'Enlace directo en el pie de página de todas las páginas titulado "Datos del DPO y Contacto de Privacidad", abriendo un panel lateral o una ventana emergente rápida.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): O usuário precisa abrir a página de Ajuda, procurar "DPO" e clicar em um link para baixar um manual em PDF.
Exemplo 2 (Inexistente): Dificultado de forma a desencorajar a leitura.',
                                    'en' => 'Example 1 (Insufficient): The user must open Help, search for "DPO", and click a download link to get a PDF manual.
Example 2 (Nonexistent): Hidden deep within multi-layered settings menus.',
                                    'es' => 'Ejemplo 1 (Insuficiente): The user must open Help, search for "DPO", and click a download link to get a PDF manual.
Ejemplo 2 (Inexistente): Hidden deep within multi-layered settings menus.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'As informações existentes descartam a necessidade do indivíduo buscar informações em outras fontes.',
                                    'en' => 'The existing information eliminates the need for the individual to search for information from other sources.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Nas descrições de objetivos e links.
Como julgar: Suficiente: A página contém todas as informações necessárias sem obrigar a consultar termos extras ou links de terceiros. Insuficiente: Cita parceiros mas deixa as explicações incompletas, exigindo pesquisa externa. Inexistente: Sem informações completas localmente.',
                                    'en' => 'Where to find: In descriptions of purposes and hyperlinks.
How to evaluate: Sufficient: The page contains complete explanations about how data is used without depending on external links to "read more in our corporate portal". Insufficient: Mentions purposes but redirects users to external/unrelated sites for explanations. Nonexistent: No details provided.',
                                    'es' => 'Dónde buscar: In descriptions of purposes and hyperlinks.
Cómo evaluar: Suficiente: The page contains complete explanations about how data is used without depending on external links to "read more in our corporate portal". Insuficiente: Mentions purposes but redirects users to external/unrelated sites for explanations. Inexistente: No details provided.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Todos os detalhes sobre os atores, contatos de privacidade e canais de DPO estão unificados e explicados em uma única página legível.',
                                    'en' => 'All details about actors, privacy contacts, and DPO channels are unified and explained on a single readable page.',
                                    'es' => 'Todos los detalles sobre los actores, contactos de privacidad y canales del DPO están unificados y explicados en una única página legible.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Para saber as obrigações do nosso parceiro de nuvem, consulte os termos de serviço da AWS no site deles." (Exige consulta externa).
Exemplo 2 (Inexistente): Sem qualquer informação útil de atores locais.',
                                    'en' => 'Example 1 (Insufficient): "To view our cloud partner\'s privacy regulations, please read the AWS policy on their site." (Requires external search).
Example 2 (Nonexistent): Vague details requiring external research.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "To view our cloud partner\'s privacy regulations, please read the AWS policy on their site." (Requires external search).
Ejemplo 2 (Inexistente): Vague details requiring external research.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'pt_BR' => 'Propósito de uso',
                            'en' => 'Purpose of Use',
                            'es' => 'Propósito de uso',
                        ],
                        'questions' => [
                            [
                                'text' => [
                                    'pt_BR' => 'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre o(s) propósito(s) de uso dos dados.',
                                    'en' => 'Design elements (texts, figures, photos, etc.) used to present information about the purpose(s) of data use.',
                                    'es' => 'Elementos de diseño (textos, figuras, fotos, etc.) utilizados para presentar información sobre el(los) propósito(s) de uso de los datos.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção de finalidades do uso de dados.
Como julgar: Suficiente: Usa infográficos, ícones representativos ou animações para explicar por que os dados são usados. Insuficiente: Listado apenas em texto comum em blocos longos sem formatação visual. Inexistente: Sem formatação.',
                                    'en' => 'Where to find: In the data usage purposes section.
How to evaluate: Sufficient: Uses infographics, representative icons, or interactive animations to explain why data is processed. Insufficient: Listed as standard plain text in long, unformatted blocks. Nonexistent: No formatting.',
                                    'es' => 'Dónde buscar: In the data usage purposes section.
Cómo evaluar: Suficiente: Uses infographics, representative icons, or interactive animations to explain why data is processed. Insuficiente: Listed as standard plain text in long, unformatted blocks. Inexistente: No formatting.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Utilização de ícones para cada propósito (ex: um carrinho de compras para "Processar Pedidos", um escudo para "Segurança e Prevenção a Fraudes").',
                                    'en' => 'Using custom icons for each purpose (e.g., a shopping cart for "Order Processing", a shield for "Security and Fraud Prevention").',
                                    'es' => 'Uso de iconos personalizados para cada propósito (ej: un carrito de compras para "Procesamiento de Pedidos", un escudo para "Seguridad y Prevención de Fraudes").',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): Um parágrafo de 30 linhas explicando todos os objetivos sem nenhum marcador visual ou quebra de linha.
Exemplo 2 (Inexistente): Sem qualquer estrutura visual.',
                                    'en' => 'Example 1 (Insufficient): A 30-line paragraph explaining all purposes without a single bullet point, bold text, or line break.
Example 2 (Nonexistent): Vague text blocks without any visual design.',
                                    'es' => 'Ejemplo 1 (Insuficiente): A 30-line paragraph explaining all purposes without a single bullet point, bold text, or line break.
Ejemplo 2 (Inexistente): Vague text blocks without any visual design.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Simplicidade, objetividade e relevância das informações, de forma a auxiliar efetivamente na análise do(s) propósito(s) de uso dos dados pessoais.',
                                    'en' => 'Simplicity, objectivity, and relevance of information to effectively assist in analyzing the purpose(s) of personal data use.',
                                    'es' => 'Simplicidad, objetividad y relevancia de la información para asistir eficazmente en el análisis del(los) propósito(s) de uso de los datos personales.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na descrição dos objetivos do tratamento.
Como julgar: Suficiente: Sentenças curtas estruturadas seguindo a diretriz do TR-Model (ex: "O objetivo de uso para seu dado pessoal é {descrição}"). Insuficiente: Sentenças longas, ambíguas ou desnecessariamente técnicas. Inexistente: Sem clareza.',
                                    'en' => 'Where to find: In the description of processing objectives.
How to evaluate: Sufficient: Short structured sentences following TR-Model guidelines (e.g., "The purpose of using your personal data is to {description}"). Insufficient: Long, ambiguous, or excessively technical sentences. Nonexistent: Lacks clarity.',
                                    'es' => 'Dónde buscar: In the description of processing objectives.
Cómo evaluar: Suficiente: Short structured sentences following TR-Model guidelines (e.g., "The purpose of using your personal data is to {description}"). Insuficiente: Long, ambiguous, or excessively technical sentences. Inexistente: Lacks clarity.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"O propósito de uso para seu dado de e-mail é enviar novidades e avisos da sua conta." (Direto e focado na necessidade do titular).',
                                    'en' => '"The purpose of using your email address is to send you product updates and account notifications." (Direct and focused on user need).',
                                    'es' => '"El propósito de usar su correo electrónico es enviarle novedades de productos y notificaciones de cuenta." (Directo y centrado en la necesidad del usuario).',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Viabilizar a maximização do engajamento do cliente por meio de estratégias de outbound e comunicações eletrônicas." (Jargão corporativo complexo).
Exemplo 2 (Inexistente): Texto sem sentido ou ausente.',
                                    'en' => 'Example 1 (Insufficient): "To maximize customer engagement leverage via custom outbound workflows and messaging structures." (Heavy business jargon).
Example 2 (Nonexistent): Empty description or missing text.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "To maximize customer engagement leverage via custom outbound workflows and messaging structures." (Heavy business jargon).
Ejemplo 2 (Inexistente): Empty description or missing text.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.',
                                    'en' => 'Ease of access to information so as not to require the individual to perform complex searches or analyze/read large volumes of text.',
                                    'es' => 'Facilidad de acceso a la información para no exigir que el individuo realice búsquedas complejas o analice/lea grandes volúmenes de texto.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na navegação para visualização das finalidades.
Como julgar: Suficiente: O painel de finalidades é visível no primeiro nível da política ou ao clicar em um menu sanfona (accordion) interativo. Insuficiente: Exige scroll infinito na página ou clicar em múltiplos termos de consentimento. Inexistente: Acesso dificultado.',
                                    'en' => 'Where to find: In the navigation structure to view data processing purposes.
How to evaluate: Sufficient: Purposes panel is visible at the first level of the policy or via interactive accordions. Insufficient: Requires endless scrolling or clicking through multiple pages. Nonexistent: Hidden or hard to access.',
                                    'es' => 'Dónde buscar: In the navigation structure to view data processing purposes.
Cómo evaluar: Suficiente: Purposes panel is visible at the first level of the policy or via interactive accordions. Insuficiente: Requires endless scrolling or clicking through multiple pages. Inexistente: Hidden or hard to access.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Menu de abas (tabs) ou acordeão (accordion) intitulado "Por que usamos seus dados?", permitindo expandir cada finalidade para ver detalhes.',
                                    'en' => 'A tabbed interface or accordion titled "Why we use your data?", allowing users to expand each item to read specific details.',
                                    'es' => 'Una interfaz de pestañas o acordeón titulado "¿Por qué usamos sus datos?", que permite a los usuarios expandir cada elemento para leer detalles específicos.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): Exigir que o usuário leia todos os termos de uso (mais de 15 páginas) para encontrar os propósitos.
Exemplo 2 (Inexistente): Dificultado de propósito para evitar leitura.',
                                    'en' => 'Example 1 (Insufficient): Requiring the user to scroll through a 15-page Terms of Service document to find processing purposes.
Example 2 (Nonexistent): Hidden deep within multi-layered settings menus.',
                                    'es' => 'Ejemplo 1 (Insuficiente): Requiring the user to scroll through a 15-page Terms of Service document to find processing purposes.
Ejemplo 2 (Inexistente): Hidden deep within multi-layered settings menus.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.',
                                    'en' => 'The existing information eliminates the need for the user to search for information from other sources.',
                                    'es' => 'La información existente descarta la necesidad de que el usuario busque información en otras fuentes.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Nas descrições de objetivos e links.
Como julgar: Suficiente: A página contém explicações completas sobre o uso dos dados, sem depender de links externos para "ler mais em nossa central corporativa X". Insuficiente: Cita propósitos mas redireciona a outros links fora do contexto para explicação. Inexistente: Incompleto.',
                                    'en' => 'Where to find: In descriptions of purposes and hyperlinks.
How to evaluate: Sufficient: The page contains complete explanations about how data is used without depending on external links to "read more in our corporate portal". Insufficient: Mentions purposes but redirects users to external/unrelated sites for explanations. Nonexistent: No details provided.',
                                    'es' => 'Dónde buscar: In descriptions of purposes and hyperlinks.
Cómo evaluar: Suficiente: The page contains complete explanations about how data is used without depending on external links to "read more in our corporate portal". Insuficiente: Mentions purposes but redirects users to external/unrelated sites for explanations. Inexistente: No details provided.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Apresentação detalhada com glossário integrado que explica termos técnicos de forma contextual sem tirar o usuário da tela.',
                                    'en' => 'Detailed descriptions with an embedded glossary explaining technical terms contextually without redirecting the user.',
                                    'es' => 'Descripciones detalladas con un glosario integrado que explica términos técnicos contextualmente sin redirigir al usuario.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Tratamos dados para fins legítimos. Para saber quais são esses fins, clique no link de políticas da nossa holding global [Link]." (Redirecionamento complexo).
Exemplo 2 (Inexistente): Informações genéricas que obrigam pesquisa externa.',
                                    'en' => 'Example 1 (Insufficient): "We process data for legitimate purposes. To find out what these are, visit our holding company\'s global policies [Link]."
Example 2 (Nonexistent): Vague statements that require external research.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "We process data for legitimate purposes. To find out what these are, visit our holding company\'s global policies [Link]."
Ejemplo 2 (Inexistente): Vague statements that require external research.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'pt_BR' => 'Dados pessoais',
                            'en' => 'Personal Data',
                        ],
                        'questions' => [
                            [
                                'text' => [
                                    'pt_BR' => 'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre os dados pessoais manipulados.',
                                    'en' => 'Design elements (texts, figures, photos, etc.) used to present information about the personal data handled.',
                                    'es' => 'Elementos de diseño (textos, figuras, fotos, etc.) utilizados para presentar información sobre los datos personales tratados.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção de dados pessoais tratados.
Como julgar: Suficiente: Usa elementos gráficos ou tabelas estilizadas para categorizar os dados tratados (ex: ícone de cartão para dados financeiros). Insuficiente: Texto corrido sem destaque tipográfico ou estrutura visual. Inexistente: Sem formatação.',
                                    'en' => 'Where to find: In the personal data handled section.
How to evaluate: Sufficient: Uses graphical elements or styled tables to categorize personal data items (e.g., credit card icon for financial data). Insufficient: Unstructured plain text without typographical highlights. Nonexistent: No formatting.',
                                    'es' => 'Dónde buscar: In the personal data handled section.
Cómo evaluar: Suficiente: Uses graphical elements or styled tables to categorize personal data items (e.g., credit card icon for financial data). Insuficiente: Unstructured plain text without typographical highlights. Inexistente: No formatting.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Tabela com colunas coloridas separando "Tipo de Dado", "Exemplos" e "Sensibilidade", usando ícones ilustrativos para cada tipo.',
                                    'en' => 'A structured table with color-coded columns separating "Data Type", "Examples" and "Sensitivity", with icons for each category.',
                                    'es' => 'Tabla estructurada con columnas codificadas por colores que separan "Tipo de Dato", "Ejemplos" y "Sensibilidad", con iconos para cada categoría.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): Uma lista gigante de dados separados por vírgula em um único parágrafo denso.
Exemplo 2 (Inexistente): Sem qualquer cuidado de design visual.',
                                    'en' => 'Example 1 (Insufficient): A giant list of data items separated by commas in a single, dense text paragraph.
Example 2 (Nonexistent): Plain text block with no styling.',
                                    'es' => 'Ejemplo 1 (Insuficiente): A giant list of data items separated by commas in a single, dense text paragraph.
Ejemplo 2 (Inexistente): Plain text block with no styling.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise do propósito de uso os dados pessoais.',
                                    'en' => 'Simplicity, objectivity, and relevance of information to effectively assist in analyzing the purpose of personal data use.',
                                    'es' => 'Simplicidad, objetividad y relevancia de la información para asistir eficazmente en el análisis del propósito de uso de los datos personales.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Nas descrições e nomes dos dados coletados.
Como julgar: Suficiente: Os nomes dos dados são compreensíveis a leigos (ex: "Histórico de compras" em vez de "logs_de_transacoes_internas_v1"). Insuficiente: Usa nomenclaturas de banco de dados ou termos técnicos de T.I. sem explicação. Inexistente: Nomes confusos.',
                                    'en' => 'Where to find: In the descriptions and names of collected data.
How to evaluate: Sufficient: Names of data categories are easy for a layperson to understand (e.g., "Purchase history" instead of "internal_tx_logs_v1"). Insufficient: Uses database column names or T.I. technical jargon without explanation. Nonexistent: Highly confusing terms.',
                                    'es' => 'Dónde buscar: In the descriptions and names of collected data.
Cómo evaluar: Suficiente: Names of data categories are easy for a layperson to understand (e.g., "Purchase history" instead of "internal_tx_logs_v1"). Insuficiente: Uses database column names or T.I. technical jargon without explanation. Inexistente: Highly confusing terms.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Dados de Navegação: As páginas que você visitou em nossa loja e os produtos que clicou."',
                                    'en' => '"Browsing Data: The specific pages you visited in our shop and the products you clicked on."',
                                    'es' => '"Datos de Navegación: Las páginas específicas que visitó en nuestra tienda y los productos en los que hizo clic."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Coletamos UUID, UserAgent, Fingerprint de Canvas e cookies de sessão." (Muito técnico para leigos).
Exemplo 2 (Inexistente): Descrições ausentes ou sem lógica.',
                                    'en' => 'Example 1 (Insufficient): "We collect UUID, UserAgent, Canvas Fingerprint, and session cookies." (Too technical for average users).
Example 2 (Nonexistent): Descriptions missing or nonsensical.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "We collect UUID, UserAgent, Canvas Fingerprint, and session cookies." (Too technical for average users).
Ejemplo 2 (Inexistente): Descriptions missing or nonsensical.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.',
                                    'en' => 'Ease of access to information so as not to require the individual to perform complex searches or analyze/read large volumes of text.',
                                    'es' => 'Facilidad de acceso a la información para no exigir que el individuo realice búsquedas complejas o analice/lea grandes volúmenes de texto.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na facilidade de achar quais dados são tratados.
Como julgar: Suficiente: A lista de dados pessoais está destacada na página inicial da política ou em uma seção fixa fácil de achar. Insuficiente: É preciso abrir vários submenus ou links externos para ler a lista. Inexistente: Difícil de encontrar.',
                                    'en' => 'Where to find: In the ease of locating which data is processed.
How to evaluate: Sufficient: Personal data list is highlighted on the main policy page or in a prominent sticky section. Insufficient: Requires opening multiple submenus or external links to read the list. Nonexistent: Hidden.',
                                    'es' => 'Dónde buscar: In the ease of locating which data is processed.
Cómo evaluar: Suficiente: Personal data list is highlighted on the main policy page or in a prominent sticky section. Insuficiente: Requires opening multiple submenus or external links to read the list. Inexistente: Hidden.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Acesso por meio de um botão flutuante ou link direto no menu superior: "Quais dados coletamos?".',
                                    'en' => 'Access via a floating button or a direct link in the top menu: "What data do we collect?".',
                                    'es' => 'Acceso mediante un botón flotante o un enlace directo en el menú superior: "¿Qué datos recopilamos?".',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): A lista de dados coletados está misturada na seção "Disposições Gerais" no meio da política.
Exemplo 2 (Inexistente): Dificultado para evitar que o usuário saiba quais dados são armazenados.',
                                    'en' => 'Example 1 (Insufficient): The list of collected data is mixed into a "Miscellaneous" section in the middle of the policy.
Example 2 (Nonexistent): Hidden so deep that searching for it yields no results.',
                                    'es' => 'Ejemplo 1 (Insuficiente): The list of collected data is mixed into a "Miscellaneous" section in the middle of the policy.
Ejemplo 2 (Inexistente): Hidden so deep that searching for it yields no results.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.',
                                    'en' => 'The existing information eliminates the need for the user to search for information from other sources.',
                                    'es' => 'La información existente descarta la necesidad de que el usuario busque información en otras fuentes.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na completude das explicações sobre os dados.
Como julgar: Suficiente: Explica todas as categorias de dados diretamente na página sem mandar o usuário pesquisar o que significa em outros sites. Insuficiente: Cita dados vagamente (ex: "dados de sensores") e manda o usuário ler manuais externos para entender. Inexistente: Incompleto.',
                                    'en' => 'Where to find: In the completeness of the data explanations.
How to evaluate: Sufficient: Explains all data categories directly on the page without forcing users to search for definitions on other sites. Insufficient: Mentions data vaguely (e.g., "sensor data") and redirects users to external manuals to understand. Nonexistent: No details provided.',
                                    'es' => 'Dónde buscar: In the completeness of the data explanations.
Cómo evaluar: Suficiente: Explains all data categories directly on the page without forcing users to search for definitions on other sites. Insuficiente: Mentions data vaguely (e.g., "sensor data") and redirects users to external manuals to understand. Inexistente: No details provided.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Apresentação com tooltips internos explicativos (popovers) ao passar o mouse sobre cada categoria de dado.',
                                    'en' => 'Presenting explanatory popovers (tooltips) when hovering over each data category.',
                                    'es' => 'Presentación de tooltips explicativos (popovers) al pasar el cursor sobre cada categoría de datos.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Coletamos telemetria do dispositivo. Consulte o manual técnico do Android para saber quais dados a telemetria envia." (Pesquisa externa).
Exemplo 2 (Inexistente): Sem qualquer detalhamento local.',
                                    'en' => 'Example 1 (Insufficient): "We collect device telemetry. Please check the Android developer manual to see what data telemetry includes."
Example 2 (Nonexistent): Generic terms without explanation.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "We collect device telemetry. Please check the Android developer manual to see what data telemetry includes."
Ejemplo 2 (Inexistente): Generic terms without explanation.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'pt_BR' => 'Compartilhamento',
                            'en' => 'Sharing',
                            'es' => 'Compartición de datos',
                        ],
                        'questions' => [
                            [
                                'text' => [
                                    'pt_BR' => 'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre a transferência/compartilhamento dos dados.',
                                    'en' => 'Design elements (texts, figures, photos, etc.) used to present information about data transfer/sharing.',
                                    'es' => 'Elementos de diseño (textos, figuras, fotos, etc.) utilizados para presentar información sobre la transferencia/compartición de los datos.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção de compartilhamento.
Como julgar: Suficiente: Usa elementos gráficos como mapas, diagramas de fluxo de dados ou ícones de conexão para ilustrar o fluxo de dados. Insuficiente: Apenas texto denso sem diagramação. Inexistente: Sem formatação.',
                                    'en' => 'Where to find: In the sharing/transfer section.
How to evaluate: Sufficient: Uses graphical elements like maps, data flow diagrams, or connection icons to illustrate the data flow. Insufficient: Only dense text without layout. Nonexistent: No formatting.',
                                    'es' => 'Dónde buscar: In the sharing/transfer section.
Cómo evaluar: Suficiente: Uses graphical elements like maps, data flow diagrams, or connection icons to illustrate the data flow. Insuficiente: Only dense text without layout. Inexistente: No formatting.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Diagrama de fluxo visual que mostra o dado saindo da aplicação, passando pelo servidor e chegando a parceiros específicos (ex: gateways de pagamento).',
                                    'en' => 'A visual data flow diagram displaying data moving from the app, through servers, to specific partners (e.g., payment gateways).',
                                    'es' => 'Un diagrama de flujo de datos visual que muestra los datos moviéndose desde la aplicación, a través de servidores, hasta socios específicos (ej: pasarelas de pago).',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): Uma lista monolítica e confusa de parceiros sem nenhuma separação visual ou ícones.
Exemplo 2 (Inexistente): Sem layout ou ausente.',
                                    'en' => 'Example 1 (Insufficient): A monolithic and confusing list of partners with no visual separations or icons.
Example 2 (Nonexistent): Plain text block with no formatting.',
                                    'es' => 'Ejemplo 1 (Insuficiente): A monolithic and confusing list of partners with no visual separations or icons.
Ejemplo 2 (Inexistente): Plain text block with no formatting.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise da transferência/compartilhamento dos dados.',
                                    'en' => 'Simplicity, objectivity, and relevance of information to effectively assist in analyzing data transfer/sharing.',
                                    'es' => 'Simplicidad, objetividad y relevancia de la información para asistir eficazmente en el análisis de la transferencia/compartición de los datos.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na descrição das transferências.
Como julgar: Suficiente: Sentenças diretas e justificativas claras de por que e com quem compartilha, focadas no usuário. Insuficiente: Sentenças prolixas ou jargões comerciais/legais confusos. Inexistente: Sem clareza.',
                                    'en' => 'Where to find: In the description of data transfers.
How to evaluate: Sufficient: Direct sentences and clear justifications of why and with whom data is shared. Insufficient: Verbose sentences or confusing commercial/legal jargon. Nonexistent: Lacks clarity.',
                                    'es' => 'Dónde buscar: In the description of data transfers.
Cómo evaluar: Suficiente: Direct sentences and clear justifications of why and with whom data is shared. Insuficiente: Verbose sentences or confusing commercial/legal jargon. Inexistente: Lacks clarity.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Compartilhamos seu e-mail com a Sendgrid para que possamos enviar alertas rápidos da sua conta." (Curto e transparente).',
                                    'en' => '"We share your email address with Sendgrid so we can deliver urgent security alerts to you." (Short and transparent).',
                                    'es' => '"Compartimos su correo con Sendgrid para poder enviarle alertas de seguridad urgentes." (Corto y transparente).',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Visando otimizar a sinergia operacional com ecossistemas de provedores integrados terceirizados..." (Complexidade excessiva).
Exemplo 2 (Inexistente): Descrições vagas ou sem lógica.',
                                    'en' => 'Example 1 (Insufficient): "To optimize operational synergy with outsourced integrated provider ecosystems..." (Overly complex).
Example 2 (Nonexistent): Empty description or missing text.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "To optimize operational synergy with outsourced integrated provider ecosystems..." (Overly complex).
Ejemplo 2 (Inexistente): Empty description or missing text.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.',
                                    'en' => 'Ease of access to information so as not to require the individual to perform complex searches or analyze/read large volumes of text.',
                                    'es' => 'Facilidad de acceso a la información para no exigir que el individuo realice búsquedas complejas o analice/lea grandes volúmenes de texto.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na navegação da seção de compartilhamento.
Como julgar: Suficiente: Acesso rápido na central de privacidade ou aba dedicada a "Compartilhamento". Insuficiente: Oculto atrás de múltiplos links ou misturado no meio do contrato geral. Inexistente: Difícil de achar.',
                                    'en' => 'Where to find: In the navigation structure of the sharing section.
How to evaluate: Sufficient: Quick access in the privacy center or a dedicated tab labeled "Sharing". Insufficient: Hidden behind multiple links or mixed within general terms. Nonexistent: Hard to find.',
                                    'es' => 'Dónde buscar: In the navigation structure of the sharing section.
Cómo evaluar: Suficiente: Quick access in the privacy center or a dedicated tab labeled "Sharing". Insuficiente: Hidden behind multiple links or mixed within general terms. Inexistente: Hard to find.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Aba específica "Com quem compartilhamos" no menu principal de configurações de privacidade.',
                                    'en' => 'A specific tab titled "Who we share data with" in the main privacy settings menu.',
                                    'es' => 'Una pestaña específica titulada "Con quién compartimos datos" en el menú principal de configuración de privacidad.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): É preciso rolar até a página 12 do contrato geral de termos e condições para achar os dados de compartilhamento.
Exemplo 2 (Inexistente): Ocultado de forma deliberada.',
                                    'en' => 'Example 1 (Insufficient): The user must scroll to page 12 of the Terms of Service to find sharing details.
Example 2 (Nonexistent): No search results or section available.',
                                    'es' => 'Ejemplo 1 (Insuficiente): The user must scroll to page 12 of the Terms of Service to find sharing details.
Ejemplo 2 (Inexistente): No search results or section available.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.',
                                    'en' => 'The existing information eliminates the need for the user to search for information from other sources.',
                                    'es' => 'La información existente descarta la necesidad de que el usuario busque información en otras fuentes.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na completude das descrições dos parceiros.
Como julgar: Suficiente: Traz todas as informações de identificação do destinatário na própria página, sem mandar o usuário pesquisar em sites externos sobre o parceiro. Insuficiente: Cita apenas a marca fantasia e exige que o usuário busque no Google quem é a empresa real. Inexistente: Incompleto.',
                                    'en' => 'Where to find: In the completeness of partner descriptions.
How to evaluate: Sufficient: Provides all identification details of the recipient on the page, without sending users to external sites to research. Insufficient: Cites only brand names, forcing users to search online to find the real company. Nonexistent: No details provided.',
                                    'es' => 'Dónde buscar: In the completeness of partner descriptions.
Cómo evaluar: Suficiente: Provides all identification details of the recipient on the page, without sending users to external sites to research. Insuficiente: Cites only brand names, forcing users to search online to find the real company. Inexistente: No details provided.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Fornecer a Razão Social, CNPJ, site e país sede de todos os destinatários em um único painel local.',
                                    'en' => 'Providing the Legal Name, tax ID, website, and headquarters country of all recipients in a single local panel.',
                                    'es' => 'Proporcionar la Razón Social, NIF/CIF, sitio web y país sede de todos los destinatarios en un único panel local.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Compartilhamos dados com a empresa X. Mais informações sobre ela podem ser obtidas em seu site institucional." (Sem detalhes locais).
Exemplo 2 (Inexistente): Sem qualquer informação complementar sobre os terceiros.',
                                    'en' => 'Example 1 (Insufficient): "We share data with Company X. More information about them can be found on their corporate website."
Example 2 (Nonexistent): Only names are listed without context.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "We share data with Company X. More information about them can be found on their corporate website."
Ejemplo 2 (Inexistente): Only names are listed without context.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => [
                            'pt_BR' => 'Agenciamento',
                            'en' => 'Agency',
                            'es' => 'Agenciamiento',
                        ],
                        'questions' => [
                            [
                                'text' => [
                                    'pt_BR' => 'Elementos de design (textos, figuras, fotos etc) utilizados para apresentar as informações sobre agências de controle e ações para questionar ou verificar o uso dos dados.',
                                    'en' => 'Design elements (texts, figures, photos, etc.) used to present information about control agencies and actions to question or verify data use.',
                                    'es' => 'Elementos de diseño (textos, figuras, fotos, etc.) utilizados para presentar información sobre agencias de control y acciones para cuestionar o verificar el uso de los datos.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na seção de direitos ou reclamações.
Como julgar: Suficiente: Usa botões de ação bem visíveis, ícones intuitivos ou tutoriais visuais (passo a passo) para guiar o exercício de direitos. Insuficiente: Apenas links de texto azul sublinhados sem formatação visual clara. Inexistente: Sem elementos visuais.',
                                    'en' => 'Where to find: In the rights or complaints section.
How to evaluate: Sufficient: Uses prominent action buttons, intuitive icons, or step-by-step visual tutorials to guide rights execution. Insufficient: Simple underlined blue text links without visual structure. Nonexistent: No visual elements.',
                                    'es' => 'Dónde buscar: In the rights or complaints section.
Cómo evaluar: Suficiente: Uses prominent action buttons, intuitive icons, or step-by-step visual tutorials to guide rights execution. Insuficiente: Simple underlined blue text links without visual structure. Inexistente: No visual elements.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Utilização de botões coloridos de grande destaque (ex: "[Solicitar Cópia dos Dados]" ou "[Excluir minha conta]") com ícones de ajuda.',
                                    'en' => 'Using prominent colored action buttons (e.g., "[Request Data Copy]" or "[Delete Account]") accompanied by help icons.',
                                    'es' => 'Uso de botones de acción de colores destacados (ej: "[Solicitar Copia de Datos]" o "[Eliminar Cuenta]") acompañados de iconos de ayuda.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): Links de e-mail inseridos de forma indistinta em parágrafos normais.
Exemplo 2 (Inexistente): Sem elementos ou botões para ação.',
                                    'en' => 'Example 1 (Insufficient): Email links blended indistinguishably inside normal text paragraphs.
Example 2 (Nonexistent): No layout or button interface.',
                                    'es' => 'Ejemplo 1 (Insuficiente): Email links blended indistinguishably inside normal text paragraphs.
Ejemplo 2 (Inexistente): No layout or button interface.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Simplicidade, objetividade e relevância das informações de forma a auxiliar efetivamente na análise das agências de controle e ações para questionar ou verificar o uso dos dados.',
                                    'en' => 'Simplicity, objectivity, and relevance of information to effectively assist in analyzing control agencies and actions to question or verify data use.',
                                    'es' => 'Simplicidad, objetividad y relevancia de la información para asistir eficazmente en el análisis de las agencias de control y acciones para cuestionar o verificar el uso de los datos.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Nas instruções de solicitação de direitos.
Como julgar: Suficiente: Sentenças curtas e passo a passo claro sobre como proceder. Insuficiente: Explicação excessivamente complexa ou cheia de pré-requisitos burocráticos. Inexistente: Sem instruções claras.',
                                    'en' => 'Where to find: In rights request instructions.
How to evaluate: Sufficient: Short sentences and clear step-by-step on how to proceed. Insufficient: Overly complex explanation full of bureaucratic prerequisites. Nonexistent: No clear instructions.',
                                    'es' => 'Dónde buscar: In rights request instructions.
Cómo evaluar: Suficiente: Short sentences and clear step-by-step on how to proceed. Insuficiente: Overly complex explanation full of bureaucratic prerequisites. Inexistente: No clear instructions.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => '"Passo 1: Clique em \'Solicitar dados\'; Passo 2: Confirme sua identidade via token de e-mail; Passo 3: Seu relatório estará disponível em 24h."',
                                    'en' => '"Step 1: Click \'Request data\'; Step 2: Verify your identity via email token; Step 3: Your data report will be available in 24 hours."',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "A petição de retificação deverá ser instruída com documentos notarizados e encaminhada em horário comercial ao conselho fiscal..." (Burocracia excessiva).
Exemplo 2 (Inexistente): Sem passo a passo ou instruções.',
                                    'en' => 'Example 1 (Insufficient): "The rectification petition must be filed with notarized documents and sent during business hours to our audit council..." (Excessive bureaucracy).
Example 2 (Nonexistent): No guidelines whatsoever.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "The rectification petition must be filed with notarized documents and sent during business hours to our audit council..." (Excessive bureaucracy).
Ejemplo 2 (Inexistente): No guidelines whatsoever.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'Facilidade de acesso das informações de forma a não exigir que o indivíduo realize buscas complexas ou análise/leia grandes volumes de textos.',
                                    'en' => 'Ease of access to information so as not to require the individual to perform complex searches or analyze/read large volumes of text.',
                                    'es' => 'Facilidad de acceso a la información para no exigir que el individuo realice búsquedas complejas o analice/lea grandes volúmenes de texto.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na facilidade de encontrar o canal de exercício de direitos.
Como julgar: Suficiente: Acesso direto pelas configurações de perfil de usuário ou link em destaque na home. Insuficiente: Requer navegar por várias páginas de FAQ para encontrar o canal de solicitação. Inexistente: Canal ocultado.',
                                    'en' => 'Where to find: In the accessibility of the rights execution channel.
How to evaluate: Sufficient: Direct access through user profile settings or highlighted homepage link. Insufficient: Requires navigating through multiple FAQ pages to find the link. Nonexistent: Hidden channel.',
                                    'es' => 'Dónde buscar: In the accessibility of the rights execution channel.
Cómo evaluar: Suficiente: Direct access through user profile settings or highlighted homepage link. Insuficiente: Requires navigating through multiple FAQ pages to find the link. Inexistente: Hidden channel.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Um menu fixo de "Privacidade e LGPD" diretamente nas configurações da conta do usuário.',
                                    'en' => 'A dedicated "Privacy Settings" menu located directly within the user account settings panel.',
                                    'es' => 'Un menú dedicado de "Configuración de Privacidad" ubicado directamente dentro del panel de configuración de la cuenta del usuario.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): O link de exclusão de dados fica oculto na última página de políticas legais secundárias.
Exemplo 2 (Inexistente): Ausência de canais integrados.',
                                    'en' => 'Example 1 (Insufficient): The link to delete data is buried on the last page of secondary legal policy documents.
Example 2 (Nonexistent): No direct links or channels available.',
                                    'es' => 'Ejemplo 1 (Insuficiente): The link to delete data is buried on the last page of secondary legal policy documents.
Ejemplo 2 (Inexistente): No direct links or channels available.',
                                ],
                            ],
                            [
                                'text' => [
                                    'pt_BR' => 'As informações existentes descartam a necessidade do usuário buscar informações em outras fontes.',
                                    'en' => 'The existing information eliminates the need for the user to search for information from other sources.',
                                    'es' => 'La información existente descarta la necesidad de que el usuario busque información en otras fuentes.',
                                ],
                                'tooltip' => [
                                    'pt_BR' => 'Onde buscar: Na completude do suporte local da aplicação para direitos.
Como julgar: Suficiente: Todo o processo é resolvido localmente na aplicação (ex: formulário na tela). Insuficiente: A aplicação apenas aponta links para outros sites de suporte externos para iniciar o processo. Inexistente: Sem suporte local.',
                                    'en' => 'Where to find: In the completeness of local app support for user rights.
How to evaluate: Sufficient: The entire request process is handled locally within the app (e.g., embedded form). Insufficient: The app merely displays links to external support portals to start the process. Nonexistent: No local support.',
                                    'es' => 'Dónde buscar: In the completeness of local app support for user rights.
Cómo evaluar: Suficiente: The entire request process is handled locally within the app (e.g., embedded form). Insuficiente: The app merely displays links to external support portals to start the process. Inexistente: No local support.',
                                ],
                                'good_practice_example' => [
                                    'pt_BR' => 'Formulário nativo integrado na tela para envio de solicitação direta de direitos sem sair do aplicativo.',
                                    'en' => 'Native form embedded on screen to submit a direct rights request without leaving the app.',
                                    'es' => 'Formulario nativo integrado en la pantalla para enviar una solicitud directa de derechos sin salir de la aplicación.',
                                ],
                                'bad_practice_example' => [
                                    'pt_BR' => 'Exemplo 1 (Insuficiente): "Para exercer seus direitos, abra um chamado em nosso sistema externo de chamados JIRA [Link]." (Redirecionamento externo).
Exemplo 2 (Inexistente): Sem formulários ou suporte nativo.',
                                    'en' => 'Example 1 (Insufficient): "To exercise your rights, please open a ticket on our external JIRA service desk [Link]."
Example 2 (Nonexistent): No local request forms or options.',
                                    'es' => 'Ejemplo 1 (Insuficiente): "To exercise your rights, please open a ticket on our external JIRA service desk [Link]."
Ejemplo 2 (Inexistente): No local request forms or options.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
