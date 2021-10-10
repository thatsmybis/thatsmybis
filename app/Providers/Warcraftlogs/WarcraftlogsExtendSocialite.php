<?php

namespace App\Providers\Warcraftlogs;

use App\Providers\Warcraftlogs\WarcraftlogsProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class WarcraftlogsExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled) {
        $socialiteWasCalled->extendSocialite('warcraftlogs', WarcraftlogsProvider::class);
    }
}
