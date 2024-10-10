<?php
namespace KIToolbox\OpenIDConnect\Repositories;

use KIToolbox\OpenIDConnect\Entities\UserEntity;
use OpenIDConnectServer\Repositories\IdentityProviderInterface;

class IdentityRepository implements IdentityProviderInterface
{
    public function getUserEntityByIdentifier($identifier)
    {
        return new UserEntity($identifier);
    }
}
