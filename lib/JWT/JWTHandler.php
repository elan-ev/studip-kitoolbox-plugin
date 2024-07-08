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

    const TOKEN_DURATION_SECONDS = 15;
    const TOKEN_ISSUED_BY = 'KIToolbox';
    private $courseTool;
    private $algorithm;
    private $key;

    public function __construct(CourseTool $courseTool)
    {
        $this->courseTool = $courseTool;
        $this->key = InMemory::plainText($this->courseTool->tool->jwt_key);
        $this->algorithm = new Sha256();
    }

    public function generateRefreshTokenUrl()
    {
        global $user;
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $now = new DateTimeImmutable();
        $expiry = $now->modify('+' . self::TOKEN_DURATION_SECONDS . ' seconds');
        $token = $tokenBuilder
            ->issuedBy(self::TOKEN_ISSUED_BY)
            ->permittedFor(self::TOKEN_ISSUED_BY)
            ->issuedAt($now)
            ->expiresAt($expiry)
            ->relatedTo($user->username)
            ->withClaim('uid', $user->id)
            ->withClaim('ktcid', $this->courseTool->id)
            ->getToken($this->algorithm, $this->key);
        $tokenString = $token->toString();
        return $this->appendUrl($tokenString);
    }

    public function issueToolToken()
    {
        global $perm, $user;
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $now = new DateTimeImmutable();
        $expiry = $now->modify('+' . self::TOKEN_DURATION_SECONDS . ' seconds');
        $token = $tokenBuilder
            ->issuedBy(self::TOKEN_ISSUED_BY)
            ->permittedFor($this->courseTool->tool->url)
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

    private function appendUrl($token)
    {
        return \PluginEngine::getURL(
            'kitoolbox',
            [
                'cid' => $this->courseTool->course->id,
                'token' => $token
            ],
            'index/redirect'
        );
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

    public static function validateRefreshToken($tokenString, $courseId)
    {
        global $user;
        if (empty($tokenString)) {
            return false;
        }

        $claims = self::getClaims($tokenString);
        if (empty($claims)) {
            return false;
        }

        $isValidated = true;

        if (!isset($claims['iss']) || $claims['iss'] !== self::TOKEN_ISSUED_BY) {
            $isValidated = false;
        }

        if (empty($claims['aud']) || $claims['aud'][0] !== self::TOKEN_ISSUED_BY) {
            $isValidated = false;
        }

        $now = new DateTimeImmutable();
        if (!isset($claims['exp']) || $claims['exp']->getTimestamp() < $now->getTimestamp()) {
            $isValidated = false;
        }

        if (!isset($claims['sub']) || $claims['sub'] !== $user->username) {
            $isValidated = false;
        }

        if (!isset($claims['uid']) || $claims['uid'] !== $user->id) {
            $isValidated = false;
        }

        if (!isset($claims['ktcid'])) {
            $isValidated = false;
        }

        return $isValidated;
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
