// Privacy Policy, version 1.0. Translation of the Portuguese text, which prevails.
export default {
    intro: [
        'This Privacy Policy describes which personal data is processed by Mitra Privacy Tool, available at {url}, for which purposes, on which legal bases, with whom it is shared, how long it is kept and how data subjects can exercise their rights, under Brazilian Law No. 13,709/2018 (General Data Protection Law, LGPD).',
    ],
    sections: [
        {
            title: 'Controller and data protection officer',
            blocks: [
                'The controller of the personal data processed on the platform is {controller}{controller_details}. The data protection officer is {dpo}.',
                'Within each project, the owner decides who is invited, which evaluations are visible to members, whether data is exported and whether the consolidated result is published. These decisions are the responsibility of the project owner, who must comply with the LGPD in the use they make of other members\' data.',
            ],
        },
        {
            title: 'Personal data processed',
            blocks: [
                'The following personal data is processed:',
                {
                    list: [
                        { label: 'Registration data', text: 'name, e-mail address, password (stored only as a cryptographic hash, from which it cannot be recovered), preferred language, e-mail confirmation date, and date and time of acceptance of the Terms of Use and of this policy.' },
                        { label: 'Optional profile data', text: 'profile (student, professional or researcher) and institutional or professional affiliation, provided only if the user so wishes.' },
                        { label: 'Platform usage data', text: 'projects created or joined by the user and the respective role, questionnaire answers, observations, review comments, consolidation decisions, publications made and date of last access.' },
                        { label: 'Data of invited people', text: 'e-mail address provided by a project owner to send an invitation, even if the person does not yet have an account on the platform.' },
                        { label: 'Technical data', text: 'IP address, browser identification (user agent), session identifier and time of last activity. When an error or failure occurs, the accessed address, the request parameters and the identification of the authenticated user are also recorded, excluding passwords, security tokens and cookies.' },
                    ],
                },
                'The platform does not request sensitive personal data (art. 5, II, LGPD) or data of children or adolescents. Free-text fields are meant to record information about the evaluated software and should not contain this kind of data.',
            ],
        },
        {
            title: 'Purposes and legal bases',
            blocks: [
                'Personal data is processed for the following purposes, each with its legal basis under art. 7 of the LGPD:',
                {
                    list: [
                        { label: 'Account creation, authentication and operation of the platform', text: 'management of projects, invitations, inspections, review, consolidation, results and export. Legal basis: performance of a contract (art. 7, V).' },
                        { label: 'Service e-mails', text: 'e-mail confirmation, password reset and project invitations. Legal basis: performance of a contract (art. 7, V) and, for invited people who do not yet have an account, legitimate interest (art. 7, IX) in enabling the collaboration requested by the project owner.' },
                        { label: 'Security and failure diagnosis', text: 'prevention of unauthorized access, session control and investigation of errors. Legal basis: legitimate interest (art. 7, IX).' },
                        { label: 'Record of acceptance of the terms', text: 'proof of the user\'s agreement with the conditions of use. Legal basis: regular exercise of rights (art. 7, VI).' },
                        { label: 'Characterization of the platform audience', text: 'use of the optional profile and institutional affiliation for aggregated statistics that do not identify people. Legal basis: consent (art. 7, I), given by voluntarily filling in these fields and revocable at any time by removing them on the profile page.' },
                    ],
                },
                'Data is not used for advertising, is not sold and is not used to build behavioral profiles. The scores calculated by the platform refer to the evaluated software and not to people, so there are no automated decisions about data subjects within the meaning of art. 20 of the LGPD.',
            ],
        },
        {
            title: 'Data sharing',
            blocks: [
                'Personal data is accessed or shared only in the following situations:',
                {
                    list: [
                        { label: 'Members of the same project', text: 'see the name and e-mail of the other members. Individual answers and observations are visible to the owner and, if the owner so configures the project, to the other members.' },
                        { label: 'Project owner', text: 'can export all project data, including each member\'s name, e-mail, answers and observations.' },
                        { label: 'Public directory and badge', text: 'display only consolidated results, as described in section 6 of the Terms of Use, without identifying the evaluators.' },
                        { label: 'Platform administrators', text: 'people authorized by the controller, who access data to the extent necessary for operation, support and security, under a duty of confidentiality.' },
                        { label: 'Processors', text: 'hosting providers for the application and database and the e-mail delivery provider, which process data only according to the controller\'s instructions.' },
                        { label: 'Google Fonts', text: 'the platform\'s pages load fonts from the servers of Google LLC, which receive the visitor\'s IP address and browser identification when serving these files.' },
                        { label: 'Authorities', text: 'upon legal request, court order or for the regular exercise of rights in proceedings.' },
                    ],
                },
            ],
        },
        {
            title: 'International transfer',
            blocks: [
                'Loading fonts from Google LLC and, depending on the contracted providers, hosting and e-mail delivery may involve processing data on servers outside Brazil. In such cases, the transfer takes place under the conditions of art. 33 of the LGPD, using providers that offer protection guarantees compatible with Brazilian law.',
            ],
        },
        {
            title: 'Retention periods',
            blocks: [
                'Personal data is kept only for as long as necessary for the purposes described, according to the following periods:',
                {
                    list: [
                        { label: 'Registration, profile and usage data', text: 'while the account exists. When the account is deleted, they are erased as described in section 10 of the Terms of Use.' },
                        { label: 'Invitations', text: 'until the invitation is removed by the owner or the project is deleted.' },
                        { label: 'Sessions', text: 'until the session ends or expires, after which they are removed automatically.' },
                        { label: 'Error and failure records', text: 'for up to 90 days.' },
                        { label: 'Backups', text: 'deleted data may remain in backups until overwritten in the regular backup cycle, without being used for any other purpose.' },
                    ],
                },
                'Data may be kept for longer when necessary to comply with a legal obligation or for the regular exercise of rights (art. 16, LGPD).',
            ],
        },
        {
            title: 'Data subject rights',
            blocks: [
                'Data subjects may exercise the rights provided for in art. 18 of the LGPD. Some of them are available directly on the platform:',
                {
                    list: [
                        { label: 'Access and correction', text: 'on the profile page, where name, e-mail, language, profile and institutional affiliation can be viewed and changed.' },
                        { label: 'Portability', text: 'through the option to export all data, which generates a file with the projects the user takes part in. For projects owned by others, the file contains only the user\'s own data.' },
                        { label: 'Erasure', text: 'by deleting the account on the profile page.' },
                        { label: 'Withdrawal of consent', text: 'by removing the profile and institutional affiliation on the profile page.' },
                    ],
                },
                'Other rights, such as confirmation that processing takes place, information about sharing, anonymization, blocking, objection to processing based on legitimate interest and review of decisions, can be exercised by contacting the data protection officer at {contact}. Requests will be answered within 15 days, and identity confirmation may be requested beforehand. Invited people who do not have an account may, through the same channel, request the removal of their e-mail address.',
                'Data subjects may also lodge a complaint with the Brazilian National Data Protection Authority (ANPD).',
            ],
        },
        {
            title: 'Security',
            blocks: [
                'Technical and administrative measures are adopted to protect personal data, including storing passwords as cryptographic hashes, encrypted communication (HTTPS), role-based access control in each project, e-mail confirmation at registration, protection against cross-site request forgery (CSRF) and omission of passwords and tokens from failure records. No system is immune to incidents. If a security incident occurs that may cause relevant risk or harm to data subjects, the controller will notify the ANPD and the affected data subjects, as required by art. 48 of the LGPD.',
            ],
        },
        {
            title: 'Cookies',
            blocks: [
                'The platform uses only cookies necessary for its operation, with no advertising or analytics cookies. Because they are essential, these cookies do not depend on consent. They are:',
                {
                    list: [
                        { label: 'Session cookie', text: 'keeps the user signed in and stores preferences such as the language. It expires after 2 hours of inactivity.' },
                        { label: 'XSRF-TOKEN', text: 'protects forms against forged requests. It lasts as long as the session.' },
                        { label: '"Remember me" cookie', text: 'created only when the user selects this option at login, to keep access between visits. It lasts about 400 days or until the user signs out.' },
                    ],
                },
                'When a third-party website embeds the platform\'s badge, the browser of that website\'s visitor makes a request to the platform, which receives the IP address and browser identification, as with any website access. The badge does not set cookies and does not identify the visitor. Blocking cookies in the browser prevents signing in to the platform.',
            ],
        },
        {
            title: 'Changes to this policy',
            blocks: [
                'This policy may be revised to reflect changes to the platform or to the law. The current version and the date of the last update are shown at the top of the document. Significant changes will be communicated by e-mail or by a notice on the platform, and new acceptance will be requested when a change expands the processing of personal data.',
            ],
        },
        {
            title: 'Contact',
            blocks: [
                'Questions about this policy and requests concerning personal data should be sent to the data protection officer at {contact}.',
            ],
        },
    ],
};
