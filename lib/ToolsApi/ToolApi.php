<?php

namespace KIToolbox\ToolsApi;

use GuzzleHttp\Client;

class ToolApi extends Client
{
    private $baseToolUri;
    private $apiKey;
    private $accept = 'application/json';
    private $extra_headers = array();

    public const RESET_INTERVAL_DAILY = 'daily';
    public const RESET_INTERVAL_WEEKLY = 'weekly';
    public const RESET_INTERVAL_MONTHLY = 'monthly';
    public const RESET_INTERVAL_SEMESTER = 'semester';
    public const RESET_INTERVAL_VALUES = [
        self::RESET_INTERVAL_DAILY,
        self::RESET_INTERVAL_WEEKLY,
        self::RESET_INTERVAL_MONTHLY,
        self::RESET_INTERVAL_SEMESTER,
    ];

    public const SCOPE_USER = 'user';
    public const SCOPE_COURSE = 'course';
    public const SCOPE_COURSE_USER = 'course-user';
    public const SCOPE_TOTAL = 'total';

    public const QUOTA_SCOPES = [
        self::SCOPE_USER,
        self::SCOPE_COURSE,
        self::SCOPE_COURSE_USER,
        self::SCOPE_TOTAL,
    ];

    public const QUOTA_SCOPES_COURSE = [
        self::SCOPE_COURSE,
        self::SCOPE_COURSE_USER
    ];

    public function __construct(string $baseToolUri, string $apiKey)
    {
        if (empty($baseToolUri) || empty($apiKey)) {
            throw new \Error("Unable to create the client, due to lack of requirements.");
        }

        $this->baseToolUri = $baseToolUri;
        $this->apiKey = $apiKey;

        $config = [
            'base_uri' => $this->baseToolUri
        ];
        parent::__construct($config);
    }

    private function appendRequestOptions(array $options = [])
    {
        if (!empty($this->extra_headers)) {
            $options['headers'] = $this->extra_headers;
        }

        // Overwritting auth and accept!
        $options['headers']['Authorization'] = "Bearer {$this->apiKey}";
        $options['headers']['Accept'] = $this->accept;

        return $options;
    }

    private function performPost(string $uri, array $options = [])
    {
        try {
            $response = $this->post($uri, $this->appendRequestOptions($options));
            return $this->digestResponse($response);
        } catch (\Throwable $th) {
            return $this->resolveException($th);
        }
    }

    private function performGet(string $uri, array $options = [])
    {
        try {
            $response = $this->get($uri, $this->appendRequestOptions($options));
            return $this->digestResponse($response);
        } catch (\Throwable $th) {
            return $this->resolveException($th);
        }
    }

    private function performPut(string $uri, array $options = [])
    {
        try {
            $response = $this->put($uri, $this->appendRequestOptions($options));
            return $this->digestResponse($response);
        } catch (\Throwable $th) {
            return $this->resolveException($th);
        }
    }

    private function performDelete(string $uri, array $options = [])
    {
        try {
            $response = $this->delete($uri, $this->appendRequestOptions($options));
            return $this->digestResponse($response);
        } catch (\Throwable $th) {
            return $this->resolveException($th);
        }
    }

    private function resolveResponseBody(string $body)
    {
        $result = json_decode($body);
        if ($result !== null) {
            return $result;
        }
        if (!empty($body)) {
            return $body;
        }

        return null;
    }

    private function resolveException(\Throwable $th)
    {
        $error = [];
        $error['code'] = $th->getCode();
        $error['reason'] = $th->getMessage();
        $error['body'] = '';
        $error['location'] = '';
        if (!empty($error['reason'])) {
            return $error;
        }

        $reason = 'Unable to perform the request!';
        if ($th instanceof \GuzzleHttp\Exception\ConnectException) {
            $reason = 'Connection Error';
        } else if ($th instanceof \GuzzleHttp\Exception\ServerException) {
            $reason = 'Internal Server Error';
        } else if ($th instanceof \GuzzleHttp\Exception\ClientException) {
            $reason = 'Client Error';
        } else if ($th instanceof \GuzzleHttp\Exception\TooManyRedirectsException) {
            $reason = 'Too Many Redirect Error';
        }
        $error['reason'] = $reason;

        return $error;
    }

    private function digestResponse($response)
    {
        $result = [];
        $result['code'] = $response->getStatusCode();
        $result['reason'] = $response->getReasonPhrase();
        $body = '';
        if ($result['code'] < 400 && !empty((string) $response->getBody())) {
            $body = $this->resolveResponseBody((string) $response->getBody());
        }
        $result['body'] = $body;

        $location = '';
        if ($response->hasHeader('Location')) {
            $location = $response->getHeader('Location');
        }
        $result['location'] = $location;

        if ($response->hasHeader('Set-Cookie')) {
            $headerSetCookies = $response->getHeader('Set-Cookie');
            $result['raw_cookie'] = $headerSetCookies;
        }

        return $result;
    }

    private function getBodyParams($params)
    {
        $options['body'] = is_array($params) ? json_encode($params) : (string) $params;
        return $options;
    }

    private function getFormParams($params)
    {
        $options = [];
        $formParams = [];
        foreach ($params as $field_name => $field_value) {
            $formParams[$field_name] = (!is_string($field_value)) ? json_encode($field_value) : $field_value;
        }
        if (!empty($formParams)) {
            $options['form_params'] = $formParams;
        }
        return $options;
    }

    private function getMultiPartFormParams($params)
    {
        $options = [];
        $multiParams = [];
        foreach ($params as $field_name => $field_value) {
            $multiParams[] = [
                'name' => $field_name,
                'contents' => $field_value
            ];
        }
        if (!empty($multiParams)) {
            $options['multipart'] = $multiParams;
        }
        return $options;
    }

    private function getQueryParams($params)
    {
        $options = [];
        $queryParams = [];
        foreach ($params as $field_name => $field_value) {
            $value = is_bool($field_value) ? json_encode($field_value) : $field_value;
            $queryParams[$field_name] = $value;
        }
        if (!empty($queryParams)) {
            $options['query'] = $queryParams;
        }
        return $options;
    }

    public function AccessTool(string $token)
    {
        if (empty($token)) {
            throw new \Error("Token must not be empty");
        }
        $uri = '/';
        $formData['token'] = $token;
        $options = $this->getFormParams($formData);
        return $this->performPost($uri, $options);
    }

    public function getMetadata()
    {
        $uri = '/metadata';
        return $this->performGet($uri);
    }

    public function getQuota()
    {
        $uri = '/quota';
        return $this->performGet($uri);
    }

    public function putQuotas(int $limit, string $scope, string $feature = '')
    {
        $uri = '/quota';

        if (!in_array($scope, self::QUOTA_SCOPES)) {
            return [
                'code' => 0,
                'reason' => _('Invalid scope')
            ];
        }

        $quota['limit'] = $limit;
        $quota['scope'] = $scope;

        if (!empty($feature)) {
            $quota['feature'] = $feature;
        }

        $quotasBodyParam = [
            $quota
        ];

        $options = $this->getBodyParams($quotasBodyParam);
        return $this->performPut($uri, $options);
    }

    public function getCourseQuota(string $courseId)
    {
        $uri = "/quota/course/{$courseId}";
        return $this->performGet($uri);
    }

    public function putCourseQuota(string $courseId, int $limit, string $scope, string $feature = '')
    {
        $uri = "/quota/course/{$courseId}";

        if (!in_array($scope, self::QUOTA_SCOPES_COURSE)) {
            return [
                'code' => 0,
                'reason' => _('Invalid scope')
            ];
        }

        $quota['limit'] = $limit;
        $quota['scope'] = $scope;
        if (!empty($feature)) {
            $quota['feature'] = $feature;
        }

        $quotasBodyParam = [
            $quota
        ];

        $options = $this->getBodyParams($quotasBodyParam);
        return $this->performPut($uri, $options);
    }

    public function getCourseMemberQuota(string $courseId, string $userId)
    {
        $uri = "/quota/course/{$courseId}/user/{$userId}";
        return $this->performGet($uri);
    }
}
