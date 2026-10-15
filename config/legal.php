<?php

/*
|--------------------------------------------------------------------------
| Legal documents (Terms of Use and Privacy Policy)
|--------------------------------------------------------------------------
|
| Identification of the data controller and contact channels shown in the
| Terms of Use and Privacy Policy pages. These values are deployment
| specific and must be filled in before the platform is opened to users
| (LGPD, art. 9º, III and art. 41, § 1º).
|
*/

return [
    'version' => '1.0',
    'updated_at' => '2026-10-13',

    'controller_name' => env('LEGAL_CONTROLLER_NAME'),
    'controller_document' => env('LEGAL_CONTROLLER_DOCUMENT'),
    'controller_address' => env('LEGAL_CONTROLLER_ADDRESS'),
    'contact_email' => env('LEGAL_CONTACT_EMAIL'),

    'dpo_name' => env('LEGAL_DPO_NAME'),
    'dpo_email' => env('LEGAL_DPO_EMAIL', env('LEGAL_CONTACT_EMAIL')),
];
