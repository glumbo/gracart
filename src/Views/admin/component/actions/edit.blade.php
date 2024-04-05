<a href="{{ $edit_url ?? '#' }}">
    <span title="{{ $edit_text ?? '' }}" type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
        @includeIf($templatePathAdmin.'svgs.edit')
    </span>
</a>
