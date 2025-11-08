<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    {{-- Itinakda ang sukat na gagayahin sa image --}}
    <div class="modal-dialog modal-dialog-centered"> 
        
        {{-- Gagamit tayo ng bagong class para sa styling --}}
        <div class="modal-content login-modal-content-v4"> 
    
            {{-- 'X' Button --}}
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            <div class="login-body-v4">
                
                <div class="login-logo-group-stacked">
                    {{-- Palitan mo 'to ng tamang path --}}
                    <img src="{{ asset('uploads/ddf63450-50d1-4fd2-9994-7a08dd496ac1-removebg-preview.png') }}" alt="Logo" class="login-logo-s-v4">
                    {{-- Palitan mo 'to ng tamang path --}}
                    <img src="{{ asset('uploads/1fc18e9c-b6b9-4f39-8462-6e4b7d594471-removebg-preview.png') }}" alt="Subdirent" class="login-logo-text-v4">
                </div>
                
                <form method="POST" action="{{ route('login.submit') }}" class="login-form-v4">
                    @csrf
                    
                    <div class="mb-3">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your e-mail" required>
                    </div>

                    <div class="mb-3" style="position: relative;">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                        {{-- Inilagay ang icon sa loob --}}
                        <span class="password-toggle-icon" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                    
                    <button type="submit" class="btn btn-login-v4 w-100">LOG IN</button>
                    <div class="text-center mt-3">
   <a href="{{ route('password.request') }}" class="forgot-password-link">Forgot Password?</a>
</div>
                
                </form>
            </div>

        </div>
    </div>
</div>

{{-- Ito ay 'di nagbago --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
{{-- Siguraduhin na ito ang CSS file na ine-edit mo --}}
<link rel="stylesheet" href="{{ asset('css/login.css') }}"> 

<script>
// Walang pagbabago sa script mo
document.addEventListener('DOMContentLoaded', () => {
  const passwordInput = document.getElementById('password');
  const toggleBtn = document.getElementById('togglePassword');
  
  if (toggleBtn) {
    const icon = toggleBtn.querySelector('i');
    toggleBtn.addEventListener('click', () => {
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      }
    });
  }
});
</script>
{{-- ❗️ BAGO: 1. I-load ang SweetAlert2 Library --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ❗️ BAGO: 2. I-load ang iyong custom alerts.js file --}}
<script src="{{ asset('js/alerts.js') }}"></script> 

{{-- 3. Iyong JavaScript (Pinagsama na) --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    // --- Iyong Password Toggle Script (Walang pagbabago) ---
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePassword');
    
    if (toggleBtn) {
        const icon = toggleBtn.querySelector('i');
        toggleBtn.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    }

    // --- ❗️ BAGO: SweetAlert Triggers ---
    // Titingnan nito kung may "error" message mula sa Laravel session
    // at ipapakita ang iyong showError() function.
    @if(session('error'))
        showError("{{ session('error') }}", "Login Failed");
    @endif

    // Para rin sa success messages, kung kailangan
    @if(session('success'))
        showSuccess("{{ session('success') }}", "Success!");
    @endif

});
</script>