<span onclick="deleteItem('{{ $delete_id }}');"  title="{{ $delete_text ?? '' }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm">
    @includeIf($templatePathAdmin.'svgs.delete')
</span>