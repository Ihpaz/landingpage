<div class="p-20 mb-4">
    <form>
        <p>This is the application specific multi-factor configuration. When configured, the application specific settings will be used instead of the tenant configuration.</p>
        <div class="form-group row">
            <label class="col-sm-2 col-form-label text-right font-bold">On Login Policy :</label>
            <div class="col-sm-8">
                @if($user->authenticator_policy_id == 1)
                <label class="col-form-label">Disabled, OTP challenge will not be required during login (Unsecure)</label>
                @else
                <label class="col-form-label">Enabled, OTP challenge must required during every login (Highly Secure)</label>
                @endif
            </div>
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
                data.user_id = "{{$user->id}}";
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
</script>
@endpush