<?php

final class AddOpenidConnect extends Migration
{
    public function up()
    {
        // Generate private and public RS256 key pair
        $private_key = openssl_pkey_new([
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($private_key, $private_key_pem);
        $public_key_pem = openssl_pkey_get_details($private_key)['key'];

        $cfg = Config::get();

        $cfg->create('KITOOLBOX_OIDC_PRIVATE_KEY',
            [
                'value' => $private_key_pem,
                'type' => 'string',
                'range' => 'global',
                'section' => 'KIToolbox',
                'description' => 'The private RS256 key is used to sign and verify JWTs transmitted.'
            ]
        );

        $cfg->create('KITOOLBOX_OIDC_PUBLIC_KEY',
            [
                'value' => $public_key_pem,
                'type' => 'string',
                'range' => 'global',
                'section' => 'KIToolbox',
                'description' => 'The public RS256 key is used to sign and verify JWTs transmitted.'
            ]
        );

        $cfg->create('KITOOLBOX_OIDC_ENCRYPTION_KEY',
            [
                'value' => base64_encode(random_bytes(32)),
                'type' => 'string',
                'range' => 'global',
                'section' => 'KIToolbox',
                'description' => 'Encryption keys are used to encrypt authorization and refresh codes.'
            ]
        );


        DBManager::get()->exec("ALTER TABLE `kit_tools` 
            ADD COLUMN `auth_method`           ENUM('oidc', 'jwt') NULL,
            ADD COLUMN `oidc_client_id`        MEDIUMTEXT NULL UNIQUE,
            ADD COLUMN `oidc_client_secret`    MEDIUMTEXT NULL,
            ADD COLUMN `oidc_redirect_url`     MEDIUMTEXT NULL");
    }
    public function down()
    {
        $cfg = Config::get();
        $cfg->delete('KITOOLBOX_OIDC_PRIVATE_KEY');
        $cfg->delete('KITOOLBOX_OIDC_PUBLIC_KEY');
        $cfg->delete('KITOOLBOX_OIDC_ENCRYPTION_KEY');

        DBManager::get()->exec("ALTER TABLE `kit_tools` 
            DROP COLUMN `auth_method`,
            DROP COLUMN `oidc_client_id`,
            DROP COLUMN `oidc_client_secret`,
            DROP COLUMN `oidc_redirect_url`");
    }
}
