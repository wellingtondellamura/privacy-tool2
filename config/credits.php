<?php

/*
|--------------------------------------------------------------------------
| Authorship and related scientific publications
|--------------------------------------------------------------------------
|
| Shown on the /creditos page and in the <meta name="author"> tag. Kept on
| the server so the data is embedded in the initial HTML response.
|
*/

return [
    'authors' => [
        [
            'name' => 'Wellington Aparecido Della Mura',
            'role_key' => 'credits.role_cnpq_phd',
            'lattes' => 'http://lattes.cnpq.br/0689979338493277',
            'orcid' => 'https://orcid.org/0000-0001-9249-8544',
        ],
        [
            'name' => 'Thiago Adriano Coleti',
            'role_key' => null,
            'lattes' => 'http://lattes.cnpq.br/8368597451080752',
            'orcid' => 'https://orcid.org/0000-0002-1078-4334',
        ],
        [
            'name' => 'Marcelo Morandini',
            'role_key' => null,
            'lattes' => 'http://lattes.cnpq.br/7235951485247158',
            'orcid' => 'https://orcid.org/0000-0001-5402-9544',
        ],
    ],

    // ABNT references, most recent first.
    'publications' => [
        [
            'year' => 2025,
            'authors' => 'COLETI, T. A.; SOUZA, M. L. M.; BALANCIERI, R.; MENOLLI, A. L. A.; YVANO, M. M.; DELLA MURA, W.; MORANDINI, M.',
            'title' => 'MITRA: Um método para inspeção e classificação da Transparência de Dados Pessoais',
            'source' => 'In: Simpósio Brasileiro de Fatores Humanos em Sistemas Computacionais, 2025, Belo Horizonte-MG. Anais do XXIV Simpósio Brasileiro sobre Fatores Humanos em Sistemas Computacionais. Porto Alegre: Sociedade Brasileira de Computação, 2025. v. 1. p. 762-778.',
            'url' => 'https://doi.org/10.5753/ihc.2025.10803',
        ],
        [
            'year' => 2025,
            'authors' => 'COLETI, T. A.; SOUZA, M. L. M.; BALANCIERI, R.; DELLA MURA, W.; YVANO, M. M.; MORANDINI, M.',
            'title' => 'Privacy Tool: Supporting the Evaluation of Transparency in Personal Data Handling',
            'source' => 'In: ARDITO, C.; BARBOSA, S. D. J.; CONTE, T.; FREIRE, A.; GASPARINI, I.; PALANQUE, P.; PRATES, R. (Org.). Human-Computer Interaction – INTERACT 2025. 1. ed. Springer, 2025. v. 16111, p. 280-285.',
            'url' => 'https://doi.org/10.1007/978-3-032-05008-3_53',
        ],
        [
            'year' => 2024,
            'authors' => 'YVANO, M. M.; COLETI, T. A.; DELLA MURA, W.; SOUZA, M. L. M.; FIORAVANTE, C. C.; TEIXEIRA, L. A.; MORANDINI, M.',
            'title' => 'Towards the access to information regarding the Personal Data Handling: A proposal for improvement and simplification of Privacy and Security Policies',
            'source' => "In: XXIII Simpósio Brasileiro de Fatores Humanos em Sistemas Computacionais, 2024, Brasília. IHC '24: Proceedings of the XXIII Brazilian Symposium on Human Factors in Computing Systems, 2024. p. 1-11.",
            'url' => 'https://doi.org/10.1145/3702038.3702078',
        ],
    ],
];
