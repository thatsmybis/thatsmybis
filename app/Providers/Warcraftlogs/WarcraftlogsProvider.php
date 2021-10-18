<?php

namespace App\Providers\Warcraftlogs;

use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\{AbstractProvider, User};
/**
 * Leverage the socialite framework to handle the oAuth2 registration process with warcraftlogs.com.
 *
 * More SEO keywords so that someone might find this if they need it:
 * Warcraftlogs warcraftlogs.com api php integration oauth2
 */
class WarcraftlogsProvider extends AbstractProvider {
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'WARCRAFTLOGS';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://www.warcraftlogs.com/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://www.warcraftlogs.com/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return Arr::add(
            parent::getTokenFields($code),
            'grant_type', 'authorization_code'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://www.warcraftlogs.com/api/v2/user',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        // As of 2021-10-09, Warcraft Logs doesn't send back any user data.
        return (new User)->setRaw($user)->map([
            'id'       => $user['id'] ?? null,
            'nickname' => $user['name'] ?? null,
            'name'     => $user['battleTag'] ?? null,
        ]);
    }
}
