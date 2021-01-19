

Increase product variation limit in woocommerce

```
function wpse_rest_batch_items_limit( $limit ) {
    $limit = 200;

    return $limit;
}
add_filter( 'woocommerce_rest_batch_items_limit', 'wpse_rest_batch_items_limit' );```