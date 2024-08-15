<?php

namespace KIToolbox\JWT;

use KIToolbox\models\CourseTool;

use \DateTimeImmutable;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;


class JWTHandler
{

    const TOKEN_DURATION_SECONDS = 30;
    private $courseTool;
    private $algorithm;
    private $key;

    public function __construct(CourseTool $courseTool)
    {
        $this->courseTool = $courseTool;
        $this->key = InMemory::plainText($this->courseTool->tool->jwt_key);
        $this->algorithm = new Sha256();
    }

    public function issueToolToken()
    {
        global $perm, $user;
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $now = new DateTimeImmutable();
        $expiry = $now->modify('+' . self::TOKEN_DURATION_SECONDS . ' seconds');
        $token = $tokenBuilder
            ->issuedAt($now)
            ->expiresAt($expiry)
            ->relatedTo($user->username)
            ->withClaim('name', $user->getFullName())
            ->withClaim('context', $this->courseTool->course->id)
            ->withClaim('context-role', $perm->get_perm())
            ->getToken($this->algorithm, $this->key);

        $tokenString = $token->toString();
        return $tokenString;
    }

    public static function parse($tokenString)
    {
        $token = null;
        $parser = new Parser(new JoseEncoder());
        try {
            $token = $parser->parse($tokenString);
        } catch (CannotDecodeContent | InvalidTokenStructure | UnsupportedHeaderFound $e) {
            throw new \Error("Error...");
        }
        return $token;
    }

    public static function getClaims($tokenString, $claimId = null)
    {
        $token = self::parse($tokenString);
        if (empty($token)) {
            return null;
        }

        $claims = $token->claims()->all();

        if (empty($claimId)) {
            return $claims;
        }

        if (isset($claims[$claimId])) {
            return $claims[$claimId];
        }

        return null;
    }
}
