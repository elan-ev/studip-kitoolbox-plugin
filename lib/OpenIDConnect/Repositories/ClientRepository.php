<?php

namespace KIToolbox\OpenIDConnect\Repositories;

use KIToolbox\models\Tool;
use KIToolbox\OpenIDConnect\Entities\ClientEntity;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClientEntity($clientIdentifier)
    {
        $tool = Tool::findOneBySQL('oidc_client_id = ?', [$clientIdentifier]);

        if (!$tool) {
            return null;
        }

        if (empty($tool->oidc_redirect_url)) {
            throw OAuthServerException::serverError(
                'No OIDC redirect URLs are configured for this client.'
            );
        }

        $client = new ClientEntity();
        $client->setIdentifier($tool->oidc_client_id);
        $client->setName($tool->name);
        $client->setRedirectUri(
            explode("\n", $tool->oidc_redirect_url)
        );
        $client->setConfidential(true);

        return $client;
    }

    /**
     * {@inheritdoc}
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        $tool = Tool::findOneBySQL('oidc_client_id = ?', [$clientIdentifier]);

        // Check if client is registered
        if (!$tool) {
            return false;
        }

        // Check client secret
        if ($clientSecret !== $tool->oidc_client_secret) {
            return false;
        }

        return true;
    }
}
