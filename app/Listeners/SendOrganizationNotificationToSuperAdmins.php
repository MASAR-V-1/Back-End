<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\NewOrganizationRegistered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendOrganizationNotificationToSuperAdmins
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        // /** @var \App\Models\User $user */
        $user = $event->user;
        
        // نتأكد إنه هاد المستخدم فعليًا org_admin وعنده مؤسسة مرتبطة
        if ( $user instanceof User &&   $user->isOrgAdmin() && $user->organization) {

            $superAdmins = User::role('super_admin')->get();

            Notification::send(
                $superAdmins,
                new NewOrganizationRegistered($user->organization)
            );
        }
    }
}
