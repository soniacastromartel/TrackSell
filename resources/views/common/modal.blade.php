<link rel="stylesheet" href="{{ asset('/css/modal.css') }}">


<div class="modal" tabindex="-1" role="dialog" id="modal-validate">
    <input type="hidden" id="id" />
    <input type="hidden" id="validateVal" />
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p id="message-validation" class="px-4 text-center"  style= "margin-bottom: 20px;"></p>
            </div>
            <div class="modal-footer center">
                <div id="btnConfirmRequest"  class="btn-yes">SI</div>
                <div id="btnCancelRequest" class="btn-no" data-dismiss="modal">NO</div>
                </p>
            </div>
        </div>
    </div>
</div>