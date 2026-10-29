@if ($paginator->hasPages())
<nav class="fs-pagination fs-mt-6" aria-label="Pages des analyses">
  @if ($paginator->onFirstPage())
    <span aria-disabled="true">Page précédente</span>
  @else
    <a href="{{ $paginator->previousPageUrl() }}">Page précédente</a>
  @endif
  <span class="fs-legende">Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}</span>
  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}">Page suivante</a>
  @else
    <span aria-disabled="true">Page suivante</span>
  @endif
</nav>
@endif
