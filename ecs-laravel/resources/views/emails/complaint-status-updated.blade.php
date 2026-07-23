<x-mail::message>
# Complaint Status Update

Hello {{ $complaint->user->name }},

The status of your complaint **{{ $complaint->reference_number }}** ("{{ $complaint->title }}") has been updated.

**New Status:** {{ $complaint->status }}

@if ($complaint->admin_remarks)
**Remarks:** {{ $complaint->admin_remarks }}
@endif

<x-mail::button :url="route('complaints.show', $complaint)">
View Complaint
</x-mail::button>

Thank you for helping us improve our community.

Barangay Electronic Complaint System
</x-mail::message>
