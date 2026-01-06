<div class="fi-infolist-entry-wrapper">
    <textarea
        readonly
        rows="20"
        class="fi-input block w-full rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500"
        style="resize: vertical; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;"
    >{{ $responseBody }}</textarea>

    <div style="margin-top: 0.5rem; font-size: 0.75rem; line-height: 1rem; opacity: 0.7;">
        {{ strlen($responseBody) }} characters
    </div>
</div>
