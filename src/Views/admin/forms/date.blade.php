@includeIf($templatePathAdmin.'forms.input', ['name' => $name ?? '', 'data' => $product ?? '', 'label' => $label ?? '', 'class' => ($class ?? '').' date_time flatpickr-input', 'prepend' => 'calendar', 'placeholder' => $placeholder ?? 'yyyy-mm-dd', 'info' => $info ?? ''])