<?php

namespace App\Http\Controllers\Mobile\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\mobile\AccountService;
use App\Exceptions\common\NotFoundException;
use App\Http\Resources\Mobile\UnitResource; // Import UnitResource
use Exception;

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
}
