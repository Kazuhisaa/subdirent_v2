<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - SubdiRent</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}"> 
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        html, body { height: 100%; }
        
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            background-image: url('{{ asset('uploads/bg1.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            overflow-x: hidden;
        }
        
        .forgot-password-container {
            width: 100%;
            max-width: 420px;
            padding: 15px;
        }

        .login-modal-content-v4 {
            background-color: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            border-radius: 1rem;
        }
        
        .btn-login-v4 {
            border: none;
            color: white;
            padding: 0.9rem;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 0.75rem;
            margin-top: 1rem;
            transition: all 0.3s ease;
            background-color: #3b82f6; 
            box-shadow: 0 4px 15px rgba(13, 59, 102, 0.2);
        }
        .btn-login-v4:hover {
            background-color: #93c5fd; 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(157, 170, 182, 0.3);
        }

        .login-form-v4 .form-control {
             border-radius: 0.5rem;
             padding: 0.9rem;
             text-align: center; /* Center the OTP numbers */
             letter-spacing: 0.5em; /* Add space between numbers for readability */
             font-size: 1.2rem;
             font-weight: 700;
        }
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
                
                <h5 class="text-center fw-bold mt-3 mb-2 color-blue" style="color: #0A2540;">Verify it's you</h5>
                <p class="text-center text-muted small mb-3 px-3">
                    We've sent a 6-digit code to <strong>{{ $email }}</strong>.<br>
                    Please enter it below.
                </p>

                {{-- Display error if OTP is wrong --}}
                @if (session('error'))
                    <div class="alert alert-danger text-center py-2" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.otp.verify.submit') }}" class="login-form-v4">
                    @csrf
                    
                    {{-- Hidden field to pass email along --}}
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="mb-3">
                        <input type="text" name="otp" id="otp" 
                               class="form-control @error('otp') is-invalid @enderror" 
                               placeholder="000000" maxlength="6" autocomplete="off" required>
                        
                        @error('otp')
                            <span class="invalid-feedback text-center" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-login-v4 w-100">Verify & Proceed</button>
                
                    <div class="text-center mt-3">
                        <a href="{{ route('password.request') }}" class="forgot-password-link small text-muted">Didn't receive code? Resend</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>