@use('App\Models\Ticket')
<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ Ticket::STATUS_BADGES[$status] }}">
    {{ Ticket::STATUS_LABELS[$status] }}
</span>
