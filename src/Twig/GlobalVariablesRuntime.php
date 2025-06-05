<?php

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class GlobalVariablesRuntime implements RuntimeExtensionInterface
{
    private string $appName;
    private string $appDomain;
    private string $currentYear;
    private string $googleAnalyticsId;

    public function __construct(
        string $appName,
        string $appDomain,
        string $currentYear,
        string $googleAnalyticsId
    ) {
        $this->appName = $appName;
        $this->appDomain = $appDomain;
        $this->currentYear = $currentYear;
        $this->googleAnalyticsId = $googleAnalyticsId;
    }

    /**
     * Retourne un tableau de variables globales accessibles dans Twig.
     */
    public function getGlobals(): array
    {
        return [
            'app_name' => $this->appName,
            'app_domain' => $this->appDomain,
            'current_year' => $this->currentYear,
            'app_version' => '1.0.0',
            'google_analytics_id' => $this->googleAnalyticsId,
        ];
    }

    /**
     * Permet d'accéder à GA_ID individuellement.
     */
    public function getGoogleAnalyticsId(): string
    {
        return $this->googleAnalyticsId;
    }
}
