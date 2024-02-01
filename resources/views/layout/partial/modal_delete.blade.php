<div class="modal inmodal" id="modal_delete" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated fadeIn">
            <div class="modal-header">
                <h4 id="modal_delete_title" class="modal-title">Hapus Data</h4>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{trans('common.close')}}</span></button>
            </div>
            <form id="modal_delete_form" method="post">
            <div id="modal_delete_body" class="modal-body">
                <p>Apakah anda ingin menghapus data ini? Tindakan ini tidak dapat <strong>dibatalkan</strong> dan data yang terhapus <strong>tidak dapat</strong> dipulihkan.</p>
                <input type="hidden" name="_method" value="DELETE">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-white" data-dismiss="modal">{{trans('common.close')}}</button>
                <button type="submit" class="btn btn-sm btn-danger">Yes, hapus!</button>
            </div>
            </form>
        </div>
    </div>
</div>