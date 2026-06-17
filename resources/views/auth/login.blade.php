@extends('layouts.auth')

@section('content')
    <style>
        /* Form group and input alignments */
        .form-group-custom {
            margin-bottom: 20px;
        }
        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-group-custom i.bi-envelope,
        .input-group-custom i.bi-lock {
            transition: all 0.3s ease;
        }
        .input-group-custom:focus-within i.bi-envelope,
        .input-group-custom:focus-within i.bi-lock {
            color: var(--primary-accent) !important;
            transform: translateY(-50%) scale(1.1);
        }
        
        .form-control-custom {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            background-color: rgba(255, 255, 255, 0.85) !important;
            border: 1.5px solid #e2e8f0 !important; /* Slate 200 */
            color: #1e293b !important;
        }
        
        .form-control-custom:focus {
            transform: translateY(-1px);
            border-color: var(--primary-accent) !important;
            border-width: 1.5px !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-accent) 12%, transparent) !important;
            background-color: #ffffff !important;
        }

        .form-label {
            color: #334155 !important; /* Slate 700 */
        }
        
        /* Links style override */
        .forgot-password-link {
            color: #64748b !important; /* Slate 500 */
            transition: color 0.2s ease;
        }
        .forgot-password-link:hover {
            color: var(--primary-accent) !important;
        }

        /* Checkbox style override */
        .form-check-input {
            border: 1.5px solid #cbd5e1 !important;
            background-color: #ffffff !important;
        }
        .form-check-input:hover {
            border-color: var(--primary-accent) !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.08);
        }

        /* Dynamic submit button layout */
        .btn-primary-custom {
            position: relative;
            overflow: hidden;
            z-index: 1;
            background: var(--primary-accent) !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 14px color-mix(in srgb, var(--primary-accent) 20%, transparent) !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        
        .btn-primary-custom:hover {
            background-color: var(--primary-hover) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.25) !important;
        }

        .btn-primary-custom:hover #submitIcon {
            transform: translateX(4px);
        }

        .btn-primary-custom:active {
            transform: translateY(1px) scale(0.98) !important;
        }

        #submitIcon {
            transition: transform 0.3s ease;
        }

        /* Custom Demo Account box style */
        .demo-box {
            background-color: rgba(59, 130, 246, 0.03) !important;
            border: 1px dashed rgba(59, 130, 246, 0.18) !important;
            border-radius: 10px !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            cursor: pointer;
        }

        .demo-box:hover {
            background-color: rgba(59, 130, 246, 0.08) !important;
            border-style: solid;
            border-color: rgba(59, 130, 246, 0.35) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(148, 163, 184, 0.08);
        }

        .demo-box:active {
            transform: scale(0.98);
        }

        /* Input autofill blink highlight animation */
        @keyframes highlightFill {
            0% { background-color: rgba(59, 130, 246, 0.12); }
            100% { background-color: rgba(255, 255, 255, 0.8); }
        }
        .highlight-input {
            animation: highlightFill 0.8s ease-out;
        }
    </style>

    <form action="{{ route('login') }}" method="POST">
        @csrf
        
        @if ($errors->any())
            <div class="alert-custom">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-3" style="margin-bottom: 18px !important;">
            <label for="email" class="form-label">Alamat Email</label>
            <div class="input-group-custom">
                <input type="email" class="form-control-custom" id="email" name="email" value="{{ old('email', 'dev@bon.com') }}" placeholder="nama@perusahaan.com" required autofocus>
                <i class="bi bi-envelope"></i>
            </div>
        </div>

        <div class="mb-4" style="margin-bottom: 20px !important;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label for="password" class="form-label mb-0">Kata Sandi</label>
                <a href="#" class="text-decoration-none forgot-password-link" style="font-size: 12.5px;">Lupa Password?</a>
            </div>
            <div class="input-group-custom">
                <input type="password" class="form-control-custom" id="password" name="password" placeholder="••••••••" required style="padding-right: 46px;">
                <i class="bi bi-lock"></i>
                <button type="button" id="togglePassword" class="btn border-0 position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); background: none; z-index: 10; color: #94a3b8; padding: 0; display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; transition: all 0.2s;">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
            </div>
        </div>

        <div class="mb-4 d-flex align-items-center" style="margin-bottom: 22px !important;">
            <input type="checkbox" class="form-check-input me-2" id="remember" name="remember" style="margin-top: 0; width: 17px; height: 17px; border-radius: 5px; cursor: pointer;">
            <label style="font-size: 13.5px; cursor: pointer; user-select: none; color: #475569;" for="remember">Ingat saya di perangkat ini</label>
        </div>

        <button type="submit" class="btn-primary-custom" id="submitBtn">
            <span>Masuk ke Akun</span>
            <i class="bi bi-arrow-right" id="submitIcon"></i>
        </button>

        <!-- Demo Account Box -->
        <div class="mt-4 p-3 rounded-3 demo-box" id="demoAccountBox" title="Klik untuk otomatis mengisi kredensial">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-primary" style="font-size: 14px; color: var(--primary-accent) !important;"></i>
                <div style="font-size: 12.5px; color: #64748b;" class="w-100 d-flex flex-wrap align-items-center justify-content-between">
                    <div>
                        <span class="fw-bold text-dark me-2">Autofill Demo:</span>
                        <code class="fw-bold font-monospace px-1.5 py-0.5 rounded" style="background-color: #e2e8f0; color: var(--primary-accent) !important; font-size: 11px;">dev@bon.com</code>
                        <span class="mx-1.5 opacity-30">|</span>
                        <code class="fw-bold font-monospace px-1.5 py-0.5 rounded" style="background-color: #e2e8f0; color: var(--primary-accent) !important; font-size: 11px;">password</code>
                    </div>
                    <span class="badge bg-primary text-white d-none d-sm-inline-block" style="font-size: 10px; background-color: rgba(59, 130, 246, 0.1) !important; color: var(--primary-accent) !important; border: 1px solid rgba(59, 130, 246, 0.2);">Klik di Sini</span>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const togglePasswordIcon = document.getElementById('togglePasswordIcon');
            const form = document.querySelector('form');
            const submitBtn = document.getElementById('submitBtn');
            const demoAccountBox = document.getElementById('demoAccountBox');
            const emailInput = document.getElementById('email');

            // Handle Password Visibility Toggle
            if (togglePassword) {
                togglePassword.addEventListener('click', function(e) {
                    e.preventDefault();
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    if (type === 'text') {
                        togglePasswordIcon.classList.remove('bi-eye');
                        togglePasswordIcon.classList.add('bi-eye-slash');
                    } else {
                        togglePasswordIcon.classList.remove('bi-eye-slash');
                        togglePasswordIcon.classList.add('bi-eye');
                    }
                });
            }

            // Handle Form Submission Loading State
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (submitBtn.disabled) return;
                    
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true" style="width: 14px; height: 14px;"></span>
                        <span>Memverifikasi...</span>
                    `;
                });
            }

            // Handle Click-to-Autofill Demo Credentials
            if (demoAccountBox) {
                demoAccountBox.addEventListener('click', function() {
                    emailInput.value = 'dev@bon.com';
                    passwordInput.value = 'password';
                    
                    // Flash inputs visual feedback
                    emailInput.classList.remove('highlight-input');
                    passwordInput.classList.remove('highlight-input');
                    void emailInput.offsetWidth; // Trigger reflow to restart animation
                    
                    emailInput.classList.add('highlight-input');
                    passwordInput.classList.add('highlight-input');
                    
                    // Cleanup classes after animation finishes
                    setTimeout(() => {
                        emailInput.classList.remove('highlight-input');
                        passwordInput.classList.remove('highlight-input');
                    }, 800);
                });
            }
        });
    </script>
@endsection
