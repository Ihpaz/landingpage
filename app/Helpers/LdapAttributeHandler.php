<?php

namespace App\Helpers;

use App\Models\User as EloquentUser;
use Adldap\Models\User as LdapUser;

class LdapAttributeHandler
{
    /**
     * Synchronizes ldap attributes to the specified model.
     *
     * @param LdapUser     $ldapUser
     * @param EloquentUser $eloquentUser
     *
     * @return void
     */
    public function handle(LdapUser $user_ad, EloquentUser $user)
    {
        $nip = preg_match('/^[0-9]{7,9}([A-Z]{0,3})/', $user_ad->getFirstAttribute('description'));
        $user->email = strtolower($user_ad->getFirstAttribute('mail'));
        $user->fullname = $user_ad->getFirstAttribute('displayname');
        $user->nip = $nip ? $user_ad->getFirstAttribute('description') : null;
        $user->thumbnail_photo = base64_encode($user_ad->getFirstAttribute('thumbnailphoto'));
        $user->phonenumber = $user_ad->getFirstAttribute('phonenumber');
        $user->position = $user_ad->getFirstAttribute('title');
        $user->company = $user_ad->getFirstAttribute('company');
        $user->department = $user_ad->getFirstAttribute('department');
    }
}
