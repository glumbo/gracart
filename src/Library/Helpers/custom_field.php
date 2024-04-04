<?php
use Glumbo\Gracart\Front\Models\ShopCustomField;
use Glumbo\Gracart\Front\Models\ShopCustomFieldDetail;
use Glumbo\Gracart\Admin\Controllers\AdminCustomFieldController;
/**
 * Update custom field
 */
if (!function_exists('gc_update_custom_field') && !in_array('gc_update_custom_field', config('helper_except', []))) {
    function gc_update_custom_field(array $fields = [], string $itemId, string $type)
    {
        $arrFields = array_keys((new AdminCustomFieldController)->fieldTypes());
        if (in_array($type, $arrFields) && !empty($fields)) {
            (new ShopCustomFieldDetail)
                ->join(GC_DB_PREFIX.'shop_custom_field', GC_DB_PREFIX.'shop_custom_field.id', GC_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
                ->where(GC_DB_PREFIX.'shop_custom_field_detail.rel_id', $itemId)
                ->where(GC_DB_PREFIX.'shop_custom_field.type', $type)
                ->delete();

            $dataField = [];
            foreach ($fields as $key => $value) {
                $field = (new ShopCustomField)->where('code', $key)->where('type', $type)->first();
                if ($field) {
                    $dataField = gc_clean([
                        'custom_field_id' => $field->id,
                        'rel_id' => $itemId,
                        'text' => is_array($value) ? implode(',', $value) : trim($value),
                    ], [], true);
                    (new ShopCustomFieldDetail)->create($dataField);
                }
            }
        }
    }
}
