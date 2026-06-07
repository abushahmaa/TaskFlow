@component('mail::message')
# 🚨 Task Overdue Notice

Hi **{{ $task->assignee?->name ?? 'Team Member' }}**,

Your task has **passed its deadline** and is now marked as **overdue**. Please take immediate action.

@component('mail::panel')
**Task:** {{ $task->name }}
**Project:** {{ $task->project?->name ?? 'N/A' }}
**Priority:** {{ $task->priority->label() }}
**Deadline Was:** {{ $task->deadline?->format('D, d M Y h:i A') }}
**Current Status:** {{ $task->status->label() }}
@endcomponent

Please update the task status or contact your Project Manager immediately.

@component('mail::button', ['url' => config('app.url'), 'color' => 'red'])
View Task Now
@endcomponent

Thanks,
**{{ config('app.name') }}** Team
@endcomponent
