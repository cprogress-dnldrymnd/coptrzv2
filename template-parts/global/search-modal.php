<!-- Modal -->
<div class="modal fade modal-v2" id="modalSearch" tabindex="-1" aria-labelledby="modalSearchLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content background-white">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSearchLabel">Search</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="/" method="GET">
                <div class="modal-body p-0">
                    <input type="text" class="form-control" id="Search" name="s" placeholder="Enter text here.."
                        required>
                </div>
                <div class="modal-footer">
                    <div class="button-box button-secondary button-small">
                        <button type="button" data-bs-dismiss="modal">Cancel</button>
                    </div>


                    <div class="button-box button-accent button-small">
                        <button type="submit">
                            Search
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>