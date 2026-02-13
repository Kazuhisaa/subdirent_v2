<?php
namespace App\Services\mobile;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Exceptions\common\NotFoundException;

class AccountService
{
  
    public function get(){
        
        /** @var User $user */
        $user = Auth::user();

        // Eager load the tenant and the unit associated with the tenant.
        $user->load('tenant.unit');

        if (!$user->tenant) {
            // Throw a custom exception if the user is not linked to a tenant record.
            throw new NotFoundException('Tenant profile not found for the authenticated user.');
        }

        // Structure the data to match a comprehensive profile response.
        return [
         'tenant_info' =>   [
            'id' => $user->tenant->id,
            'first_name' => $user->tenant->first_name,
            'last_name' => $user->tenant->last_name,
            'email' => $user->email,
            'contact_num' => $user->tenant->contact_num,
            'birth_date' => $user->tenant->birth_date,
            'profile_photo_path' => $user->profile_photo_path,
            ],
            'unit' => $user->tenant->unit,
        ];
    }
}
