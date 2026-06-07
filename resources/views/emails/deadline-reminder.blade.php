@component('mail::message')
# ⏰ Task Deadline Reminder

Hi **{{ $task->assignee?->name ?? 'Team Member' }}**,

This is a reminder that your task is due in **{{ $hoursUntilDeadline }} hour(s)**.

@component('mail::panel')
**Task:** {{ $task->name }}
**Project:** {{ $task->project?->name ?? 'N/A' }}
**Priority:** {{ $task->priority->label() }}
**Status:** {{ $task->status->label() }}
**Deadline:** {{ $task->deadline?->format('D, d M Y h:i A') }}
@endcomponent

Please ensure you complete or update the task status before the deadline.

@component('mail::button', ['url' => config('app.url'), 'color' => 'blue'])
View Task
@endcomponent

Thanks,
**{{ config('app.name') }}** Team
@endcomponent
