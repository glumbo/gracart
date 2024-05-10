@if (gc_check_multi_store_installed())
    {{-- select shop_store --}}
    @php
        $listStore = [];
        if (function_exists('gc_get_list_store_of_category_detail')) {
                $oldData = gc_get_list_store_of_category_detail($category['id'] ?? '');
            } else {
                $oldData = null;
            }
        $shop_store = old('shop_store', $oldData);

        if(is_array($shop_store)){
            foreach($shop_store as $value){
                $listStore[] = $value;
            }
        }
    @endphp

    @includeIf($templatePathAdmin.'forms.select', ['name' => 'shop_store[]', 'options' => gc_get_list_code_store(), 'label' => gc_language_render('admin.select_store'), 'multiple' => 1 ])
@endif