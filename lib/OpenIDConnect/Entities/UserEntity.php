<?php

namespace KIToolbox\OpenIDConnect\Entities;

use League\OAuth2\Server\Entities\UserEntityInterface;
use OpenIDConnectServer\Entities\ClaimSetInterface;

class UserEntity implements ClaimSetInterface, UserEntityInterface
{
    private $identifier;

    public function __construct($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getClaims()
    {
        $user = \User::findByUsername($this->identifier);

        return [
            // profile scope
            'name' => $user->getFullName(),
            'given_name' => $user->vorname,
            'family_name' => $user->nachname,
            //'middle_name' => '',
            //'nickname' => '',
            //'preferred_username' => '',
            //'profile' => '',
            //'picture' => '',
            //'website' => '',
            //'gender' => '',
            //'birthdate' => '',
            //'zoneinfo' => 'Europe/Berlin',
            'locale' => 'DE',
            //'updated_at' => '',

            // email scope
            'email' => $user->email,
            'email_verified' => true,

            // phone scope
            //'phone_number' => '',
            //'phone_number_verified' => true,

            // address scope
            //'address' => '',
        ];
    }

    /**
     * Return the user's identifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}
