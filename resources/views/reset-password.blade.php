{{-- resources/views/auth/reset-password.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SubdiRent</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}"> 
    
    <style>
        html, body { height: 100%; background-color: #f8f9fa; }
        body { display: flex; align-items: center; justify-content: center; }
        .forgot-password-container { width: 100%; max-width: 420px; padding: 15px; }
    </style>
</head>
<body>

    <div class="forgot-password-container">
        <div class="modal-content login-modal-content-v4"> 
            <div class="login-body-v4">
                
                <div class="login-logo-group-stacked">
                    <img src="{{ asset('uploads/ddf63450-50d1-4fd2-9994-7a08dd496ac1-removebg-preview.png') }}" alt="Logo" class="login-logo-s-v4">
                    <img src="{{ asset('uploads/1fc18e9c-b6b9-4f39-8462-6e4b7d594471-removebg-preview.png') }}" alt="Subdirent" class="login-logo-text-v4">
                </div>
                
                <h5 class="text-center fw-bold mt-3 mb-2">Set Your New Password</h5>

                <form method="POST" action="#" class="login-form-v4">
                    @csrf
                    
                    {{-- Ang email ay (hypothetically) galing sa URL kaya naka-readonly --}}
                    <div class="mb-3">
                        <input type="email" name="email" id="email" class="form-control" 
                               placeholder="Enter your e-mail" value="user@example.com" required readonly>
                    </div>

                    <div class="mb-3">
                        <input type="password" name="password" id="password" class="form-control" 
                               placeholder="New Password" required>
                    </div>

                    <div class="mb-3">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" 
                               placeholder="Confirm New Password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-login-v4 w-100">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>