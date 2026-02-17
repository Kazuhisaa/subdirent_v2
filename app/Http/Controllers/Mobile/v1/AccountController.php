<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\mobile\AccountService;
use App\Exceptions\common\NotFoundException;
use App\Http\Resources\Mobile\UnitResource; // Import UnitResource
use App\Http\Requests\Mobile\Account\UpdateProfileRequest; // Import UpdateProfileRequest
use App\Http\Requests\Mobile\Account\UpdateCredentialsRequest; // Import UpdateCredentialsRequest
use Exception;
use App\Exceptions\Auth\InvalidCredentialsException;
// Removed: use Illuminate\Support\Facades\Validator; // No longer needed

class AccountController extends Controller
{
    
   protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function show(){
    
        try {
            $profileData = $this->accountService->get();
            
            return response()->json([
                'success' => true,
                'message' => 'User profile and unit data retrieved successfully.',
                'data' => [
                    'tenant_info' => $profileData['tenant_info'],
                    'unit' => new UnitResource($profileData['unit']) // Apply UnitResource here
                ]
            ]);
        } catch(InvalidCredentialsException $e){
              return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        } catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfile(UpdateProfileRequest $request) // Changed type hint to UpdateProfileRequest
    {
        // Removed manual validation and error handling as FormRequest handles it
        try {
            $this->accountService->updateProfile($request->validated()); // Use validated() method
            
            return response()->json([
                'success' => true,
                'message' => 'Account updated successfully.',
            ]);
        } catch (NotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch(Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function updatePicture(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $path = $this->accountService->updatePicture($request->file('avatar'));

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully.',
                'data' => [
                    'profile_photo_path' => $path
                ]
            ]);
        } catch (NotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function updateCredentials(UpdateCredentialsRequest $request){
        try {
            $this->accountService->updateCredentials($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Login credentials updated successfully.',
            ]);
        } catch (NotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
