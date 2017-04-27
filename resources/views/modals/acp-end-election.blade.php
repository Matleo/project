<div class="modal fade" id="Wahlgang_beenden_Modal" tabindex="-1" role="dialog"
     aria-labelledby="Wahlgang_beenden_Modal_Label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="Wahlgang_beenden_Modal_Label">@lang('content.modal_endel1') </h4>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <p>
                        @lang('content.modal_endel2')
                    </p>
                    <p>
                        @lang('content.modal_endel3')
                    </p>
                    <p><a href="/Ergebnisse.html" download>@lang('fields.downloadResults')</a></p>
                    <p>

                    </p>
                    <form>
                        @lang('content.modal_endel5'): <input type="password" required>
                        <button type="submit" class="btn btn-danger">@lang('content.modal_endel6')</button>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('fields.cancel')</button>
            </div>
        </div>
    </div>
</div>