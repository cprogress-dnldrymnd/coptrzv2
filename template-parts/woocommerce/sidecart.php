<div class="offcanvas offcanvas-end background-white" tabindex="-1" id="offCanvasSideCart"
  aria-labelledby="offCanvasSideCartLabel">
  <div class="offcanvas-header">
    <h5 id="offCanvasSideCartLabel">BASKET</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="widget_shopping_cart_content">
      <?php woocommerce_mini_cart(); ?>
    </div>
  </div>
</div>