<?php

use App\Models\User;

if (! function_exists('active_role')) {
    /**
     * Get the currently active role for the authenticated user.
     */
    function active_role(): ?string
    {
        return session('active_role');
    }
}

if (! function_exists('active_role_is')) {
    /**
     * Check if the given role is the currently active role.
     */
    function active_role_is(string $role): bool
    {
        return active_role() === $role;
    }
}

if (! function_exists('format_role_name')) {
    /**
     * Format role name for display (convert to title case).
     */
    function format_role_name(string $role): string
    {
        // Special replacements
        $replacements = [
            'admin lppm' => 'Admin Lppm',
            'kepala lppm' => 'Kepala Lppm',
        ];

        $result = str_replace(array_keys($replacements), array_values($replacements), $role);

        return ucwords($result);
    }
}

if (! function_exists('active_has_role')) {
    /**
     * Check if the active role matches the given role.
     */
    function active_has_role(string $role): bool
    {
        $activeRole = active_role();

        return $activeRole === $role;
    }
}

if (! function_exists('active_has_any_role')) {
    /**
     * Check if the active role matches any of the given roles.
     */
    function active_has_any_role(array $roles): bool
    {
        $activeRole = active_role();

        return in_array($activeRole, $roles, true);
    }
}

if (! function_exists('active_has_all_roles')) {
    /**
     * Check if the active role matches all of the given roles.
     */
    function active_has_all_roles(array $roles): bool
    {
        $activeRole = active_role();

        foreach ($roles as $role) {
            if ($activeRole !== $role) {
                return false;
            }
        }

        return true;
    }
}
