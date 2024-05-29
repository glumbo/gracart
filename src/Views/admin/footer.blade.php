<footer class="main-footer">
  @if (!gc_config('hidden_copyright_footer_admin'))
    <div class="float-end d-none d-sm-inline-block">
      <strong>Env</strong>
      {{ config('app.env') }}
      &nbsp;&nbsp;
      <strong>Version</strong> 
      {{ config('grakan.sub-version') }} ({{ config('grakan.core-sub-version') }})
    </div>
    <strong>Copyright &copy; {{ date('Y') }} <a href="{{ config('grakan.homepage') }}">S-Cart: {{ config('grakan.title') }}</a>.</strong> All rights
    reserved.
  @endif
</footer>
