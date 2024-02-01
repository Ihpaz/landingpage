<?php

namespace App\Http\Livewire\Mfa;

use App\Helpers\ActivityLog;
use App\Models\User;
use App\Models\User\Authenticator;
use App\Traits\LivewireAlert;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use PragmaRX\Google2FAQRCode\Google2FA;

class TotpAuthenticator extends Component
{
    use LivewireAlert;

    public $user_id;
    public $user_email;
    public $authenticator_policy;
    public $secret_key;
    public $qr_image;
    public $verification;
    public $device;

    public function mount(User $user)
    {
        if ($user) {
            $this->user_id = $user->id;
            $this->user_email = $user->email;
            $this->authenticator_policy = $user->authenticator_policy_id;
            $this->generateQr();
        }
    }

    protected function generateQr()
    {
        $google2fa = new Google2FA();
        $google2fa->setQrcodeService(
            new \PragmaRX\Google2FAQRCode\QRCode\Bacon(
                new \BaconQrCode\Renderer\Image\ImagickImageBackEnd()
            )
        );
        $this->secret_key = $google2fa->generateSecretKey(32);
        $this->qr_image = $google2fa->getQRCodeInline(config('app.name'), $this->user_email, $this->secret_key);
    }

    public function hydrate()
    {
        $this->emit('authenticatorSelect');
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function saveAuthenticator()
    {
        $this->validate([
            'secret_key' => 'required',
            'verification' => 'required',
            'device' => 'required'
        ]);

        try {
            $google2fa = new Google2FA();
            if ($google2fa->verifyKey($this->secret_key, $this->verification)) {
                $mfa = new Authenticator();
                $mfa->user_id = $this->user_id;
                $mfa->device = $this->device;
                $mfa->secret = $this->secret_key;
                $mfa->save();

                // Reset value
                $this->verification = null;
                $this->device = null;
                $this->generateQr();

                $this->emit('postSave');
                return $this->success(trans('common.success'), 'Multi Factor berhasil ditambahkan');
            }
            $this->verification = null;
            $this->generateQr();
            return $this->addError('verification', 'Kode verifikasi tidak sesuai');
        } catch (\Exception $e) {
            $this->emit('postSave');
            ActivityLog::sentry($e);
            return $this->error($e->getMessage());
        }
    }

    public function save()
    {
        try {
            $user = User::findOrFail($this->user_id);
            $user->authenticator_policy_id = $this->authenticator_policy;
            $user->save();

            return $this->success(trans('common.success'), 'Policy berhasil disimpan');
        } catch (\Exception $e) {
            ActivityLog::sentry($e);
            return $this->error($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.mfa.totp-authenticator');
    }
}
