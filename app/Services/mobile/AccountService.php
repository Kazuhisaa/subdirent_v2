<?php
namespace App\Services\mobile;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Exceptions\common\NotFoundException;


use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
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

    public function updateProfile(array $data)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->tenant) {
            throw new NotFoundException('Tenant profile not found for the authenticated user.');
        }

        $user->tenant->update($data);

        return $user->tenant;
    }



    public function updatePicture(UploadedFile $file)
    {
        /** @var User $user */
        $user = Auth::user();
        $tenant = $user->tenant;

        if (!$tenant) {
            throw new NotFoundException('Tenant not found.');
        }

        $uploadPath = public_path('uploads/tenants/');
        
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $filename = 'tenant-' . $tenant->id . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/tenants/' . $filename;

        $file->move($uploadPath, $filename);

        if ($user->profile_photo_path && File::exists(public_path($user->profile_photo_path))) {
            File::delete(public_path($user->profile_photo_path));
        }

        $user->profile_photo_path = $path;
        if (!$user->save()) {
            throw new \Exception('Could not save user');
        }

        return $path;
    }


    public function updateCredentials(array $data){
          /** @var User $user */
          $user = Auth::user();

          if (!$user) {
              throw new NotFoundException('User not found.');
          }
  
          $user->password = Hash::make($data['new_password']);
          $user->save();
  
          return $user;
    }
}
