@extends('layouts.auth')

@section('title', 'OTP')

@section('content')
  <div class="auth-form-light text-left p-5">
    <div class="brand-logo">
      <img src="{{ asset('/images/logo.svg') }}" alt="Logo">
    </div>

    {{-- Cek apakah ada error --}}
    @if(session('success'))
      <div class="alert alert-success mt-2">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger mt-2">
        {{ session('error') }}
      </div>
    @endif

    <h4>Verify your email!</h4>
    <h6 class="font-weight-light">Input the OTP to continue.</h6>

    <!-- OTP Verification Form -->
    <form class="pt-3" method="POST" action="{{ route('verified-otp') }}">
      @csrf
      <div class="form-group">
        <label for="otp">Enter OTP</label>
        <div class="input-group align-items-center">
          <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter 6-digit OTP" maxlength="6"
            pattern="\d{6}" required>
          <div class="input-group-append">
            <button class="btn btn-outline-primary btn-sm d-flex align-items-center" type="button" id="pasteOtp">
              Paste
            </button>
          </div>
        </div>
        @error('otp')
          <span class="text-danger">{{ $message }}</span>
        @enderror
      </div>

      <div class="mt-3 text-center">
        <button type="submit" class="btn btn-primary btn-lg font-weight-medium auth-form-btn px-5"
          style="min-width: 200px;">
          VERIFY OTP
        </button>
      </div>
    </form>

    <div class="text-center mt-4 font-weight-light">
      Didn't receive the code?
      <form method="POST" action="{{ route('send-otp') }}" style="display: inline;" id="resendForm">
        @csrf
        <button type="submit" class="btn btn-link text-primary p-0" id="resendOtp">
          Resend OTP
        </button>
      </form>
    </div>

    <div class="text-center mt-2">
      <small class="text-muted" id="timer"></small>
    </div>
  </div>

  @push('js-page')
    <script>
      // Optional: Auto-paste OTP from clipboard
      document.getElementById('pasteOtp').addEventListener('click', async () => {
        try {
          const text = await navigator.clipboard.readText();
          const otpInput = document.getElementById('otp');
          if (text.match(/^\d{6}$/)) {
            otpInput.value = text;
          } else {
            alert('Clipboard does not contain a valid 6-digit OTP');
          }
        } catch (err) {
          alert('Unable to access clipboard');
        }
      });
    </script>
  @endpush

  @push('style-page')
    <style>
      /* OTP input styling */
      .form-group .input-group {
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .form-group .input-group input[type="text"] {
        letter-spacing: 2px;
        font-size: 1.2rem;
        text-align: center;
        height: 45px;
        flex: 1;
      }

      .form-group .input-group .input-group-append {
        display: flex;
        align-items: center;
      }

      .form-group .input-group .input-group-append .btn {
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        margin-left: 5px;
        border-radius: 4px;
      }

      /* Tombol Verify OTP center horizontal */
      .text-center {
        text-align: center;
      }

      .btn-primary.auth-form-btn {
        display: inline-block;
        margin: 0 auto;
      }

      #timer {
        display: block;
        margin-top: 10px;
      }

      .disabled {
        opacity: 0.5;
        cursor: not-allowed;
      }

      /* Responsive adjustments */
      @media (max-width: 768px) {
        .form-group .input-group {
          flex-direction: column;
          align-items: stretch;
        }

        .form-group .input-group .input-group-append .btn {
          margin-left: 0;
          margin-top: 5px;
          width: 100%;
        }
      }
    </style>
  @endpush
@endsection