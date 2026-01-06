<dl class="fi-infolist-grid" style="display: grid; gap: 1rem;">
    @foreach ($headers as $header => $values)
        <div class="fi-infolist-entry-wrapper">
            <dt class="fi-infolist-entry-label" style="font-size: 0.875rem; font-weight: 500; margin-bottom: 0.25rem;">
                {{ $header }}
            </dt>
            <dd class="fi-infolist-entry-content" style="font-size: 0.875rem; opacity: 0.7; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; word-break: break-all;">
                @if (is_array($values))
                    {{ implode(', ', $values) }}
                @else
                    {{ $values }}
                @endif
            </dd>
        </div>
    @endforeach
</dl>
