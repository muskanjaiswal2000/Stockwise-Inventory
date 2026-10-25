<?php

namespace Modules\Inventory\Services;

use Illuminate\Support\Collection;

class NotificationRecipients
{
    /**
     * Return the single configured administrator email address.
     */
    public function all(): Collection
    {
        $configuredAdmin = config('inventory.notifications.admin_email');

        return collect([$configuredAdmin])
            ->filter(fn (?string $email): bool => filled($email))
            ->map(fn (string $email): string => strtolower(trim($email)))
            ->reject(function (string $email): bool {
                foreach (config('inventory.notifications.ignored_domains', []) as $domain) {
                    if (str_ends_with($email, strtolower($domain))) {
                        return true;
                    }
                }

                return false;
            })
            ->unique()
            ->values();
    }
}
