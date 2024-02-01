<div>
    <div wire:ignore.self class="modal inmodal" id="modal_mfa" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="form-mfa" wire:submit.prevent="saveAuthenticator" autocomplete="off" method="post">
                <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <h4 class="modal-title">Enabled Two-Factor</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="p-0">
                            <div class="form-group row">
                                <div class="col-md-8">
                                    <fieldset style="">
                                        <h4>Instructions</h4>
                                        <ol style="padding-inline-start: 20px;">
                                            <li>Install one of the following applications on your mobile device:
                                                <ul>
                                                    <li>Free OTP
                                                        <a href="https://play.google.com/store/apps/details?id=org.fedorahosted.freeotp><i class=" fa fa-android"></i> (Android)</a> |
                                                        <a href="https://apps.apple.com/us/app/freeotp-authenticator/id872559395"><i class="fa fa-apple"></i> (iPhone)</a>
                                                    </li>
                                                    <li>Google Authenticator
                                                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"><i class="fa fa-android"></i> (Android)</a> |
                                                        <a href="https://apps.apple.com/us/app/google-authenticator/id388497605"><i class="fa fa-apple"></i> (iPhone)</a>
                                                    </li>
                                                    <li>Microsoft Authenticator
                                                        <a href="https://play.google.com/store/apps/details?id=com.azure.authenticator"><i class="fa fa-android"></i> (Android)</a> |
                                                        <a href="https://apps.apple.com/us/app/microsoft-authenticator/id983156458"><i class="fa fa-apple"></i> (iPhone)</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>Open your two-factor authentication app and add your account by scanning the QR code to the right or by manually entering the Base32 encoded secret <strong>{{$secret_key}}</strong>.</li>
                                            <li>Enter the OTP code from the application and click "Submit" to complete the setup</li>
                                    </fieldset>
                                </div>

                                <div class="col-md-4">
                                    <img src="{{$qr_image}}" alt="">
                                </div>
                            </div>
                            <div class="form-group @error('verification') has-danger @enderror">
                                <label class="font-bold">One-time Code <label class="text-danger">*</label></label>
                                <input wire:model="verification" type="text" class="form-control" required>
                                @error('verification') <div class="form-control-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="font-bold">Device Name <label class="text-danger">*</label></label>
                                <input wire:model.defer="device" type="text" class="form-control" required>
                                @error('device') <span class="error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans('common.cancel')}}</button>
                        <button class="btn btn-info btn-sm" type="submit"><i class="fa fa-check"></i> {{trans('common.submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="p-20 mb-4" wire:ignore>
        @include('layout.partial.flash-error')
        <form wire:submit.prevent="save" method="post">
            <p>This is the application specific multi-factor configuration. When configured, the application specific settings will be used instead of the tenant configuration.</p>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label text-right font-bold">On Login Policy :</label>
                <div class="col-sm-8">
                    <select id="authenticator_policy" wire:model="authenticator_policy" class="select2 form-control d-none">
                        <option value="1">Disabled, OTP challenge will not be required during login (Unsecure)</option>
                        <option value="2">Enabled, OTP challenge must required during every login (Highly Secure)</option>
                    </select>
                </div>
            </div>
            <div class="mb-2">
                <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#modal_mfa"><i class="fa fa-plus"></i> Add MFA</button>
                <button type="submit" class="btn btn-secondary btn-xs"><i class="fa fa-check"></i> Save</button>
            </div>
        </form>
        <div class="table-responsive">
            <table id="table-mfa" class="table" style="width:100% !important">
                <thead>
                    <th>Device</th>
                    <th>Secret</th>
                    <th>Last IP</th>
                    <th>Last Used</th>
                    <th>{{trans('common.actions')}}</th>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    var datatableMfa = $('#table-mfa').DataTable({
        pageLength: 10,
        responsive: true,
        serverSide: true,
        scrollX: true,
        searchDelay: 1000,
        ajax: {
            url: "{{ route('mfa.ajax.otp') }}",
            data: function(data) {
                data.user_id = "{{$user_id}}";
            }
        },
        columns: [
            { data: 'device', name: 'device' },
            { data: 'secret', name: 'secret' },
            { data: 'last_ip', name: 'last_ip' },
            { data: 'last_used', name: 'last_used' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'},
        ],
        order: [[3, 'desc']]
    });

    document.addEventListener('swal', function(event) {
        Swal.fire(event.detail);
    });

    document.addEventListener("livewire:load", function(event) {
        initAuthenticatorSelect();

        window.livewire.on('authenticatorSelect', () => {
            initAuthenticatorSelect();
        });

        window.livewire.on('postSave', () => {
            $('#modal_mfa').trigger('click');
            datatableMfa.draw();
        });

        function initAuthenticatorSelect() {
            $("#authenticator_policy").select2({
                width: '100%'
            }).on('select2:select', function(e) {
                var value = $(this).val();
                var model = $(this).attr('wire:model');
                if (model) {
                    @this.set(model, value);
                }
            });
        }
    });
</script>
@endpush